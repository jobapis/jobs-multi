<?php namespace JobApis\Jobs\Client\Tests;

use Mockery as m;
use JobApis\Jobs\Client\JobsMulti;

class JobsMultiTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->providers = [
            'Careerbuilder' => [
                'DeveloperKey' => 'YYY',
            ],
            'Dice' => [],
            'Govt' => [],
            'Github' => [],
            'Indeed' => [
                'publisher' => 'ZZZ',
            ],
        ];
        $this->client = new JobsMulti($this->providers);
    }

    public function testItCanInstantiateQueryObjects()
    {
        $queries = $this->getProtectedProperty($this->client, 'queries');

        foreach ($this->providers as $className => $provider) {
            $key = lcfirst($className).'Query';
            $this->assertTrue(isset($queries[$key]));
            $this->assertEquals(
                'JobApis\\Jobs\\Client\\Queries\\'.$className.'Query',
                get_class($queries[$key])
            );
        }
    }

    public function testItCanSetKeywordOnAllProviders()
    {
        $keyword = uniqid();
        $result = $this->client->setKeyword($keyword);

        $this->assertEquals(get_class($this->client), get_class($result));

        $queries = $this->getProtectedProperty($this->client, 'queries');

        foreach ($this->providers as $className => $provider) {
            $key = lcfirst($className) . 'Query';
            switch ($className) {
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
                default:
                    throw new \Exception("Provider {$className} not found in test.");
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

        foreach ($this->providers as $className => $provider) {
            $key = lcfirst($className) . 'Query';
            switch ($className) {
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
                default:
                    throw new \Exception("Provider {$className} not found in test.");
            }
        }
    }

    public function testItCanSetPageOnAllProviders()
    {
        $page = rand(1, 20);
        $result = $this->client->setPage($page);

        $this->assertEquals(get_class($this->client), get_class($result));
        // TODO: add more assertions for each provider
    }

    public function testItCanSetPerPageOnAllProviders()
    {
        $perPage = rand(1, 50);
        $result = $this->client->setPerPage($perPage);

        $this->assertEquals(get_class($this->client), get_class($result));
        // TODO: add more assertions for each provider
    }

    private function getProtectedProperty($object, $property = null)
    {
        $class = new \ReflectionClass(get_class($object));
        $property = $class->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

}
