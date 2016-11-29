<?php namespace JobApis\Jobs\Client;

use JobApis\Jobs\Client\Providers\AbstractProvider;
use JobApis\Jobs\Client\Queries\AbstractQuery;

class JobsMulti
{
    /**
     * Search keyword
     *
     * @var string
     */
    protected $keyword;

    /**
     * Search location
     *
     * @var string
     */
    protected $location;

    /**
     * Results page number
     *
     * @var integer
     */
    protected $pageNumber;

    /**
     * Results per page
     *
     * @var integer
     */
    protected $perPage;

    /**
     * Job board API providers
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Job board API query objects
     *
     * @var array
     */
    protected $queries = [];

    /**
     * Creates query objects for each provider and creates this unified client.
     *
     * @param array $providers
     */
    public function __construct($providers = [])
    {
        $this->setProviders($providers);
    }

    /**
     * Gets jobs from all providers in a single go and returns an array of Collection objects.
     *
     * @return array
     */
    public function getAllJobs($options = [])
    {
        $jobs = [];
        foreach ($this->providers as $providerName => $options) {
            $jobs[$providerName] = $this->getJobsByProvider($providerName, $options);
        }
        return $jobs;
    }

    /**
     * Gets jobs from a single provider and hydrates a new jobs collection.
     *
     * @var $name string Provider name.
     *
     * @return \JobApis\Jobs\Client\Collection
     */
    public function getJobsByProvider($name = null, $options = [])
    {
        try {
            // Instantiate the query with all our parameters
            $query = $this->instantiateQuery($name);

            // Instantiate the provider
            $provider = $this->instantiateProvider($name, $query);

            // Get the jobs and return a collection
            return $provider->getJobs();
        } catch (\Exception $e) {
            return (new Collection())->addError($e->getMessage());
        }
    }

    /**
     * Sets a keyword on the query.
     *
     * @param $keyword string
     *
     * @return $this
     */
    public function setKeyword($keyword = null)
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * Sets a location on the query for each provider.
     *
     * @param $location
     *
     * @return $this
     */
    public function setLocation($location = null)
    {
        if (!$this->isValidLocation($location)) {
            throw new \OutOfBoundsException("Location parameter must follow the pattern 'City, ST'.");
        }
        $this->location = $location;

        return $this;
    }

    /**
     * Sets a page number and number of results per page for each provider.
     *
     * @param $pageNumber integer
     * @param $perPage integer
     *
     * @return $this
     */
    public function setPage($pageNumber = 1, $perPage = 10)
    {
        $this->pageNumber = $pageNumber;
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Sets an array of providers.
     *
     * @param $providers array
     *
     * @return $this
     */
    public function setProviders($providers = [])
    {
        $this->providers = $providers;

        return $this;
    }

    /**
     * Gets an array of options from a translator array
     *
     * @param array $translator
     *
     * @return array
     */
    protected function getOptionsFromTranslator($translator = [])
    {
        $options = [];
        foreach($translator as $standardKey => $providerKey) {
            if (method_exists($this, $providerKey)) {
                $this->$providerKey($this->{$standardKey});
            } else {
                $options[$providerKey] = $this->{$standardKey};
            }
        }
        return $options;
    }

    /**
     * Gets the options array based on the provider name.
     *
     * @param $name
     *
     * @return array
     */
    protected function getTranslatorForProvider($name)
    {
        switch ($name) {
            case 'Careerbuilder':
                return [
                    'keyword' => 'Keywords',
                    'location' => 'Location',
                ];
                break;
            case 'Careercast':
                return [
                    'keyword' => 'keyword',
                    'location' => 'location',
                ];
                break;
            case 'Careerjet':
                return [
                    'keyword' => 'keywords',
                    'location' => 'location',
                ];
                break;
            case 'Dice':
                return [
                    'keyword' => 'text',
                    'location' => 'getCityAndState',
                ];
                break;
            case 'Github':
                return [
                    'keyword' => 'search',
                    'location' => 'location',
                ];
                break;
            case 'Govt':
                return [
                    'keyword' => 'getQueryWithKeywordAndLocation',
                ];
                break;
            case 'Ieee':
                return [
                    'keyword' => 'keyword',
                    'location' => 'location',
                ];
                break;
            case 'Indeed':
                return [
                    'keyword' => 'q',
                    'location' => 'l',
                ];
                break;
            case 'Jobinventory':
                return [
                    'keyword' => 'q',
                    'location' => 'l',
                ];
                break;
            case 'Juju':
                return [
                    'keyword' => 'k',
                    'location' => 'l',
                ];
                break;
            case 'Stackoverflow':
                return [
                    'keyword' => 'q',
                    'location' => 'l',
                ];
                break;
            case 'Usajobs':
                return [
                    'keyword' => 'Keyword',
                    'location' => 'LocationName',
                ];
                break;
            case 'Ziprecruiter':
                return [
                    'keyword' => 'search',
                    'location' => 'location',
                ];
                break;
            default:
                throw new \Exception("Provider {$name} not found");
        }
    }

    /**
     * Instantiates a provider using a query object.
     *
     * @param null $name
     * @param AbstractQuery $query
     *
     * @return AbstractProvider
     */
    protected function instantiateProvider($name, AbstractQuery $query)
    {
        $path = 'JobApis\\Jobs\\Client\\Providers\\' . $name . 'Provider';

        return new $path($query);
    }

    /**
     * Instantiates a query using a client name.
     *
     * @param null $name
     *
     * @return AbstractQuery
     */
    protected function instantiateQuery($name)
    {
        $path = 'JobApis\\Jobs\\Client\\Queries\\' . $name . 'Query';

        $options = array_merge(
            $this->providers[$name],
            $this->getOptionsFromTranslator($this->getTranslatorForProvider($name))
        );

        return new $path($options);
    }

    /**
     * Gets a start from count for APIs that use per_page and start_from pattern.
     *
     * @param int $page
     * @param int $perPage
     *
     * @return int
     */
    private function getStartFrom($page = 1, $perPage = 10)
    {
        return ($page * $perPage) - $perPage;
    }

    /**
     * Tests whether location string follows valid convention (City, ST).
     *
     * @param string $location
     *
     * @return bool
     */
    private function isValidLocation($location = null)
    {
        preg_match("/([^,]+),\s*(\w{2})/", $location, $matches);
        return isset($matches[1]) && isset($matches[2]) ? true : false;
    }

    /**
     * Tests whether the method is a valid get<Provider>Jobs() method.
     *
     * @param $method
     *
     * @return bool
     */
    private function isGetJobsByProviderMethod($method)
    {
        return preg_match('/(get)(.*?)(Jobs)/', $method, $matches) && $matches[2] && isset($this->queries[$matches[2]]);
    }

    /**
     * Get the provider name from the method.
     *
     * @param $method
     *
     * @return string
     */
    private function getProviderFromMethod($method)
    {
        preg_match('/(get)(.*?)(Jobs)/', $method, $matches);
        return $matches[2];
    }

    /**
     * Get the city and state as an array from a location string.
     *
     * @return array
     */
    private function getCityAndState()
    {
        if ($this->location) {
            $locationArr = explode(', ', $this->location);
            return [
                'city' => $locationArr[0],
                'state' => $locationArr[1],
            ];
        }
        return [];
    }

    /**
     * Get the query with keyword and location.
     *
     * @return array
     */
    private function getQueryWithKeywordAndLocation()
    {
        $queryString = $this->keyword;

        if ($this->location) {
            $queryString .= ' in '.$this->location;
        }

        return [
            'query' => $queryString,
        ];
    }
}
