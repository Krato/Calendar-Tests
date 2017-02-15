@extends('layouts.default')
@section('styles')

    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.7.2/fullcalendar.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('calendar_assets/css/custom-calendar.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('calendar_assets/css/dates.css') }}">
    <link rel="stylesheet" type="text/css" media="print" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.7.2/fullcalendar.print.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.min.css">
    <link rel="stylesheet" href="{{ asset('calendar_assets/css/types.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.1/jquery-ui-timepicker-addon.min.css">

@endsection
@section('content')


    <div id="addEvent" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add event</h4>
                </div>
                <div class="modal-body">
                    @if( view()->exists('vendor.infinety.calendar.form') )
                        @include('vendor.infinety.calendar.form', ['action' => 'post', 'model' => $modelData, 'modelColumn' => $modelColumn])
                    @else
                        @include('calendar:calendar.form', ['action' => 'post', 'model' => $modelData, 'modelColumn' => $modelColumn])
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" id="create-event-modal" class="btn btn-primary" onclick="CalendarEvents.createEventAjax('{{ url('calendar/post') }}')">Add event</button>
                </div>
            </div>
        </div>
    </div>

    <div id="moveRepeatEventModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Updating event</h4>
                </div>
                <div class="modal-body">
                    <h3>Do you want to move all repeated event, or just this event?</h3>
                    <form role="form" id="form-move-repeated">
                        <input type="hidden" name="event-id-move-repeat" id="event-id-move-repeat">
                        <input type="hidden" name="event-id-moved-day-start" id="event-id-move-day-start">
                        <input type="hidden" name="event-id-moved-day-end" id="event-id-move-day-end">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" onclick="CalendarEvents.moveJustThisEventRepeat('{{ url('calendar/moveThis') }}')">Just this</button>
                    <button type="button" id="create-event-modal" class="btn btn-primary" onclick="CalendarEvents.moveAllEventRepeat('{{ url('calendar/moveAll') }}')">Move all</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="calendar-page">
            <div class="calendar-page-content">
                <div class="calendar-page-title"></div>
                <div class="calendar-page-content-in">
                    <div id='calendar'></div>
                </div><!--.calendar-page-content-in-->
            </div><!--.calendar-page-content-->

            <div class="calendar-page-side">
                
                <div class="">
                    <div id="month-picker"></div>
                </div>
                <section class="calendar-page-side-section">
                    <header class="box-typical-header-sm">Filters</header>
                    <div class="calendar-page-side-section-in">
                        <ul class="colors-guide-list">
                        <?php 
                            $modelBindData = $colorModel->lists('model_id')->toArray();
                         ?>
                        @if(count($modelData) > 0 )
                            @foreach($modelData as $item)
                                @if(count($colorModel->events($item->id)) > 0)
                                    @if(in_array($item->id, $modelBindData))
                                        <li data-model="{{ $item->id }}" class="filter">
                                            <div class="color-double pointer {{ $colorModel->getColor($item->id) }}"> <div></div></div>{{ $item->$modelColumn }}
                                        </li>
                                @endif
                                @endif
                            @endforeach
                        @endif
                        </ul>
                    </div>
                </section>
            </div><!--.calendar-page-side-->
        </div><!--.calendar-page-->
    </div>
    
@endsection
@section('scripts')
    

    
    <script src="https://code.jquery.com/ui/1.12.0-rc.2/jquery-ui.min.js" integrity="sha256-55Jz3pBCF8z9jBO1qQ7cIf0L+neuPTD1u7Ytzrp2dqo=" crossorigin="anonymous"></script>
    

    <script type="text/javascript" src="https://rawgit.com/KidSysco/jquery-ui-month-picker/v3.0.0/demo/MonthPicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.1/jquery-ui-timepicker-addon.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.1/i18n/jquery-ui-timepicker-addon-i18n.min.js"></script>
    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.7.2/fullcalendar.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.js"></script>

    <script type="text/javascript" src="{{ asset('calendar_assets/js/datetimepicker/jquery.moment.datetimepicker.min.js') }}"></script>
    
    <script type="text/javascript" src="{{ asset('calendar_assets/js/calendar-events.js') }}"></script>

    <script>var formatDate = "{{ config('calendar.formatDate', 'Y-m-d H:i')}}"; </script>

    <script>
    $(document).ready(function () {



        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });
        CalendarEvents.init();
        var Calendar = $('#calendar');
        var Picker = $('#month-picker');
        var isPopOverOpen = false;
        var dateChosen;
        var modelFilterValue = null;
        $.timepicker.setDefaults($.timepicker.regional['es']);

        Calendar.fullCalendar({
            lang: 'es',
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            nextDayThreshold: '00:00:00', // 9am
            editable: true,
            selectable: true,
            eventLimit: 3, // allow "more" link when too many events
            eventSources: [
                {
                    url: '{{ url('calendar/json') }}'
                }
            ],
            viewRender: function(view, element) {

                // // Update monthpicker on change if init
                // if (Picker.hasClass('hasMonthpicker')) {
                //     var selectedDate = Calendar.fullCalendar('getDate');
                //     var formatted = moment(selectedDate, 'MM-DD-YYYY').format('MM/YY');
                //     Picker.monthpicker("setDate", formatted);
                // }
                // // Update mini calendar title
                // var titleContainer = $('.fc-title-clone');
                // if (!titleContainer.length) {
                //     return;
                // }
                // titleContainer.html(view.title);
                // $('.fc-popover.click').remove();
            },
            droppable: true, // allows things to be dropped onto the calendar
            drop: function ( date, jsEvent, ui, resourceId ) {
                
                
            },
            eventReceive: function( event ) { 
               
            },
            eventDrop: function(event, delta, revertFunc, jsEvent, ui, view ) {

                if(event.repeat == true){

                    $('#moveRepeatEventModal').find('#event-id-move-repeat').val(event.id);
                    $('#moveRepeatEventModal').find('#event-id-move-day-start').val(event.start.format(formatDate));
                    $('#moveRepeatEventModal').find('#event-id-move-day-end').val(event.end.format(formatDate));

                    $('#moveRepeatEventModal').modal('show');
                    $('#moveRepeatEventModal').on('hidden.bs.modal', function (e) {
                        revertFunc();
                    })
                    return false;
                }

                if(event.allDay == true){
                    var dataToUpdate = {
                        id : event.id,
                        start : moment(event.start).hour(00).minutes(00).seconds(00).format(formatDate),
                        end : moment(event.end).subtract(1, 'days').hour(23).minutes(59).seconds(59).format(formatDate)
                    }
                } else {
                    var dataToUpdate = {
                        id : event.id,
                        start : moment(event.start).format(formatDate),
                        end : moment(event.end).format(formatDate)
                    }
                }

                jQuery.ajax({
                    url: "{{ url('calendar/updateDrop') }}",
                    type: "POST",
                    data: dataToUpdate
                }).done(function( data ) {
                    console.log(data);
                }).fail(function(data) {
                    console.log(data);
                });

            },
            eventResize: function(event, delta, revertFunc) {
                var dataToUpdate = {
                    id : event.id,
                    start : event.start.toISOString(),
                    end : event.end.toISOString()
                }

                jQuery.ajax({
                    url: "{{ url('calendar/updateDrop') }}",
                    type: "POST",
                    data: dataToUpdate
                }).done(function( data ) {
                    console.log(data);
                }).fail(function(data) {
                    console.log(data);
                });

            },
            eventRender: function (event, element) {
                if(modelFilterValue != null){
                    return ['all', event.model_id].indexOf(modelFilterValue) >= 0
                }
                
            },
            eventClick: function(event, jsEvent, view){
                var eventEl = $(this);
                // Add and remove event border class
                if (!$(this).hasClass('event-clicked')) {
                    $('.fc-event').removeClass('event-clicked');
                    $(this).addClass('event-clicked');
                }

                if(isPopOverOpen == event){
                    $('.fc-popover.click').remove();
                    $('.fc-event').removeClass('event-clicked');
                    isPopOverOpen = false;
                    return false;
                }

                // Add popover
                var popoverHtml = 
                    '<div class="fc-popover click">' +
                        '<div class="fc-header">' +
                            moment(event.start).format('dddd • D') +
                            '<button type="button" class="cl"><i class="fa fa-close"></i></button>' +
                        '</div>' +

                        '<div class="fc-body main-screen">';
                            //Check if event is allDay
                            if(event.allDay == false){
                popoverHtml+='<p>' +
                                '<strong>Start</strong>: '+ moment(event.start).format('HH:mm') + ' - ' +
                                '<strong>End</strong>: '+ moment(event.end).format('HH:mm') +
                            '</p>';
                            } else {
                popoverHtml+='<p><strong>Full day event</strong>';              
                            }

                            if(event.repeat == true){
                popoverHtml+='<p>Repeat event all <strong>'+ moment(event.start).format('dddd') +'</strong>';                            
                            }
                popoverHtml+='<p class="color-blue-grey">'+ event.description +'</p>' +
                            '<ul class="actions list-inline">' +
                                '<li><a href="#">More details</a></li>' +
                                '<li><a href="#" class="fc-event-action-remove">Remove</a></li>' +
                            '</ul>' +
                        '</div>' +

                        '<div class="fc-body remove-confirm">' +
                            '<p>Are you sure to remove event?</p>' +
                            '<div class="text-center">' +
                                '<button type="button" class="btn btn-rounded btn-sm btn-danger">Yes</button>' +
                                '<button type="button" class="btn btn-rounded btn-sm btn-primary remove-popover">No</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>';

                $('body').append(popoverHtml);



                // Position popover
                function posPopover(){
                    $('.fc-popover.click').css({
                        left: eventEl.offset().left + eventEl.outerWidth()/2,
                        top: eventEl.offset().top + eventEl.outerHeight()
                    });
                    isPopOverOpen = event;
                }

                posPopover();

                $('.fc-scroller, .calendar-page-content, body').scroll(function(){
                    posPopover();
                    
                });

                $(window).resize(function(){
                   posPopover();
                });
                

                // Remove old popover
                if ($('.fc-popover.click').length > 1) {
                    for (var i = 0; i < ($('.fc-popover.click').length - 1); i++) {
                        $('.fc-popover.click').eq(i).remove();
                    }
                }

                // Close buttons
                $('.fc-popover.click .cl, .fc-popover.click .remove-popover').click(function(){
                    $('.fc-popover.click').remove();
                    $('.fc-event').removeClass('event-clicked');
                });

                /* Remove option */
                
            },
            dayClick: function(date, jsEvent, view) {

                // console.log('Clicked on: ' + date.format());

                // console.log('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);

                // console.log('Current view: ' + view.name);
                // $('#addEvent').modal({remote: "{{ url('calendar/add') }}" });
                

            },
            select: function( start, end, jsEvent, view){
               
                //Reset fields 
                $('#addEvent').find('#start').val('');
                $('#addEvent').find('#end').val('');
                $("#addEvent").find('#all-day').prop('checked', false);

                if( moment(end).isSame(moment(start).add(1, 'd')) ){

                    $('#addEvent').find('#start').val(start.hour(00).minutes(00).seconds(00).format(formatDate));

                    $("#addEvent").find('#all-day').prop('checked', true);
                    $('#addEvent').find('#end').prop('disabled', true);

                } else if( moment(end).isAfter(moment(start).add(1, 'd') ) ) {
                    $("#addEvent").find('#all-day').prop('checked', true);
                    $('#addEvent').find('#end').prop('disabled', false);
                    $('#addEvent').find('#start').val(moment(start).hour(00).minutes(00).seconds(00).format(formatDate));
                    $('#addEvent').find('#end').val(moment(end).subtract(1, 'days').hour(23).minutes(59).seconds(59).format(formatDate));
                } else {
                    $('#addEvent').find('#start').val(start.format(formatDate));
                    $('#addEvent').find('#end').val(end.format(formatDate));
                    $("#addEvent").find('#all-day').prop('checked', false);
                    $('#addEvent').find('#end').prop('disabled', false);
                }
                $('#addEvent').modal('show');

            }
        });


        



        /* ==========================================================================
        Side datepicker
        ========================================================================== */

        Picker.MonthPicker({
            i18n: {
                year: 'Year',
                prevYear: 'Previous Year',
                nextYear: 'Next Year',
                next12Years: 'Jump Forward 12 Years',
                prev12Years: 'Jump Back 12 Years',
                nextLabel: '<i class="fa fa-chevron-right"></i>',
                prevLabel: 'Prev',
                buttonText: 'Open Month Chooser',
                jumpYears: 'Jump Years',
                backTo: 'Back to',
                months: ['Jan.', 'Feb.', 'Mar.', 'Apr.', 'May', 'June', 'July', 'Aug.', 'Sep.', 'Oct.', 'Nov.', 'Dec.']
            },
            OnAfterChooseMonth: function (selectedDate) {
                Calendar.fullCalendar('gotoDate', selectedDate)
            }
        });

        // Init FullCalendar events
        $('#external-events .fc-event').each(function () {
            // Calendar data stored until drop event
            $(this).data('event', {
                title: $.trim($(this).text()), // element text = event title
                stick: true,
                className: 'fc-event-' + $(this).attr('data-event')
            });

            // make the event draggable
            $(this).draggable({
                zIndex: 999,
                revert: true,
                revertDuration: 0 //  After drag position
            });

        });

        $('body').on('click', '.filter', function(){
            if(modelFilterValue == $(this).data('model')){
                modelFilterValue = null;
            } else {
                modelFilterValue = $(this).data('model');
            }
            CalendarEvents.refreshCalendarSource();
        });

        $('body').on('click', function(e){
            if($(e.target).hasClass('fc-event')){
                return false;
            }
            if($(e.target).hasClass('fc-content')){
                return false;
            }
            if($(e.target).parent().hasClass('fc-content')){
                return false;
            }
            if($(e.target).prev().hasClass('fc-content')){
                return false;
            }
            $('.fc-popover.click').remove();
            $('.fc-event').removeClass('event-clicked');
            return false;
        }).on('click', '.fc-popover.click', function(e){
            e.stopPropagation();
        });




    });
</script>
@endsection


