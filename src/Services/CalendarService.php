<?php

namespace Infinety\Calendar\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Infinety\Calendar\Engine\CalendarEngine;
use Infinety\Calendar\Models;

/**
 * Calendar events service
 *
 * @package Infinety\Calendar\Services
 * @author Eric Lagarda <eric@infinety.es>
 */
class CalendarService
{
    /**
     * @var CalendarEngine
     */
    protected $calendarEngine;

    /**
     * @var Models\Events
     */
    protected $events;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * Cache key
     */
    const CACHE_KEY = 'calendar_';

    /**
     * All calendar events cache key
     */
    const ALL_EVENTS_KEY = 'all_calendar_events';

    /**
     * All calendar events json string cache key
     */
    const ALL_EVENTS_TO_JSON_KEY = 'all_calendar_events_json';

    /**
     * @var int
     */
    protected $cacheTimeToLive;

    /**
     * calendarsService constructor.
     *
     * @param calendarsEngine $calendarEngine
     * @param Models\Events $events
     * @param Cache $cache
     * @param int $cacheTimeToLive
     */
    public function __construct(
        CalendarEngine $calendarEngine,
        Models\Events $events,
        Models\EventsModelsColor $eventsColors,
        Cache $cache
    ) {
        $this->calendarsEngine = $calendarEngine;
        $this->events = $events;
        $this->eventsColors = $eventsColors;
        $this->cache = $cache;
        $this->cacheTimeToLive = $this->setCacheTimeToLive(config('calendar.cacheTime', 10));
    }

   
    /**
     * Check if should use cache or not
     * 
     * @return bool
     */
    private function useCache()
    {
        return config('calendar.useCache', false);
    }


    /**
     * @param int $cacheTimeToLive
     *
     * @return $this
     */
    public function setCacheTimeToLive($cacheTimeToLive)
    {
        $this->cacheTimeToLive = $cacheTimeToLive;

        return $this;
    }

    /**
     * Stores new cache file from name and values given
     * 
     * @param  string $name
     * @param  object $values
     */
    private function storeCache($name, $values){
        $cache = $this->cache;
        $cache::tags('InfinetyCalendar')->put($name, $values, $this->cacheTimeToLive);
    }

    /**
     * Remove item from cache
     * 
     * @param  string $name
     */
    private function forgetCache($name){
        $cache = $this->cache;
        $cache::tags('InfinetyCalendar')->forget($name);
    }


    /**
     * Creates calendar event
     *
     * @param array $data
     *
     * @return bool
     */
    public function createEvent(array $data)
    {
        $eventData = $this->calendarsEngine->buildEventData($data);
        
        $event = $this->events->create($eventData);

        if($this->useCache()){
            $this->storeCache(self::CACHE_KEY . $event->id, $event);
            $allEvents = $this->getAllEvents();
            $allEvents[$event->id] = $event;

            $this->storeCache(self::ALL_EVENTS_KEY, $allEvents);
            $this->forgetCache(self::ALL_EVENTS_TO_JSON_KEY);
        }
        return true;
    }

    public function createModelColor(array $data){
        $eventsColors = $this->eventsColors->create($data);
        return true;
    }
    

    /**
     * Gets an calendar event based on id
     *
     * @param int $id
     *
     * @return Models\Event
     */
    public function getCalendar($id)
    {
        /** @var Models\Event $event */
        $event = null;

        if($this->useCache()){
            $cache = $this->cache;
            if ($cache::tags('InfinetyCalendar')->has(self::CACHE_KEY . $id)) {
                return $cache::tags('InfinetyCalendar')->get(self::CACHE_KEY . $id);
            }
        }

        $event = $this->events
            ->where('id', $id)
            ->firstOrFail();

        if($this->useCache()){
            $this->storeCache(self::CACHE_KEY . $id, $event);
        }

        return $event;
    }

    /**
     * Gets all calendar events
     *
     * @return \Illuminate\Database\Eloquent\Collection|null|static[]
     */
    public function getAllEvents()
    {
        $events = null;

        if($this->useCache()){
            $cache = $this->cache;
            if ($cache::tags('InfinetyCalendar')->has(self::ALL_EVENTS_KEY)) {
                return $cache::tags('InfinetyCalendar')->get(self::ALL_EVENTS_KEY);
            }
        }

        $allEvents = $this->events->get();

        $events = [];

        foreach ($allEvents as $event) {
            $events[$event->id] = $event;
        }

        if($this->useCache()){
            $this->storeCache(self::ALL_EVENTS_KEY, $events);
        }

        return $events;
    }

    /**
     * Gets all calendar events by dates
     *
     * @return \Illuminate\Database\Eloquent\Collection|null|static[]
     */
    public function getAllEventsByDates($start, $end)
    {
        $events = null;
        

        $startCompare = Carbon::parse($start);
        $endCompare = Carbon::parse($end);

        $datesUnique =  md5($start.$end);

        if($this->useCache()){
            $cache = $this->cache;
            if ($cache::tags('InfinetyCalendar')->has(self::ALL_EVENTS_KEY.$datesUnique)) {
                return $cache::tags('InfinetyCalendar')->get(self::ALL_EVENTS_KEY.$datesUnique);
            }
        }
        

        $allEvents = $this->events->whereBetween('start', [$startCompare, $endCompare])
                                  ->orWhereBetween('end', [$startCompare, $endCompare])
                                  ->orWhere('repeat_week', 1)
                                  ->get();
 
        $events = [];
        foreach ($allEvents as $event) {
            $events[$event->id] = $event;
        }

        if($this->useCache()){
            $this->storeCache(self::ALL_EVENTS_KEY.$datesUnique, $events);
        }

        return $events;
    }

    /**
     * Get all events JSON
     *
     * @return string
     */
    public function getAllEventsAsJson($start, $end)
    {

        $datesUnique =  md5($start.$end);
        if($this->useCache()){
            $cache = $this->cache;
            if ($cache::tags('InfinetyCalendar')->has(self::ALL_EVENTS_TO_JSON_KEY.$datesUnique)) {
                return $cache::tags('InfinetyCalendar')->get(self::ALL_EVENTS_TO_JSON_KEY.$datesUnique);
            }
        }
        

        $allEvents = $this->calendarsEngine->formatEventsToJson($this->getAllEventsByDates($start, $end), $start, $end);
        $allEventsToJson = json_encode($allEvents);

        if($this->useCache()){
            $this->storeCache(self::ALL_EVENTS_TO_JSON_KEY.$datesUnique, $allEventsToJson);
        }

        return $allEventsToJson;

    }

    /**
     * Deletes an calendar event and rebuilds the cache.
     *
     * @param int $id
     *
     * @return bool
     */
    public function deletecalendar($id)
    {
        $this->events->destroy($id);

        $allEvents = $this->getAllEvents();
        unset($allEvents[$id]);

        if($this->useCache()){
            $this->storeCache(self::ALL_EVENTS_KEY, $allEvents);
        }

        return true;
    }

    /**
     * Updates an calendar event
     *
     * @param int $id
     * @param array $data
     *
     * @return bool
     */
    public function updatecalendar($id, array $data)
    {
        $eventData = $this->calendarsEngine->buildEventData($data);

        $this->events
            ->where('id', $id)
            ->update($eventData);
        $event = $this->events
            ->where('id', $id)
            ->firstOrFail();


        if($this->useCache()){
            $this->storeCache(self::CACHE_KEY . $event->id, $event);
            $allEvents = $this->getAllEvents();
            $allEvents->put($event->id, $event);
            $this->storeCache(self::ALL_EVENTS_KEY, $allEvents);
        }

        return true;
    }

    /**
     * Update dragged event
     * 
     * @param  integer $id
     * @param  array  $data
     */
    public function updateDragEvent($id, array $data)
    {
        $eventData = $this->calendarsEngine->buildEventTimeData($data);

        $this->events
            ->where('id', $id)
            ->update($eventData);
        $event = $this->events
            ->where('id', $id)
            ->firstOrFail();

        if($this->useCache()){
            $cache = $this->cache;
            $cache::tags('InfinetyCalendar')->flush();
        }
    }

    /**
     * Update drag event from repetead event and creates another repeated event from next week.
     * 
     * @param  $int $id
     * @param  array  $data
     */
    public function updateDragEventRepeatedOnlyThis($id, array $data)
    {
            $event = $this->events->find($id);
            $newEvent = $event->replicate();
            $newEventRepeated = $event->replicate();

            $startDateGiven = Carbon::parse($data['event-id-moved-day-start']);

            $startFromEvent = Carbon::parse($event->start);
            $endFromEvent = Carbon::parse($event->end);


            $event->repeat_event_end = $startDateGiven->copy()->startOfWeek()->previous($startFromEvent->dayOfWeek)->toDateTimeString();
            $event->save();

            $newEvent->start = Carbon::parse($data['event-id-moved-day-start'])->toDateTimeString();
            $newEvent->end = Carbon::parse($data['event-id-moved-day-end'])->toDateTimeString();
            $newEvent->repeat_week = false;
            $newEvent->save();

            $newRepeatedStart = Carbon::parse($data['event-id-moved-day-start'])->addWeek()->startOfWeek()->next($startFromEvent->dayOfWeek);
            $newRepeatedStart->hour = $startFromEvent->hour;
            $newRepeatedStart->minute = $startFromEvent->minute;

            $newRepeatedEnd = Carbon::parse($data['event-id-moved-day-end'])->addWeek()->startOfWeek()->next($event->dayOfWeek);
            $newRepeatedEnd->hour = $endFromEvent->hour;
            $newRepeatedEnd->minute = $endFromEvent->minute;

            $newEventRepeated->start =  $newRepeatedStart->toDateTimeString();
            $newEventRepeated->end =  $newRepeatedEnd->toDateTimeString();
            $newEventRepeated->save();
    }

    /**
     * Update drag event from repetead event
     * 
     * @param  int $id
     * @param  array  $data
     */
    public function updateDragEventRepeatedAll($id, array $data){
            $event = $this->events->find($id);
            $eventStart = Carbon::parse($event->start);
            $eventEnd = Carbon::parse($event->end);

            $startDateGiven = Carbon::parse($data['event-id-moved-day-start']);
            $endDateGiven = Carbon::parse($data['event-id-moved-day-end']);

            $newEventStart = Carbon::parse($event->start)->startOfWeek()->next($startDateGiven->dayOfWeek);
            $newEventStart->hour = $eventStart->hour;
            $newEventStart->minute = $eventEnd->minute;

            $newEventEnd = Carbon::parse($event->end)->startOfWeek()->next($endDateGiven->dayOfWeek);
            $newEventEnd->hour = $eventEnd->hour;
            $newEventEnd->minute = $eventEnd->minute;

            $eventRepeatedEnd = Carbon::parse($event->repeat_event_end)->startOfWeek()->next($endDateGiven->dayOfWeek);
            $eventRepeatedEnd->hour = $eventEnd->hour;
            $eventRepeatedEnd->minute = $eventEnd->minute;

            $endDateOfEvent = $endDateGiven->toDateTimeString();
            $event->start = $newEventStart->toDateTimeString();
            $event->end = $newEventEnd->toDateTimeString();
            $event->repeat_event_end = $eventRepeatedEnd->toDateTimeString();
            $event->save();
    }

}