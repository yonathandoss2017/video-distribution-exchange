const VIDEO_PUBLISHED = "1";

$(document).ready(function(){
    var utcTimezone = 'UTC';
    var scheduled_date_input = $('#scheduled_date_input');
    var scheduled_time_input = $('#scheduled_time_input');
    var currentTimeZoneName = moment.tz.guess();
    $('form').on('submit', function(e) {
        e.preventDefault();

        if (!is_published) {
            // Convert current time to GMT Time
            // Convert time from: m/d/Y to Y-m-d
            var dateArr = scheduled_date_input.val().split('/');
            var newDateFormat = dateArr[2] + '-' + dateArr[0] + '-' + dateArr[1];

            var datetimeValue = newDateFormat + ' ' + scheduled_time_input.val();
            var timeArr = convertTimezone(currentTimeZoneName, utcTimezone, datetimeValue);

            $('#scheduled_date').val(timeArr[0]);
            $('#scheduled_time').val(timeArr[1]);
        }

        this.submit();

    });

    $('.input-group.date').datepicker({
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: true,
        autoclose: true,
    });

    var gmtzone = moment(getCurrentTime(), "hh:mm:ss").tz(currentTimeZoneName).format('ZZ');
    $('#gmtzone').html(' ' + gmtzone);

    // Convert to Current timezone of user
    if (is_edit_form && is_published == VIDEO_PUBLISHED) {
        var datetimeValue = $('#scheduled_datetime').val();
        var timeArr = convertTimezone(utcTimezone, currentTimeZoneName, datetimeValue);
        var localTime = timeArr[0] + ' ' + timeArr[1];
        localTime = moment(localTime).tz(currentTimeZoneName).format('MMM DD, YYYY h:mm A');
        $('#published_datetime').html(localTime);   // Aug 18, 2017 11:02 AM
    } else {
        restoreTimezoneLocal();
    }

    function restoreTimezoneLocal() {
        var datetimeValue = $('#scheduled_date').val() + ' ' + $('#scheduled_time').val();
        if (datetimeValue == " ") {
            return false;
        }
        var timeArr = convertTimezone(utcTimezone, currentTimeZoneName, datetimeValue);

        // Convert time from: Y-m-d to m/d/Y
        var date = timeArr[0].split('-');
        var newDateFormat = date[1] + '/' + date[2] + '/' + date[0];

        scheduled_date_input.val(newDateFormat);
        scheduled_time_input.val(timeArr[1]);

        $('.input-group.date').datepicker('update', scheduled_date_input.val());
    }

    function convertTimezone(fromTimezone, toTimezone, datetimeValue, format) {
        if (format == undefined) {
            format = 'YYYY-MM-DD HH:mm';
        }
        var timeArr = [];
        if (toTimezone == utcTimezone) {
            // Convert from UTC/GMT to local time
            var localTime = moment(datetimeValue).tz(toTimezone).format(format);
            timeArr = localTime.split(' ');
        } else {
            // Convert from local time to UTC/GMT
            var gmtDateTime = moment.utc(datetimeValue, format)
            var utc = gmtDateTime.local().format(format);
            timeArr = utc.split(' ');
        }

        return timeArr;
    }

    function getCurrentTime() {
        var currentdate = new Date();
        return currentdate.getFullYear() + '-' +
                (currentdate.getMonth()<10?"0":"") + currentdate.getMonth() + '-' +
                (currentdate.getDay()<10?"0":"") + currentdate.getDay() + ' ' +
                currentdate.getHours() + ':' +
                currentdate.getMinutes() + ':' +
                currentdate.getSeconds();
    }

});
