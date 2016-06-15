var CalendarEvents = {
    /**
     * The init method. Sets the states of the blocks.
     */
    init: function () {
        this.allDayToggle();
        this.fixCheckBoxOnModal();
    },

    /**
     * Toggles all day on and off
     */
    allDayToggle: function () {
        if ($('#all-day').prop('checked')) {
            $('#end').prop('disabled', true);
        } else {
            $('#end').removeAttr('disabled');
        }
    },
    /** Change color of box based of given or not given input and attribute */
    changeClassColor: function($input = false, $dataAttr = false){
        var $input;
        if($input){
            var type = ($dataAttr) ? $('#'+$input).find(':selected').data($dataAttr) : $('#'+$input).val()
        } else {
            var type = ($dataAttr) ? $('#color').find(':selected').data($dataAttr) : $('#color').val()
        }
        console.log(type);
        $('#show-box').removeClass().addClass(type);
    },
    //Fix checkbox on Modal
    fixCheckBoxOnModal: function (){
        jQuery(".modal input:checkbox,.modal label").on("click", function(e){
            e.stopImmediatePropagation();
            var element = (e.currentTarget.htmlFor !== undefined) ? e.currentTarget.htmlFor : e.currentTarget;
            var checked = (element.checked) ? false : true;
            element.checked = (checked) ? false : checked.toString();
        });
    },
    refreshCalendarSource: function(){
        $('#calendar').fullCalendar( 'refetchEvents' );
    },

    createEventAjax: function(action){
        var serialized = $("#form-create").serialize();
        jQuery.ajax({
            url: action,
            type: "POST",
            data: serialized
        }).done(function( data ) {
            $('.modal').modal('hide');
            CalendarEvents.refreshCalendarSource();
        }).fail(function(data) {
            console.log(data);
        });
    },

    moveJustThisEventRepeat: function(action){
        var serialized = $("#form-move-repeated").serialize();
        jQuery.ajax({
            url: action,
            type: "POST",
            data: serialized
        }).done(function( data ) {
            $('.modal').modal('hide');
            CalendarEvents.refreshCalendarSource();
        }).fail(function(data) {
            console.log(data);
        });
    },

    moveAllEventRepeat: function(action){
        var serialized = $("#form-move-repeated").serialize();
        jQuery.ajax({
            url: action,
            type: "POST",
            data: serialized
        }).done(function( data ) {
            $('.modal').modal('hide');
            CalendarEvents.refreshCalendarSource();
        }).fail(function(data) {
            console.log(data);
        });
    },

    

}

$(document).ready(function () {


    // $.datetimepicker.setDateFormatter({
    //     parseDate: function (date, format) {
    //         var d = moment(date, format);
    //         return d.isValid() ? d.toDate() : false;
    //     },
    //     formatDate: function (date, format) {
    //         return moment(date).format(format);
    //     }
    // });

    jQuery('#end').datetimepicker({
      format: formatDate,
      closeOnDateSelect: true,
      timepickerScrollbar: false
    });

    jQuery('#start').datetimepicker({
        format: formatDate,
        closeOnDateSelect: true,
        timepickerScrollbar: false,
        onSelectDate: function(ct,$i){
            jQuery('#end').datetimepicker({
                'defaultDate' : ct
            });
        }
    });

});