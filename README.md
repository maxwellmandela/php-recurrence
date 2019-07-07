# PHP Recurring events library

> PHP library for working with recurrence rules, uses [simshaun/recurr](https://github.com/simshaun/recurr)

## What it does

> Very simply, php-recurrence enables scheduling recurring events a level lower than simshaun/recurr, e.g `twice every 3 weeks`
> This will generate an event recurring two times every 3 weeks


How to 
-----------


### Seting up
Configure your script for `php-recurrence`

```php
$timezone    = 'Africa/Nairobi';
$schedule = new use Reccurence\Schedule($timezone);
```


### Creating a simple weekly frequency

You can create a simple array of dates by passing the `start`,`end`,`freq`,`interval` this way

```php
$events = $schedule->createEvents([
    'start' => '2019-07-01 00:00:00',
    'end'   => '2019-07-31 00:00:00',
    'interval' => 2,
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

    // how many times a week for a WEEKLY frequency
    'recurrence_count' => 3,
]);
```


## Credits
##### simshaun/recurr
##### nesbot/carbon

## Contribution

> All is welcome!