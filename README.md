# Jobs Multi
Making it easy to get jobs from multiple job boards at once.

## Usage
```
$providers = [
    'Indeed' => [
        'developerKey' => 'XXX',
    ],
    'Dice' => [],
];

$client = new JobsMulti($providers);

$indeedJobs = $client->setLocation('chicago, il')
    ->setKeyword('training')
    ->setPage('1')
    ->setPerPage('10')
    ->getIndeedJobs();

$diceJobs = $client
    ->getDiceJobs();
```
