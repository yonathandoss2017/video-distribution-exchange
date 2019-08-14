$(document).ready(function(){
    const lang = document.documentElement.lang.substr(0, 2);
    var locale = 'en'
    var dateFormat = 'MMM D, YYYY'
    if (lang == 'zh') {
        locale = 'zh-cn'
        dateFormat = 'LL'
    }
    $('small.timestamp').each(function() {
        setTime($(this).attr('id'), $(this).attr('dt'));
    });

    function setTime(attrId, date){
        abbr = ' GMT ' + moment.tz(moment.tz.guess()).format('ZZ');
        time = moment(new Date(date*1000)).tz(moment.tz.guess()).locale(locale);
        $('#' + attrId+'-'+date).text( time.format(dateFormat) );
        $('#' + attrId).text( time.format('HH:mm') +' '+ abbr);
    }
    
});