<?php

namespace Recurrence\Test;

use Reccurence\Schedule;
use PHPUnit\Framework\TestCase;

class ScheduleTest extends TestCase
{
    /** @var Schedule */
    protected $schedule;

    public function __construct()
    {
        parent::__construct();

        $this->schedule = new Schedule('Africa/Nairobi');
    }

    public function testChecksInvalidInputs()
    {
        $events = $this->schedule->createEvents([]);
        $this->assertSame([
            'success' => false,
            'message' => 'Missing arguments'
        ], $events);
    }

    /**
     * Asserts events valid, 
     * 
     * @param array $array
     */
    public function hasEvents($array)
    {
        $this->assertIsArray($array);
        $this->assertArrayHasKey('start', $array[0]);
        $this->assertArrayHasKey('end', $array[0]);
    }

    public function testCanScheduleByWeek()
    {
        $events = $this->schedule->createEvents([
            'start' => '2019-07-01 00:00:00',
            'end'   => '2019-07-31 00:00:00',
            'interval' => 2,
            'freq' => 'WEEKLY'
        ]);

        $this->hasEvents($events);
    }

    public function testCanScheduleMultipleDaysByWeek()
    {
        $events = $this->schedule->createEvents([
            'start' => '2019-07-01 00:00:00',
            'end'   => '2019-07-31 00:00:00',
            'interval' => 2,
            'freq' => 'WEEKLY',

            // how many times a week
            'recurrence_count' => 3,
        ]);

        $this->hasEvents($events);
    }
}
