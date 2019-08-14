
(function(global,d){
	var debugGA = false; //keep this here during development phase.
	if (debugGA) {
   (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics_debug.js','ga');
	}
	else {
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
	}

/*
	metric1 outline appeared
	metric2 graphic appeared / viewed graphic (clicked )
	metric3 visited website
		if wanna see the distinction then can still filter by action
	metric4 video played 
		action: "hotspot|bannner|vertrect" (this is for filtering reasons in reports)
		label: entry id + video title: unique to the session
		category: SP URL: unique to the session 
		dimension1: ''
*/
	var glongId = '';
	var gDoGA = true;
	var gHostname = global.location.hostname;
    var gVideoLabel = '';
    var gUA = '';
    var gTrackerNames = {};

    var domain = {
				"beta.pocketimes.my"  : "UA-90565141-6",
				"pocketimes.my"       : "UA-77307024-9",
				"others"              : "UA-90565141-6"
	};
    /*
    var m1 = 0;
    var m2 = 0;
    var m3 = 0;
    var m4 = 0;
    */

    function getMetricName(type, action) {
	    if (type == 0) { //video start. Dunno whom to send to.
    	    return 'metric4';
    	}
    	if (type == 1 || type == 3) { //banner or vert rect
        	if (action == 0) //mere viewing
            	return 'metric2'; //graphic appears (assumed it viewed)
            else if (action == 2)
            	return 'metric3'; //Visited site
        	//else
        	//debug_alert("should not be here getMetricName (B) ");
    	}
    	else if (type == 2) { //hotspot
        	if (action == 0) //mere viewing faint rect
            	return 'metric1';
        	else if (action == 1)
            	return 'metric2'; //viewing the graphic
        	else if (action == 2)
            	return 'metric3'; //visiting the site
        	//else
            	//debug_alert("should not be here getMetricName (C) ");
    	}

	}
	function getCustomObject(type, action) {
		var retObj = {
			metric1 : 0,
			metric2 : 0,
			metric3 : 0,
			metric4 : 0
		};
		/*
		var str = getMetricName(type, action);
		if (str == 'metric1')
			m1 += 1;
		else if (str == 'metric2')
			m2 += 1;
		else if (str == 'metric3')
			m3 += 1;
		else if (str == 'metric4')
			m4 += 1;
		*/
		retObj[getMetricName(type, action)] = 1;
		return retObj;
	}

	function ga_createwithlog( trackerId, trackerName) {
		console.log(trackerId + " " + trackerName);
		if (debugGA)
			window.ga_debug = {trace: true};
		ga('create', trackerId, 'auto', {'name': trackerName });		
		if (debugGA && location.hostname == 'localhost') 
 			 ga('set', 'sendHitTask', null);
	}		

	var string4AdType = [ "", "banner", "hotspot", "vertrect"];
	
	global.gaTrackHotspots = {	
		doGA : function doGA() {
			return gDoGA;
		},
		//printDump : function printDump() {
		//	alert("m1 m2 m3 m4 " + m1 + " " + m2 + " " + m3 + " " + m4);
		//},
		//disableGA : function disableGA() {
		//	alert("GA is disabled!")
		//	gDoGA = false;
		//},
		init : function init(entryId, videoTitle, longId) {
			for(var c in domain){
				if( (new RegExp( c+"$","i" ) ).test(global.location.hostname)){
					gUA = domain[c];
					break;
				}
			}
			if (gUA == '')
				gUA = domain['others'];
			gVideoLabel = entryId + "|" + videoTitle;
			
			glongId = longId;
			
			//only need the destUA and the type if type == 0
			this.registerEvent(0);
		},

		/*
	 	.type (1 for banner, 2 for hotspot etc)
	 	.action (about clicking and stuff)
	 	.title: name of the hotspot/banner 
	 	.starttime: starttime (msec) of the hotspot/banner
	 	.campaign: (optional) the campaign associated with this hotspot/banner
	 	.destination UA  (optional) any particular trackerID to send to?)
	 	*/
		registerEvent : function registerEvent(type, action, title, starttime, campaign) {	 
			//if there is a destUA and a gUA and they are both valid and different trackers, maybe
			//we should consider to send to both. Can be easily changed to that here.
			//if (destUA == '' && gUA == '') return; //nothing to do.
			//var thisUA = (destUA != '' ? destUA : gUA);
			
			if (gUA == '') return; //nothing to do.
			var thisUA = gUA;
			
	    	if (gTrackerNames[thisUA] == undefined) {
	    		gTrackerNames[thisUA] = "Tracker" + Math.floor(Math.random() * 100000000000);
				thisUAName = gTrackerNames[thisUA];
				ga_createwithlog(thisUA, thisUAName);
			}

			var customObj = getCustomObject(type, action);
			customObj[getMetricName(type, action)] = 1;
			customObj['dimension1'] = (type == 0 ? '' : campaign);
			customObj['dimension2'] = glongId;
			
			if (type == 0){ //special case:
				ga( thisUAName+'.send', 'event',
					gHostname, 
					"hotspot|banner|vertrect",  //This is a special event 
					gVideoLabel,
					1,
					customObj);
				return;
			
			}
			
			ga( thisUAName+'.send', 'event',
				gHostname, 
				string4AdType[type] + "|" + title + "|" + starttime,
				gVideoLabel,
				1,
				customObj);

		}//function registerEvent
	}

})(window,document); 

