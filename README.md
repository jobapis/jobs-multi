# Jobs Multi
Making it easy to get jobs from multiple job boards at once.

## Usage
```php
// Include as many or as few providers as you want.
$providers = [
    'Indeed' => [
        'developerKey' => 'XXX',
    ],
    'Dice' => [],
];

$client = new JobsMulti($providers);

// Set the parameters in order: Keyword, Location, Page
$client->setKeyword('training')
    ->setLocation('chicago, il')
    ->setPage(1, 10);

// Make queries to each individually
$indeedJobs = $client->getIndeedJobs();
$diceJobs = $client->getDiceJobs();

// Or get an array with results from all the providers at once
$jobs = $client->getAllJobs();
```
