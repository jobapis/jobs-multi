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

JobsMulti allows you to easily retrieve job listings from many job boards with one library and just a few lines of code. Here is a brief overview of what you can do:

Install the package via composer:
```bash
$ composer require jobapis/jobs-multi
```

```php
// Include as many or as few providers as you want. Just be sure to include any required keys.
$providers = [
    'Careerbuilder' => [
        'DeveloperKey' => '<YOUR DEVELOPER KEY>',
    ],
    'Careercast' => [],
    'Careerjet' => [
        'affid' => '<YOUR AFFILIATE ID>',
    ],
    'Dice' => [],
    'Github' => [],
    'Govt' => [],
    'Ieee' => [],
    'Indeed' => [
        'publisher' => '<YOUR PUBLISHER ID>',
    ],
    'Jobinventory' => [],
    'Juju' => [
        'partnerid' => '<YOUR PARTNER ID>',
    ],
    'Stackoverflow' => [],
    'Usajobs' => [
        'AuthorizationKey' => '<YOUR API KEY>',
    ],
    'Ziprecruiter' => [
        'api_key' => '<YOUR API KEY>',
    ],
];

// Instantiate a new JobsMulti client
$client = new JobsMulti($providers);

// Set the parameters: Keyword, Location, Page
$client->setKeyword('training')
    // Location must be formatted "City, ST".
    ->setLocation('chicago, il')
    ->setPage(1, 10);

// Make queries to each individually
$indeedJobs = $client->getJobsByProvider('Indeed');

// And include an array of options if you'd like
$options = [
    'maxAge' => 30,              // Maximum age (in days) of listings
    'maxResults' => 100,         // Maximum number of results
    'orderBy' => 'datePosted',   // Field to order results by
    'order' => 'desc',           // Order ('asc' or 'desc')
];
$diceJobs = $client->getJobsByProvider('Dice', $options);

// Or get an array with results from all the providers at once
$jobs = $client->getAllJobs($options);
```

The `getJobsByProvider` and the `getAllJobs` method will return a [Collection](https://github.com/jobapis/jobs-common/blob/master/src/Collection.php) containing many [Job](https://github.com/jobapis/jobs-common/blob/master/src/Job.php) objects.

## Documented Methods

- `setProviders($providers)` Set the providers you want to use (see example above) with default and required parameters for each.

- `setKeyword($keyword)` Set the search string.

- `setLocation($location)` Set the location string. Should be in the format "City, ST". Currently only supports US locations.

- `setPage($pageNumber, $perPage)` Set the results page options.

- `setOptions($options)` Set options for `getAllJobs` method. Options include:
    - `maxAge` Maximum age (in days) of listings.
    - `maxResults` Truncate the results to a certain number.
    - `order` Sort results by `asc` or `desc`.
    - `orderBy` Field to order results by, eg: `datePosted`.

- `getAllJobs($options)` Get a collection of jobs from all providers set above. See `setOptions` method above for available options.

- `getJobsByProvider($provider, $options)` Get a collection of jobs from a single provider by name. The provider must be in the array of providers. See `setOptions` method above for available options.

## Supported APIs

This package currently supports the following API providers:

- [Careerbuilder](https://github.com/jobapis/jobs-careerbuilder)
- [Careercast](https://github.com/jobapis/jobs-careercast)
- [Careerjet](https://github.com/jobapis/jobs-careerjet)
- [Dice](https://github.com/jobapis/jobs-dice)
- [Github](https://github.com/jobapis/jobs-github)
- [Govt](https://github.com/jobapis/jobs-govt)
- [IEEE](https://github.com/jobapis/jobs-ieee)
- [Indeed](https://github.com/jobapis/jobs-indeed)
- [Jobinventory](https://github.com/jobapis/jobs-jobinventory)
- [Juju](https://github.com/jobapis/jobs-juju)
- [Stack Overflow](https://github.com/jobapis/jobs-stackoverflow)
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
