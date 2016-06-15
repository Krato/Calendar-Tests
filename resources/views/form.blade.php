

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if(isset($modelForm))
    <form action="save" method="POST" id="form-create" role="form">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        {{-- Model Bind --}}
        
        <div class="form-group">
            <label for="description">{{ config('calendar.modelLabel', 'Model')}}</label>
            <select name="model_id" id="model_id" class="form-control" >
                    <option value="{{ $model->id }}">{{ $model->$modelColumn }}</option>
            </select>
        </div>
        {{-- Colors --}}
        <div class="row">
            <div class="col-md-10">
                <div class="form-group ">
                    <label for="class">Clase</label>
                    <select name="color" id="color" class="form-control" onchange="CalendarEvents.changeClassColor()">
                        @for ($i = 1; $i < 10; $i++)
                            <option value="fc-event-color{{$i}}" {{ ($colorModel->getColor($model->id) == 'fc-event-color'.$i) ? 'selected' : '' }}>Color {{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div id="show-box" class="{{ ($colorModel->getColor($model->id) != '') ? $colorModel->getColor($model->id) : 'fc-event-info   '  }}">
                </div>
            </div>
        </div>
        <a type="button" class="btn btn-default" href="{{ url('calendar/model') }}">Cancel</a>
        <input type="submit" class="btn btn-primary" value="{!! trans('calendar-events::calendar-events.save') !!}" />
        
    </form>
@else
    @if(isset($calendarEvent))
    <form action="{{ $action }}" method="POST" id="form-update" role="form">
        <input type="hidden" name="_method" value="PUT" />
@else
    <form action="{{ $action }}" method="POST" id="form-create" role="form">
@endif
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        {{-- Title field --}}
        <div class="form-group">
            <label for="title">*{!! trans('calendar-events::calendar-events.title') !!}</label>
            <input
                    name="title"
                    type="text"
                    class="form-control"
                    placeholder="{!! trans('calendar-events::calendar-events.title') !!}"
                    value="{{ isset($calendarEvent) ? $calendarEvent->title : null }}"
            />
        </div>

        {{-- Description --}}
        <div class="form-group">
            <label for="description">{!! trans('calendar-events::calendar-events.description') !!}</label>
            <textarea
                    name="description"
                    class="form-control"
                    placeholder="{!! trans('calendar-events::calendar-events.your_text_here') !!}"
                    id="description"
                >{{ isset($calendarEvent) ? $calendarEvent->description : null }}
            </textarea>
        </div>

        {{-- Model Bind --}}
        <div class="row">
            <div class="col-md-10">
                <div class="form-group">
                    <label for="description">{{ config('calendar.modelLabel', 'Model')}}</label>
                    <select name="model_id" id="model_id" class="form-control" onchange="CalendarEvents.changeClassColor('model_id', 'color')">
                        <option value="">Select an {{ config('calendar.modelLabel', 'Model')}}</option>
                        @foreach($model as $item)
                            <option value="{{ $item->id }}" data-color="{{ $colorModel->getColor($item->id) }}">{{ $item->$modelColumn }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div id="show-box" class="">
                </div>
            </div>
        </div>
        {{-- All day check box --}}
        <div class="form-group checkbox check-default">
        <label for="all-day" class="">
            <input
                    type="checkbox"
                    name="all_day"
                    value="true"
                    id="all-day" onchange="CalendarEvents.allDayToggle();"
                    {{ (isset($calendarEvent) && true == $calendarEvent->all_day) ? 'checked=\"\"' : null }}
            />
            {!! trans('calendar-events::calendar-events.all_day') !!}
        </label>
        </div>

        {{-- Start date --}}
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="start">*{!! trans('calendar-events::calendar-events.start') !!}</label>
                    <input
                            type="text"
                            name="start"
                            placeholder="{!! trans('calendar-events::calendar-events.date') !!}"
                            id="start"
                            class="form-control"
                            value="{{ isset($calendarEvent) ? date('Y-m-d H:i', strtotime($calendarEvent->start)) : null }}"
                    />
                </div>
            </div>
            {{-- End date --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="end">{!! trans('calendar-events::calendar-events.end') !!}</label>
                    <input
                            type="text"
                            name="end"
                            placeholder="{!! trans('calendar-events::calendar-events.end') !!}"
                            id="end"
                            class="form-control"
                            value="{{ (isset($calendarEvent) && false == $calendarEvent->all_day) ? strtotime('Y-m-d H:i', $calendarEvent->end) : null }}"
                    />
                </div>
            </div>
        </div>

        {{-- Reapeat event checkbox --}}
        <div class="form-group checkbox check-default">
            <label for="repeat">
                <input
                        type="checkbox"
                        name="repeat"
                        id="repeat"
                        class="checkbox check-default"
                        {{ (isset($calendarEvent) && $calendarEvent->calendarEventRepeatDates()->count() > 0) ? 'checked=\"\"' : null }}
                />
                {!! trans('calendar-events::calendar-events.repeat_event') !!}
            </label>
        </div>

        <input type="submit" value="{!! trans('calendar-events::calendar-events.save') !!}" />
    </form>
@endif

