<?php

/**
 * Instructions for running this example:
 *
 * - In your terminal, navigate to this folder.
 * - Install composer dependencies: `composer install`
 * - Run the collector: `php index.php`
 *
 * If you have any questions, see the repo on Github: https://github.com/jobapis/jobs-multi
 */

require __DIR__ . '/vendor/autoload.php';

$providers = [
    'Careercast' => [],
    'Dice' => [],
    'Github' => [],
    'Govt' => [],
    'Ieee' => [],
    'Jobinventory' => [],
    'Monster' => [],
    'Stackoverflow' => [],
];

$client = new \JobApis\Jobs\Client\JobsMulti($providers);

$client->setKeyword('training')
    ->setLocation('chicago, il')
    ->setPage(1, 10);

$options = [
    'maxAge' => 7,
    'maxResults' => 20,
    'orderBy' => 'datePosted',
    'order' => 'desc',
];

// Get all the jobs
$jobs = $client->getAllJobs($options);

// Output a summary of jobs as arrays
echo count($jobs)." Jobs Found".PHP_EOL;

foreach ($jobs->all() as $job) {
    print_r($job->toArray());
}
