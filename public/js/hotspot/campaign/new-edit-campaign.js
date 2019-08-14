var endAt;
var startAt;
var currentTimeZone;
var abbr;
var utcTimezone = 'UTC';

$(function() {
    endAt = $('#end_at');
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

    var requireRestore = (endAt.val() != "") || (startAt.val() != "");
    if (!requireRestore) {
        if (is_edit_form) {
            restoreDatetimeEdit(endAt);
            restoreDatetimeEdit(startAt);
        } else {
            restoreFromOldData();
        }
    } else {
        restoreFromOldData();
    }

    function restoreFromOldData() {
        if (endAt.val() != "") {
            restoreDateTimeCreate(endAt);
        }
        if (startAt.val() != "") {
            restoreDateTimeCreate(startAt);
        }
    }

    function restoreDatetimeEdit(element) {
        // Restore date and time from timestamp
        var timestamp = element.attr('data-timestamp') * 1000;
        var time = moment(new Date(timestamp)).tz(currentTimeZone);
        var elementId = element.attr('id');

        var dateInput = $('#' + elementId + '_date_input');
        var timeInput = $('#' + elementId + '_time_input');

        dateInput.val(time.format('MM/DD/YYYY'));
        timeInput.val(time.format('HH:mm'));
        $('.input-group.date').datepicker('update');
    }

    function restoreDateTimeCreate(element) {
        var datetime = convertTimezone(utcTimezone, currentTimeZone, element.val(), 'YYYY-MM-DD HH:mm:ss');
        datetime = new Date(datetime);

        var elementId = element.attr('id');
        var dateInput = $('#' + elementId + '_date_input');
        var timeInput = $('#' + elementId + '_time_input');

        var date = (datetime.getMonth()<9?"0":"") + (datetime.getMonth() + 1) + '/' +
                    (datetime.getDate()<10?"0":"") + datetime.getDate() + '/' +
                    datetime.getFullYear();
        var time = (datetime.getHours()<10?"0":"") + datetime.getHours() + ':' +
                    (datetime.getMinutes()<10?"0":"") + datetime.getMinutes();

        dateInput.val(date);
        timeInput.val(time);
        $('.input-group.date').datepicker('update');
    }

    $('form').on('submit', function(e) {
        e.preventDefault();

        fillDateTime(startAt);
        fillDateTime(endAt);
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
            var gmtDateTime = moment.utc(datetimeValue, format)
            var utc = gmtDateTime.local().format(format);
            dateTime = utc;
        }

        return dateTime;
    }

});

