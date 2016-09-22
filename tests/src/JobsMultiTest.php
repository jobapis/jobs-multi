<?php namespace JobApis\Jobs\Client\Tests;

use Mockery as m;
use JobApis\Jobs\Client\JobsMulti;

class JobsMultiTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->providers = [
            'Careerbuilder' => [
                'DeveloperKey' => uniqid(),
            ],
            'Dice' => [],
            'Govt' => [],
            'Github' => [],
            'Indeed' => [
                'publisher' => uniqid(),
            ],
            'Usajobs' => [
                'AuthorizationKey' => uniqid(),
            ],
            'Careercast' => [],
        ];
        $this->client = new JobsMulti($this->providers);
    }

    public function testItCanInstantiateQueryObjectsWithAllProviders()
    {
        $queries = $this->getProtectedProperty($this->client, 'queries');

        foreach ($this->providers as $key => $provider) {
            $this->assertTrue(isset($queries[$key]));
            $this->assertEquals(
                'JobApis\\Jobs\\Client\\Queries\\'.$key.'Query',
                get_class($queries[$key])
            );
        }
    }

    public function testItCanInstantiateQueryObjectsWithoutAllProviders()
    {
        $providers = [
            'Dice' => [],
            'Govt' => [],
            'Github' => [],
        ];
        $client = new JobsMulti($providers);
        $queries = $this->getProtectedProperty($client, 'queries');

        foreach ($providers as $key => $provider) {
            $this->assertTrue(isset($queries[$key]));
            $this->assertEquals(
                'JobApis\\Jobs\\Client\\Queries\\'.$key.'Query',
                get_class($queries[$key])
            );
        }
        $this->assertFalse(isset($queries['careerbuilderQuery']));
        $this->assertFalse(isset($queries['indeedQuery']));
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testItThrowsErrorOnInvalidMethodCall()
    {
        $method = uniqid();
        $this->client->$method();
    }

    public function testItCanSetKeywordOnAllProviders()
    {
        $keyword = uniqid();
        $result = $this->client->setKeyword($keyword);

        $this->assertEquals(get_class($this->client), get_class($result));

        $queries = $this->getProtectedProperty($this->client, 'queries');

        foreach ($this->providers as $key => $provider) {
            switch ($key) {
                case 'Careerbuilder':
                    $this->assertEquals($keyword, $queries[$key]->get('Keywords'));
                break;
                case 'Dice':
                    $this->assertEquals($keyword, $queries[$key]->get('text'));
                break;
                case 'Govt':
                    $this->assertEquals($keyword, $queries[$key]->get('query'));
                break;
                case 'Github':
                    $this->assertEquals($keyword, $queries[$key]->get('search'));
                    break;
                case 'Indeed':
                    $this->assertEquals($keyword, $queries[$key]->get('q'));
                    break;
                case 'Usajobs':
                    $this->assertEquals($keyword, $queries[$key]->get('Keyword'));
                    break;
                case 'Careercast':
                    $this->assertEquals($keyword, $queries[$key]->get('keyword'));
                    break;
                default:
                    throw new \Exception("Provider {$key} not found in test.");
            }
        }
    }

    public function testItCanSetLocationOnAllProviders()
    {
        $city = uniqid();
        $state = 'te';
        $location = $city.', '.$state;
        $result = $this->client->setLocation($location);

        $this->assertEquals(get_class($this->client), get_class($result));

        $queries = $this->getProtectedProperty($this->client, 'queries');

        foreach ($this->providers as $key => $provider) {
            switch ($key) {
                case 'Careerbuilder':
                    $this->assertEquals('true', $queries[$key]->get('UseFacets'));
                    $this->assertEquals($location, $queries[$key]->get('FacetCityState'));
                    break;
                case 'Dice':
                    $this->assertEquals($city, $queries[$key]->get('city'));
                    $this->assertEquals($state, $queries[$key]->get('state'));
                    break;
                case 'Govt':
                    $this->assertNotEquals(false, strpos($queries[$key]->get('query'), 'in '.$location));
                    break;
                case 'Github':
                    $this->assertEquals($location, $queries[$key]->get('location'));
                    break;
                case 'Indeed':
                    $this->assertEquals($location, $queries[$key]->get('l'));
                    break;
                case 'Usajobs':
                    $this->assertEquals($location, $queries[$key]->get('LocationName'));
                    break;
                case 'Careercast':
                    $this->assertEquals($location, $queries[$key]->get('location'));
                    break;
                default:
                    throw new \Exception("Provider {$key} not found in test.");
            }
        }
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testItCannotSetLocationOnProvidersWhenInvalid()
    {
        $location = uniqid().' '.uniqid();
        $this->client->setLocation($location);
    }

    public function testItCanSetPageOnAllProviders()
    {
        $page = rand(1, 20);
        $perPage = rand(1, 20);
        $startFrom = ($page * $perPage) - $perPage;
        $result = $this->client->setPage($page, $perPage);

        $this->assertEquals(get_class($this->client), get_class($result));

        $queries = $this->getProtectedProperty($this->client, 'queries');

        foreach ($this->providers as $key => $provider) {
            switch ($key) {
                case 'Careerbuilder':
                    $this->assertEquals($page, $queries[$key]->get('PageNumber'));
                    $this->assertEquals($perPage, $queries[$key]->get('PerPage'));
                    break;
                case 'Dice':
                    $this->assertEquals($page, $queries[$key]->get('page'));
                    $this->assertEquals($perPage, $queries[$key]->get('pgcnt'));
                    break;
                case 'Govt':
                    $this->assertEquals($perPage, $queries[$key]->get('size'));
                    $this->assertEquals($startFrom, $queries[$key]->get('from'));
                    break;
                case 'Github':
                    $this->assertEquals($page-1, $queries[$key]->get('page'));
                    break;
                case 'Indeed':
                    $this->assertEquals($perPage, $queries[$key]->get('limit'));
                    $this->assertEquals($startFrom, $queries[$key]->get('start'));
                    break;
                case 'Usajobs':
                    $this->assertEquals($page, $queries[$key]->get('Page'));
                    $this->assertEquals($perPage, $queries[$key]->get('ResultsPerPage'));
                    break;
                case 'Careercast':
                    $this->assertEquals($page, $queries[$key]->get('page'));
                    $this->assertEquals($perPage, $queries[$key]->get('rows'));
                    break;
                default:
                    throw new \Exception("Provider {$key} not found in test.");
            }
        }
    }

    public function testItCanGetResultsFromSingleApi()
    {
        if (!getenv('REAL_CALL')) {
            $this->markTestSkipped('REAL_CALL not set. Real API calls will not be made.');
        }

        $keyword = 'engineering';
        $providers = [
            'Dice' => [],
        ];
        $client = new JobsMulti($providers);

        $client->setKeyword($keyword);

        $results = $client->getDiceJobs();

        $this->assertInstanceOf('JobApis\Jobs\Client\Collection', $results);
        foreach($results as $job) {
            $this->assertEquals($keyword, $job->query);
        }
    }

    public function testItCanGetAllResultsFromApis()
    {
        if (!getenv('REAL_CALL')) {
            $this->markTestSkipped('REAL_CALL not set. Real API calls will not be made.');
        }

        $providers = [
            'Dice' => [],
            'Govt' => [],
            'Github' => [],
        ];
        $client = new JobsMulti($providers);
        $keyword = 'engineering';

        $client->setKeyword($keyword)
            ->setLocation('Chicago, IL')
            ->setPage(1, 10);

        $jobs = $client->getAllJobs();

        foreach ($jobs as $provider => $results) {
            $this->assertInstanceOf('JobApis\Jobs\Client\Collection', $results);
            foreach($results as $job) {
                $this->assertEquals($keyword, $job->query);
            }
        }
    }

    private function getProtectedProperty($object, $property = null)
    {
        $class = new \ReflectionClass(get_class($object));
        $property = $class->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

}
