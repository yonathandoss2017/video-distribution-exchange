var start_at_date_input = $('#start_at_date_input');
var start_at_time_input = $('#start_at_time_input');
var currentTimeZone;
var abbr;
var utcTimezone = 'UTC';
var startNow = $('#start_now');
var startSchedule = $('#start_schedule');
var streamSchedule = $('#data_1');
var startMothod = $('input[name=start_method]:checked');

startNow.click(function(){
    if (!streamSchedule.hasClass('hidden')) {
        streamSchedule.addClass('hidden');
    }
});
startSchedule.click(function(){
    if (streamSchedule.hasClass('hidden')) {
        streamSchedule.removeClass('hidden');
    }
});

if(startNow.is(":checked")){
    if (!streamSchedule.hasClass('hidden')) {
        streamSchedule.addClass('hidden');
    }
}

$(function() {
    startAt = $('#start_at');
    currentTimeZone = moment.tz.guess();
    abbr = moment.tz(currentTimeZone).format('ZZ');

    $('.input-group.date').datepicker({
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: true,
        autoclose: true
    });

    $('span[id^="gmtzone"]').each(function(pos, item) {
        $(item).html(' ' + abbr);
    });

    var realDate = $('#real_date');

    if (realDate.val()) {
        var gmtDateTime = moment.utc(realDate.val(), 'YYYY-MM-DD HH:mm');
        var utc = gmtDateTime.local().format('HH:mm');
        $('#start_at_time_input').val(utc);
    }

    $('form').on('submit', function(e) {
        e.preventDefault();
        if ($('input[name=start_method]:checked').val() == 'schedule') {
            fillDateTime(startAt);
        }
        this.submit();
    });

    function fillDateTime(element) {
        var elementId = element.attr('id');
        var dateInput = $('#' + elementId + '_date_input');
        var timeInput = $('#' + elementId + '_time_input');

        // Convert time from: Y-m-d to m/d/Y
        var dateArr = dateInput.val().split('/');
        var newDateFormat = dateArr[2] + '-' + dateArr[0] + '-' + dateArr[1];

        // Convert from local timezone to UTC time
        var dateTimeLocal = newDateFormat + ' ' + timeInput.val();
        var dateTimeUtc = convertTimezone(currentTimeZone, utcTimezone, dateTimeLocal);

        element.val(dateTimeUtc + ':00');
    }

    function convertTimezone(fromTimezone, toTimezone, datetimeValue, format) {
        if (format == undefined) {
            format = 'YYYY-MM-DD HH:mm';
        }
        var dateTime;
        if (toTimezone == utcTimezone) {
            // Convert from UTC/GMT to local time
            var localTime = moment(datetimeValue).tz(toTimezone).format(format);
            dateTime = localTime;
        } else {
            // Convert from local time to UTC/GMT
            var gmtDateTime = moment.utc(datetimeValue, format);
            var utc = gmtDateTime.local().format(format);
            dateTime = utc;
        }

        return dateTime;
    }

});