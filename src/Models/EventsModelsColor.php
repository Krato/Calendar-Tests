<?php

namespace Infinety\Calendar\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Calendar event model
 *
 * @package Todstoychev\CalendarEvents\Models
 * @author Todor Todorov <todstoychev@gmail.com>
 */
class EventsModelsColor extends Model
{
    /**
     * @var string
     */
    protected $table = 'calendar_events_colors';

    /**
     * @var boolean
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'model_id',
        'color'
    ];


    public function events($id){
        $events = Events::where('model_id', $id)->get();
        return $events;
    }


    /**
     * Check if ModelId has Color saved and returns color
     *
     * @param  int $modelId
     * @return string
     */
    public function getColor($modelId){
        $color = EventsModelsColor::where('model_id', $modelId)->first();
        if($color){
            return $color->color;
        } else {
            return '';
        }
    }

}
