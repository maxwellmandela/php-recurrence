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


/**
 * Recurrence/ScheduleInterface
 * 
 * Creates events from Reccur/Rule
 * 
 */
interface ScheduleInterface
{

    public const INPUT_KEYS = [
        'start',
        'end',
        'freq',
        'interval'
    ];

    public const  MINIMUN_INPUTS = 4;


    /**
     * Creates events from given rule
     * 
     * @param array $variables
     */
    public function createEvents(array $variables);


    /**
     * Create the actual event dates from recurrence rule
     * 
     * @param array $variables
     */
    public function createEventsFromValidInputs($variables);


    /**
     * Creates array from events
     * 
     * @param array $events
     */
    public function eventsAsArray($events);


    /**
     * Filters events by `start` date
     * 
     * @param mixed $events
     * 
     * @param date $date
     */
    public function filter($events, $date);
}
