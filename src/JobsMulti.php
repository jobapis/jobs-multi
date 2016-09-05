<?php namespace JobApis\Jobs\Client;

use JobApis\Jobs\Client\Providers\CareerbuilderProvider;
use JobApis\Jobs\Client\Queries\CareerbuilderQuery;
use JobApis\Jobs\Client\Queries\DiceQuery;
use JobApis\Jobs\Client\Queries\GovtQuery;
use JobApis\Jobs\Client\Queries\IndeedQuery;
use JobApis\Jobs\Client\Collection;

class JobsMulti
{
    /**
     * Careerbuilder query object
     *
     * @var CareerbuilderQuery
     */
    protected $careerbuilderQuery;

    /**
     * Dice query object
     *
     * @var DiceQuery
     */
    protected $diceQuery;

    /**
     * Government jobs query object
     *
     * @var GovtQuery
     */
    protected $govtQuery;

    /**
     * Indeed query object
     *
     * @var IndeedQuery
     */
    protected $indeedQuery;

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
            $this->{lcfirst($query)} = new $className($options);
        }
    }

    /**
     * Gets jobs from Careerbuilder and hydrates a new jobs collection
     *
     * @var Collection
     */
    public function getCareerbuilderJobs()
    {
        $provider = new CareerbuilderProvider($this->careerbuilderQuery);
        return $provider->getJobs();
    }

    public function getDiceJobs()
    {

    }

    public function getGovtJobs()
    {

    }

    public function getIndeedJobs()
    {

    }

    public function setKeyword($keyword)
    {

    }

    public function setLocation($location)
    {

    }

    public function setPage($page)
    {

    }

    public function setPerPage($perPage)
    {

    }
}
