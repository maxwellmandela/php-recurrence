# PHP Recurring events library

> PHP library for working with recurrence rules, uses [simshaun/recurr](https://github.com/simshaun/recurr)

## What it does

> Very simply, php-recurrence enables scheduling recurring events a level lower than simshaun/recurr, e.g `twice every 3 weeks`
> This will enable have the event recurring two times every 3 week


How to 
-----------

### Creating simple frequency

You can create a simple array or dates by passing the start,end,freq, interval this way

```php
$timezone    = 'Africa/Nairobi';
$schedule = new use Reccurence\Schedule($timezone);
$events = $schedule->createEvents([
    'start' => '2019-07-01 00:00:00',
    'end'   => '2019-07-31 00:00:00',
    'interval' => 2,
    'freq' => 'WEEKLY',

    // how many times a week
    'recurrence_count' => 3,
]);
```



## Credits
> simshaun/recurr
> nesbot/carbon

## Contribution

> All is welcome!