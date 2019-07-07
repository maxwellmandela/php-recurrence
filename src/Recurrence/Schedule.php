<?php

namespace Reccurence;

use Carbon\Carbon;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\ArrayTransformerConfig;
use Recurr\Transformer\Constraint\BeforeConstraint;

/**
 * Recurrence/Schedule
 * 
 * Creates events from Reccur/Rule
 * 
 */
class Schedule
{

    const VARIABLE_KEYS = [
        'start',
        'end',
        'freq',
        'interval'
    ];

    /**
     * string $timezone
     */
    private $timezone = '';


    // transformer
    private $transformer;

    private $transformerConfig;


    public function __construct($timezone)
    {
        $this->timezone = $timezone;

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
     * Validates inputs
     * 
     * @param array $variables
     */
    public function validateInputs($variables)
    {
        $valid = 0;

        if (count($variables) < 1) {
            return false;
        }

        foreach (self::VARIABLE_KEYS as $key) {
            if (array_key_exists($key, $variables)) {
                $valid++;
            }
        }

        return count(self::VARIABLE_KEYS) == $valid;
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
            // TODO: gte the next day by a day from the previous one, using carbon
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
}
