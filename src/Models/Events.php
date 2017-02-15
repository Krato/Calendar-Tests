<?php

namespace Infinety\Calendar\Models;

use Illuminate\Database\Eloquent\Model;
use Infinety\Calendar\Models\EventsModelsColor;
use Carbon\Carbon;

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

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start',
        'end',
        'created_at',
        'updated_at'
    ];


    function color(){
        if($this->model_id != null){
            $eventsModelsColor = new EventsModelsColor;
            return $eventsModelsColor->getColor($this->model_id);
        } else {
            return null;
        }
    }
    public function getStartHumansAttribute()
    {
        return $this->start->diffForHumans();
    }
    public function getEndHumansAttribute()
    {
        if($this->end == Null)
        {
            return 'No end date';
        } else {
        return $this->end->diffForHumans();
        }
    }
}
