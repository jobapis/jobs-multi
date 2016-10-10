# Jobs Multi
[![Latest Version](https://img.shields.io/github/release/jobapis/jobs-multi.svg?style=flat-square)](https://github.com/jobapis/jobs-multi/releases)
[![Software License](https://img.shields.io/badge/license-APACHE%202.0-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/jobapis/jobs-multi/master.svg?style=flat-square&1)](https://travis-ci.org/jobapis/jobs-multi)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/jobapis/jobs-multi.svg?style=flat-square)](https://scrutinizer-ci.com/g/jobapis/jobs-multi/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/jobapis/jobs-multi.svg?style=flat-square)](https://scrutinizer-ci.com/g/jobapis/jobs-multi)
[![Total Downloads](https://img.shields.io/packagist/dt/jobapis/jobs-multi.svg?style=flat-square)](https://packagist.org/packages/jobapis/jobs-multi)

This package allows you to easily make basic queries to job board APIs supported by [Jobs Common v2](https://github.com/jobapis/jobs-common).

Each client on its own will give you more flexibility and access to all the parameters for its respective API, but this package allows you to query one or more API in a single call.

## Usage

```php
// Include as many or as few providers as you want. Just be sure to include any required keys.
$providers = [
    'Careerbuilder' => [
        'DeveloperKey' => '<YOUR DEVELOPER KEY>',
    ],
    'Careercast' => [],
    'Dice' => [],
    'Github' => [],
    'Govt' => [],
    'Indeed' => [
        'publisher' => '<YOUR PUBLISHER ID>',
    ],
    'Juju' => [
        'partnerid' => '<YOUR PARTNER ID>',
    ],
    'Usajobs' => [
        'AuthorizationKey' => '<YOUR API KEY>',
    ],
    'Ziprecruiter' => [
        'api_key' => '<YOUR API KEY>',
    ],
];

// Instantiate a new JobsMulti client
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

The `get<Provider>Jobs()` methods will return a [Collection](https://github.com/jobapis/jobs-common/blob/master/src/Collection.php) of [Job](https://github.com/jobapis/jobs-common/blob/master/src/Job.php) objects, while the `getAllJobs()` method will return an array of these collections with keys for each API provider.

## Supported APIs

This package currently supports the following API providers:

- [Careerbuilder](https://github.com/jobapis/jobs-careerbuilder)
- [Careercast](https://github.com/jobapis/jobs-careercast)
- [Dice](https://github.com/jobapis/jobs-dice)
- [Github](https://github.com/jobapis/jobs-github)
- [Govt](https://github.com/jobapis/jobs-govt)
- [Indeed](https://github.com/jobapis/jobs-indeed)
- [Juju](https://github.com/jobapis/jobs-juju)
- [USAJobs](https://github.com/jobapis/jobs-usajobs)
- [Ziprecruiter](https://github.com/jobapis/jobs-ziprecruiter)

If you'd like to add support for another provider, please see [Contributing.md](CONTRIBUTING.MD).

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Karl Hughes](https://github.com/karllhughes)
- [All Contributors](https://github.com/jobapis/jobs-multi/contributors)

## License

The Apache 2.0. Please see [License File](/LICENSE.md) for more information.
