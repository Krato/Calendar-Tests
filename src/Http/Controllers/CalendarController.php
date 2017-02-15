<?php

namespace Infinety\Calendar\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Infinety\Calendar\Http\Requests\EventRequest;
use Infinety\Calendar\Models\EventsModelsColor;
use Infinety\Calendar\Models\Events;
use Infinety\Calendar\Services\CalendarService;

class CalendarController extends Controller
{
    protected $calendarService;

    /**
     * @param CalendarService $calendarService
     */
    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }
    /**
     * Home of Calendar.
     *
     * @return [type]
     */
    public function index()
    {
        $colorModel = new EventsModelsColor();
        $modelData = auth()->user()->getFilters();
        $modelColumn = config('calendar.modelColumn', 'name');

        return $this->firstViewThatExists('vendor/infinety/calendar/index', 'calendar.index', compact('modelData', 'modelColumn', 'colorModel'));
    }

    /**
     * Home of Model List.
     *
     * @return view
     */
    public function modelIndex()
    {
        $colorModel = new EventsModelsColor();
        $modelData = app(config('calendar.modelBind'))->get();
        $modelColumn = config('calendar.modelColumn', 'name');

        return $this->firstViewThatExists('vendor/infinety/calendar/model/index', 'calendar.model.index', ['modelForm' => true, 'model' => $modelData, 'modelColumn' => $modelColumn, 'colorModel' => $colorModel]);
    }

    /**
     * Edit a model.
     *
     * @param int $modelId
     *
     * @return view
     */
    public function modelEdit($modelId)
    {
        $colorModel = new EventsModelsColor();
        $modelData = app(config('calendar.modelBind'))->find($modelId);
        $modelColumn = config('calendar.modelColumn', 'name');

        return $this->firstViewThatExists('vendor/infinety/calendar/create', 'calendar.create', ['modelForm' => true, 'modelId' => $modelId, 'model' => $modelData, 'modelColumn' => $modelColumn, 'colorModel' => $colorModel]);
    }

    /**
     * Save a color to model.
     *
     * @param Request $request
     *
     * @return view
     */
    public function modelPost(Request $request)
    {
        $color = EventsModelsColor::where('model_id', $request->model_id)->first();
        if(isset($color)){
            $color->delete();
        }
        $this->calendarService->createModelColor($request->input());

        return redirect()->to('calendar/model');
    }

    /**
     * Add Event form.
     *
     * @return view
     */
    public function form()
    {
        $colorModel = new EventsModelsColor();
        $modelData = app(config('calendar.modelBind'))->get();
        $modelColumn = config('calendar.modelColumn', 'name');

        return view('calendar.create', ['action' => 'post', 'model' => $modelData, 'modelColumn' => $modelColumn, 'colorModel' => $colorModel]);
    }

    /**
     * Save a new event.
     *
     * @param EventRequest $request
     *
     * @return redirect
     */
    public function postAdd(EventRequest $request)
    {
        $this->calendarService->createEvent($request->all());

        return json_encode(true);
    }

    /**
     * Saves a edited event.
     * @param  EventRequest $request [description]
     * @return [type]                [description]
     */
    public function editPost(EventRequest $request)
    {
        $event = Events::where('id', $request->id)->first();
        $event->title = $request->title;
        $event->description = $request->description;
        
        if($request->all_day == true)
        {
            $event->all_day = 1;
        } else {
            $event->all_day = 0;
        }

        $event->start = $request->start;

        if(empty($request->end))
        {
        $event->end = null;
        } else { 
        $event->end = $request->end;
        }

        $event->save();
        
        return redirect()->back();
    }

    public function deletePost($id)
    {
        $event = Events::where('id', $id)->first();
        
        if(count($event) > 0){ $event->delete(); }    
    }

    public function getEvent($id)
    {
        return $this->calendarService->getCalendar($id);
    }

    /**
     * Update an event drag.
     *
     * @param Request $request
     *
     * @return json
     */
    public function updateEvent(Request $request)
    {
        $this->calendarService->updateDragEvent($request->get('id'), $request->input());

        return json_encode(true);
    }

    /**
     * Update an repeated event.
     *
     * @param Request $request
     *
     * @return json
     */
    public function updateEventRepeatedAll(Request $request)
    {
        $this->calendarService->updateDragEventRepeatedAll($request->get('event-id-move-repeat'), $request->input());

        return json_encode(true);
    }

    /**
     * Update an repeated event only for this.
     *
     * @param Request $request
     *
     * @return json
     */
    public function updateEventRepeatedOnlyThis(Request $request)
    {
        $this->calendarService->updateDragEventRepeatedOnlyThis($request->get('event-id-move-repeat'), $request->input());

        return json_encode(true);
    }

    /**
     * Return events json.
     *
     * @param Request $request
     *
     * @return json
     */
    public function getJson(Request $request)
    {
        echo $this->calendarService->getAllEventsAsJson($request->get('start'), $request->get('end'));
    }

    /**
     * Allow replace the default views by placing a view with the same name.
     * If no such view exists, load the one from the package.
     *
     * @param $first_view
     * @param $second_view
     * @param array $information
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function firstViewThatExists($first_view, $second_view, $information = [])
    {
        // load the first view if it exists, otherwise load the second one
        if (view()->exists($first_view)) {
            return view($first_view, $information);
        } else {
            return view($second_view, $information);
        }
    }
}
