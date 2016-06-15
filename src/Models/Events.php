<?php

namespace Infinety\Calendar\Models;

use Illuminate\Database\Eloquent\Model;
use Infinety\Calendar\Models\EventsModelsColor;

/**
 * Calendar event model
 *
 * @package Todstoychev\CalendarEvents\Models
 * @author Todor Todorov <todstoychev@gmail.com>
 */
class Events extends Model
{
    /**
     * @var string
     */
    protected $table = 'calendar_events';

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'start',
        'end',
        'all_day',
        'repeat_week',
        'class',
        'repeat_event_end',
        'model_id'
    ];

    function color(){
        if($this->model_id != null){
            $eventsModelsColor = new EventsModelsColor;
            return $eventsModelsColor->getColor($this->model_id);
        } else {
            return null;
        }
        
    }
}
