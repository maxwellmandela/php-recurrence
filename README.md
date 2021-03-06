<img height="100px" width="100px" src="https://image.flaticon.com/icons/svg/123/123392.svg">

<div>Icons made by <a href="https://www.freepik.com/" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/"                 title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/"                 title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>

# php-recurrence - A PHP Recurring events library

> PHP library for working with recurrence rules, uses [simshaun/recurr](https://github.com/simshaun/recurr)

## What it does

> Very simply, php-recurrence enables scheduling recurring events much like  simshaun/recurr but with more abtraction, e.g `twice every 3 weeks`
> This will generate an event recurring two times every 3 weeks
> It also needs to be mentioned that this is an approach to learning to TDD in PHP


How to 
-----------


### Install

Install using [Composer](http://getcomposer.org):

```
composer require maxwellmandela/php-recurrence
```

No composer? You can clone/download the repository and use the package directly by including `bootstrap.php` onto your script


### Seting up
Configure your script for `php-recurrence`

```php
use Reccurence\Schedule;

$timezone    = 'Africa/Nairobi';
$schedule = new Schedule($timezone);
```


### Creating a simple weekly frequency

You can create a simple array of dates by passing the `start`,`end`,`freq`,`interval` this way

```php
$events = $schedule->createEvents([
    'start' => '2019-07-01 00:00:00',
    'end'   => '2019-07-31 00:00:00',
    'interval' => 2,

    // for weekly events, you can change this to either  MONTHLY|YEARLY|DAILY|HOURLY
    // read more here: https://tools.ietf.org/html/rfc5545 for all valid frequencies
    'freq' => 'WEEKLY',
]);
```

### Creating a weekly frequency with `no. of times` per interval

You can create an array of dates and number of recurrence per interval by passing the `start`,`end`,`freq`,`interval`,`recurrence_count` this way

```php
$events = $schedule->createEvents([
    'start' => '2019-07-01 00:00:00',
    'end'   => '2019-07-31 00:00:00',
    'interval' => 2,
    'freq' => 'WEEKLY',

    // for an event recurring 3 times(recurrence_count) every(interval)  2 weeks(freq)
    'recurrence_count' => 3,
]);
```


## Credits
##### simshaun/recurr
##### nesbot/carbon

## Contribution

> All is welcome!
