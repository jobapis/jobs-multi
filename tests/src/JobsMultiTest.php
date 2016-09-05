<?php namespace JobApis\Jobs\Client\Tests;

use Mockery as m;
use JobApis\Jobs\Client\JobsMulti;

class JobsMultiTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $providers = [
            'Careerbuilder' => [
                'DeveloperKey' => 'YYY',
            ],
            'Dice' => [],
            'Govt' => [],
            'Indeed' => [
                'publisher' => 'ZZZ',
            ],
        ];
        $this->client = new JobsMulti($providers);
    }

    public function testItCanInstantiateQueryObjects()
    {
        $class = new \ReflectionClass('JobApis\Jobs\Client\JobsMulti');
        $property = $class->getProperty('careerbuilderQuery');
        $property->setAccessible(true);
        // Gets the careerbuilderQuery property even though it's protected
        var_dump($property->getValue($this->client)); exit;
    }
}
