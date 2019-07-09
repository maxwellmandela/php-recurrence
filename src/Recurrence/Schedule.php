<?php

/*
 * Copyright 2019 Maxwell Mandela
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Based on simshaun/recurr
 * Copyright (c) 2015 Shaun Simmons
 * https://github.com/simshaun/recurr/blob/master/LICENSE
 *
 * Based on nesbot/carbon
 * Copyright (C) Brian Nesbitt
 * https://github.com/briannesbitt/Carbon/blob/master/LICENSE
 */

namespace Reccurence;

use Carbon\Carbon;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\ArrayTransformerConfig;
use Recurr\Transformer\Constraint\BeforeConstraint;

use Reccurence\Traits\ValidatesInputs;
use Reccurence\ScheduleInterface;

/**
 * Recurrence/Schedule
 * 
 * Creates events from Reccur/Rule
 * 
 */
class Schedule implements ScheduleInterface
{

    use ValidatesInputs;

    /**
     * @var string $timezone
     */
    private $timezone = '';


    /**
     * @var transformer
     */
    private $transformer;

    /**
     * @var transformerConfig
     */
    private $transformerConfig;


    /**
     * Setup
     */
    public function __construct($timezone)
    {
        $this->timezone = $timezone;

        /**
         * The transformer configuration for last of day of month fix
         * 
         * By default, if your start date is on the 29th, 30th, or 31st, 
         * Recurr will skip following months that don't have at least that many days. 
         * (e.g. Jan 31 + 1 month = March)
         * 
         * Read more: https://github.com/simshaun/recurr#warnings
         */
        $this->transformerConfig = new ArrayTransformerConfig();
        $this->transformerConfig->enableLastDayOfMonthFix();

        $this->transformer = new ArrayTransformer();
        $this->transformer->setConfig($this->transformerConfig);
    }


    /**
     * Creates events from given rule
     * 
     * @param array $variables
     */
    public function createEvents(array $variables)
    {
        if ($this->validateInputs($variables)) {
            $events = $this->createEventsFromValidInputs($variables);
            return $events;
        };


        return [
            'success' => false,
            'message' => 'Missing arguments'
        ];
    }


    /**
     * Create the actual event dates from recurrence rule
     * 
     * @param array $variables
     */
    public function createEventsFromValidInputs($variables)
    {

        $startDate   = new \DateTime($variables['start'], new \DateTimeZone($this->timezone));
        $endDate     = new \DateTime($variables['end'], new \DateTimeZone($this->timezone));
        $rule        = new Rule('FREQ=' . $variables['freq'] . ';INTERVAL=' . $variables['interval'], $startDate, $endDate, $this->timezone);


        // apply constraint - before end date
        $constraint = new BeforeConstraint(new \DateTime($variables['end']));

        $events = $this->transformer->transform($rule, $constraint);

        // check expected event types by frequency, reccurence_count
        // helps achieve no of occurrence per interval e.g 'two times every 2 weeks' schedule
        // the functionality lacks in recurr, which is why I've added this little library
        if (isset($variables['recurrence_count']) && is_int($variables['recurrence_count'])) {
            return $this->createEventsByReccurenceCount($variables, $events);
        }

        return $this->eventsAsArray($events);
    }


    public function createEventsByReccurenceCount($variables, $events)
    {
        switch ($variables['freq']) {
            case 'WEEKLY':
                return $this->weeklyReccurence($events, $variables);
                break;

            default:
                return $this->eventsAsArray($events);
                break;
        }
    }


    /**
     * Handles creating WEEKLY events
     * 
     * @param array $events
     * 
     * @param array $variables
     * 
     * @return array $dates
     */
    function weeklyReccurence($events, $variables)
    {
        $interval =  $variables['interval'];
        $recurrence_count = $variables['recurrence_count'];

        $week_events = [];
        $dates = [];

        foreach ($events as $key => $value) {
            $start = $value->getStart()->format('Y-m-d H:i:s');


            if ($key > 0) {
                $start = new Carbon(end($week_events));
                $start = $start->add($interval, 'weeks')->format('Y-m-d H:i:s');
            }

            $end = new Carbon($start);
            $end = $end->add(7, 'days')->format('Y-m-d H:i:s');
            $rule        = new Rule('FREQ=DAILY', new \DateTime($start), new \DateTime($end), $this->timezone);
            $constraint = new BeforeConstraint(new \DateTime($end));
            $events = $this->transformer->transform($rule, $constraint);

            // set selected weeks days, must be less than or equal to $recurrence_count
            // TODO: get the next day by a day from the previous one, using carbon
            for ($i = 0; $i < $recurrence_count; $i++) {
                if ($events[$i]) {
                    array_push($dates, $events[$i]);
                }
            }

            array_push($week_events, $value->getStart()->format('Y-m-d H:i:s'));
        }

        return $this->eventsAsArray($dates);
    }


    /**
     * Creates array from events
     * 
     * @param array $events
     */
    public function eventsAsArray($events)
    {
        $dates = array();

        foreach ($events as $key => $value) {
            $start = $value->getStart()->format('Y-m-d H:i:s');
            $end = $value->getEnd()->format('Y-m-d H:i:s');

            array_push($dates, [
                'start' => $start,
                'end'   => $end
            ]);
        }

        return $dates;
    }


    /**
     * Filters events by `start` date
     * 
     * @param mixed $events
     * 
     * @param date $date
     */
    public function filter($events, $date)
    {
        $valid = [];
        for ($i = 0; $i < count($events); $i++) {
            $start = Carbon::parse($events[$i]['start']);
            $date = Carbon::parse($date);
            if ($start->equalTo($date)) {
                array_push($valid, $events[$i]);
            }
        }

        return $valid;
    }
}
