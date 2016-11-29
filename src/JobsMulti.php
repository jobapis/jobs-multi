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
            $jobs[$providerName] = $this->getJobsByProvider($providerName);
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
    public function getJobsByProvider($name = null)
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
     * Gets the options array based on the provider name.
     *
     * @param $name
     *
     * @return array
     */
    protected function getOptionsForProvider($name)
    {
        switch ($name) {
            case 'Careerbuilder':
                return [
                    'Keywords' => $this->keyword,
                    'Location' => $this->location,
                ];
                break;
            case 'Careercast':
                return [
                    'keyword' => $this->keyword,
                    'location' => $this->location,
                ];
                break;
            case 'Careerjet':
                return [
                    'keywords' => $this->keyword,
                    'location' => $this->location,
                ];
                break;
            case 'Dice':
                // Break down location by city and state
                $locationArr = explode(', ', $this->location);
                return [
                    'text' => $this->keyword,
                    'city' => $locationArr[0],
                    'state' => $locationArr[1],
                ];
                break;
            case 'Github':
                return [
                    'search' => $this->keyword,
                    'location' => $this->location,
                ];
                break;
            case 'Govt':
                // Create a query string with keyword and location
                $queryString = $this->keyword.' in '.$this->location;
                return [
                    'query' => $queryString,
                ];
                break;
            case 'Ieee':
                return [
                    'keyword' => $this->keyword,
                    'location' => $this->location,
                ];
                break;
            case 'Indeed':
                return [
                    'q' => $this->keyword,
                    'l' => $this->location,
                ];
                break;
            case 'Jobinventory':
                return [
                    'q' => $this->keyword,
                    'l' => $this->location,
                ];
                break;
            case 'Juju':
                return [
                    'k' => $this->keyword,
                    'l' => $this->location,
                ];
                break;
            case 'Stackoverflow':
                return [
                    'q' => $this->keyword,
                    'l' => $this->location,
                ];
                break;
            case 'Usajobs':
                return [
                    'Keyword' => $this->keyword,
                    'LocationName' => $this->location,
                ];
                break;
            case 'Ziprecruiter':
                return [
                    'search' => $this->keyword,
                    'location' => $this->location,
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
            $this->getOptionsForProvider($name)
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
}
