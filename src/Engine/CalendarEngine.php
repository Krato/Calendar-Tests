<?php

namespace Infinety\Calendar\Engine;

use Carbon\Carbon;
use Infinety\Calendar\Exceptions\DateDifferenceException;
use Infinety\Calendar\Exceptions\InvalidDateStringException;

/**
 * Calendar events engine. Calculates repeat dates and event length.
 *
 * @package Infinety\Calendar\Engine
 * @author Eric Lagarda <eric@infinety.es>
 */
class CalendarEngine
{
    /**
     * @var Carbon
     */
    protected $carbon;

    /**
     * CalendarEventsEngine constructor.
     *
     * @param Carbon $carbon
     */
    public function __construct(Carbon $carbon)
    {
        $this->carbon = $carbon;
    }

    /**
     * Builds event data
     *
     * @param array $data
     *
     * @return array
     */
    public function buildEventData(array $data)
    {
        $start = strtotime($data['start']);
        $start = date('Y-m-d H:i:s', $start);
        $end = null;
        $repeat_week = 0;

        if (array_key_exists('end', $data)) {
            $end = strtotime($data['end']);
            $end = date('Y-m-d H:i:s', $end);

            if (strtotime($end) < strtotime($start)) {
                throw new DateDifferenceException('Start date bigger then end date!');
            }
        }
        if (array_key_exists('repeat', $data)) {
            $repeat_week = 1;
        }

        $event = [
            'title' => $data['title'],
            'description' => $data['description'],
            'start' => $start,
            'end' => $end,
            'all_day' => array_key_exists('all_day', $data),
            'repeat_week' => $repeat_week,
            'model_id' => ( $data['model_id'] != '' ) ?  $data['model_id'] : null
        ];

        

        return $event;
    }


    public function buildEventTimeData(array $data){
        $start = strtotime($data['start']);
        $start = date('Y-m-d H:i:s', $start);


        $end = strtotime($data['end']);
        $end = date('Y-m-d H:i:s', $end);

        $updateData = [
            'start' => $start,
            'end' => $end
        ];
        return $updateData;
    }

    /**
     * Creates JSON string from events collection
     *
     * @param array $calendarEvents
     *
     * @return array
     */
    public function formatEventsToJson(array $calendarEvents, $start, $end)
    {
        $array = [];

        foreach ($calendarEvents as $event) {

            if($event->repeat_week == false){
                $startEvent = $this->carbon
                    ->copy()
                    ->setTimestamp(strtotime($event->start));
                $endEvent = $this->carbon
                    ->copy()
                    ->setTimestamp(strtotime($event->end));
                    
                $allDay = $event->all_day == 1;

                $className = null;
                if($event->color() != '' ){
                    $className = $event->color();
                } else {
                    $className = ($event->class != null) ? $event->class : null;
                }

                $data = [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'start' => $startEvent->toIso8601String(),
                    'end' => ($allDay == true) ? $endEvent->addDay()->toIso8601String() : $endEvent->toIso8601String(),
                    'allDay' => $allDay,
                    'className' => $className,
                    'repeat' => false,
                    'model_id' => $event->model_id
                ];

                $array[] = $data;

            } else {
                    
                    $startCheck = $this->carbon->copy()->parse($event->start);
                    $endCheck = $this->carbon->copy()->parse($end);

                    $startInitialEvent =  $this->carbon->copy()->parse($event->start);
                    $endEventDate = $this->carbon->copy()->parse($event->repeat_event_end);

                    $datesRange = $this->generateDateRange($startCheck, $endCheck, ($event->repeat_event_end != null) ? $endEventDate : null,  $startInitialEvent->dayOfWeek);
                    

                    $className = null;
                    if($event->color() != '' ){
                        $className = $event->color();
                    } else {
                        $className = ($event->class != null) ? $event->class : null;
                    }

                    // dd();
                    foreach ($datesRange as $dt) {

                            
                        $startEvent = $this->carbon->copy()->parse($dt)->toIso8601String();
                        $endEvent = $this->carbon
                            ->copy()
                            ->setTimestamp(strtotime($event->end));

                        $endEvent->day = $dt->format('d');
                        $endEvent->month = $dt->format('m');
                        $endEvent->year = $dt->format('Y');

                        $endEvent = $endEvent->toIso8601String();
                        $allDay = $event->all_day == 1;
                        $data = [
                            'id' => $event->id,
                            'title' => $event->title,
                            'description' => $event->description,
                            'start' => $startEvent,
                            'end' => $endEvent,
                            'allDay' => $allDay,
                            'className' => $className,
                            'repeat' => true,
                            'model_id' => $event->model_id
                        ];

                        $array[] = $data;
                        

                    }

            }

        }
        return $array;
    }


    /**
     * Generates an array of dates between two days and given week days
     * 
     * @param  Carbon      $start_date
     * @param  Carbon      $end_date
     * @param  int|boolean $weekDay
     * @return array   
     */
    private function generateDateRange(Carbon $start_date, Carbon $end_date, $finalEventDate, $weekDay = false)
    {
        $dates = [];
        $end_date->addDay();


        $start_date = Carbon::parse($start_date->toDateString());
        $end_date = Carbon::parse($end_date->toDateString());

        // dump("-----------------");
        // dump("New Event");
        // dump("Fecha Inicio: ".$start_date);
        // dump("Fecha Fin: ".$end_date);
        // dump("Fecha Máxima: ".$finalEventDate);
        for($date = $start_date; $date->lte($end_date); $date->addDay()) {



            if($finalEventDate == null || $finalEventDate->lt($date) == false ){
                if($weekDay == false || $date->dayOfWeek == $weekDay){
                    $dates[] = $date->copy();
                }
            }
        }
        // dump($dates);
        return $dates;
    }


    /**
     * Calculate event length in seconds
     *
     * @param array $data
     *
     * @return int
     */
    protected function calculateEventLength(array $data)
    {
        $start = $this->carbon->copy()->setTimestamp(strtotime($data['start']['date'] . ' ' . $data['start']['time']));

        if (array_key_exists('all_day', $data)) {
            $end = $this->carbon->copy()->setTimestamp(strtotime($data['start']['date'] . ' 23:59:59'));
        } else {
            $end = $this->carbon->copy()->setTimestamp(strtotime($data['start']['date'] . ' ' . $data['end']['time']));
        }

        return $start->diffInSeconds($end);
    }
}