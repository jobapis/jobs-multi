<?php namespace JobApis\Jobs\Client;

use JobApis\Jobs\Client\Providers\CareerbuilderProvider;
use JobApis\Jobs\Client\Queries\AbstractQuery;

class JobsMulti
{
    /**
     * Job board API query objects
     *
     * @var AbstractQuery
     */
    protected $queries = [];

    /**
     * Creates query objects for each provider and creates this unified client.
     *
     * @param array $providers
     */
    public function __construct($providers = [])
    {
        foreach ($providers as $provider => $options) {
            $query = $provider.'Query';
            $className = 'JobApis\\Jobs\\Client\\Queries\\'.$query;
            $this->addQuery(lcfirst($query), $className, $options);
        }
    }

    /**
     * Instantiates a Query object and adds it to the queries array
     *
     * @param $key Query name
     * @param $className Query class name
     * @param array $options Array of parameters to add to constructor
     */
    public function addQuery($key, $className, $options = [])
    {
        $this->queries[$key] = new $className($options);
        return $this;
    }

    /**
     * Gets jobs from Careerbuilder and hydrates a new jobs collection
     *
     * @return \JobApis\Jobs\Client\Collection
     */
    public function getCareerbuilderJobs()
    {
        $provider = new CareerbuilderProvider($this->queries['careerbuilderQuery']);
        return $provider->getJobs();
    }

    /**
     * Gets jobs from Dice and hydrates a new jobs collection
     *
     * @return \JobApis\Jobs\Client\Collection
     */
    public function getDiceJobs()
    {

    }

    /**
     * Gets jobs from the Government Jobs API and hydrates a new jobs collection
     *
     * @return \JobApis\Jobs\Client\Collection
     */
    public function getGovtJobs()
    {

    }

    /**
     * Gets jobs from Indeed and hydrates a new jobs collection
     *
     * @return \JobApis\Jobs\Client\Collection
     */
    public function getIndeedJobs()
    {

    }

    /**
     * Sets a keyword on the query for each provider
     *
     * @param $keyword
     *
     * @return $this
     */
    public function setKeyword($keyword)
    {
        foreach ($this->queries as $classKey => $query) {
            $className = ucfirst($classKey);

            switch ($className) {
                case 'CareerbuilderQuery':
                    $query->set('Keywords', $keyword);
                    break;
                case 'DiceQuery':
                    $query->set('text', $keyword);
                    break;
                case 'GovtQuery':
                    $query->set('query', $keyword);
                    break;
                case 'GithubQuery':
                    $query->set('search', $keyword);
                    break;
                case 'IndeedQuery':
                    $query->set('q', $keyword);
                    break;
                default:
                    throw new \Exception("Provider {$className} not found");
            }
        }
        return $this;
    }

    /**
     * Sets a location on the query for each provider
     *
     * @param $location
     *
     * @return $this
     */
    public function setLocation($location)
    {
        if (!$this->isValidLocation($location)) {
            throw new \OutOfRangeException("Location parameter must follow the pattern 'City, ST'.");
        }

        $locationArr = explode(', ', $location);
        $city = $locationArr[0];
        $state = $locationArr[1];

        foreach ($this->queries as $classKey => $query) {
            $className = ucfirst($classKey);

            switch ($className) {
                case 'CareerbuilderQuery':
                    $query->set('UseFacets', 'true');
                    $query->set('FacetCityState', $location);
                    break;
                case 'DiceQuery':
                    $query->set('city', $city);
                    $query->set('state', $state);
                    break;
                case 'GovtQuery':
                    $queryString = $query->get('query').' in '.$location;
                    $query->set('query', $queryString);
                    break;
                case 'GithubQuery':
                    $query->set('location', $location);
                    break;
                case 'IndeedQuery':
                    $query->set('l', $location);
                    break;
                default:
                    throw new \Exception("Provider {$className} not found");
            }
        }
        return $this;
    }

    public function setPage($page)
    {
        return $this;
    }

    public function setPerPage($perPage)
    {
        return $this;
    }

    /**
     * Tests whether location string follows valid convention (City, ST)
     *
     * @param string $location
     *
     * @return bool
     */
    private function isValidLocation($location = null)
    {
        preg_match("/([^,]+),\s*(\w{2})/", $location, $matches);
        return ($matches[1] && $matches[2]) ? true : false;
    }
}
