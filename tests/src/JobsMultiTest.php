<?php namespace JobApis\Jobs\Client\Tests;

use Mockery as m;
use JobApis\Jobs\Client\JobsMulti;

class JobsMultiTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->client = new JobsMulti([]);
    }

    public function testItCanGetClientResponse()
    {
        $this->assertTrue(true);
    }
}
