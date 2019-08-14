/**
 * Created by viscovery on 2016/5/9.
 */

(function($){

    // 播放器
    var savedplayerId = "";
    var hover; /* whether it is hover type player or not */
    var sliderHeight = 36; /* Will be queried properly later.  This is just a default */

    var kdp;
    var kdpiframe;
    var iframe;
    var TOP_OFFSET = 0, LEFT_OFFSET = 0;
    var PLAYER_WIDTH = 0, PLAYER_HEIGHT = 0;
    var ORIGIN_WIDTH = 0, ORIGIN_HEIGHT=0;

    // pre-loaded images (to increase performance)
    var imageSet = new Set();
    
    // 读取到的json2的结果
    var JSON_RESULT;
    // obj table
    var OBJ_TABLE;
    //定时器都放这个数组中
    var timeOutArray = [];
    // frame集合
    var jsonFrameSet = new Set();
    // 被移除的frame集合
    var removeFrameSet = new Set();
    // 存每一对 "frame: objects"
    var jsonResultObj = {};
    // 默认视频的宽高，以及放缩比例
    //var width, height, wrate, hrate;
    // 保存播放器状态
    var playerState;
    // 保存上一次广告时间
    var lastAdTime=0;
    var host = window.location.protocol+"//"+window.location.hostname;
    if (window.location.port.length !== 0){
        host+=":"+window.location.port;
    }
    var adCloseVisible = false;


    $( window ).resize(function(evt) {
        //scaleActiveBanner();
        if (evt.target.id == "hs_new") 
            ;
        else {
            //if we don't do that, when adSetCloseButtonPosision is called upon
            //e.g. normal --> fullscreen, the pic width if not accurate.
            //we often end up with half of the hotspot picture!
        setTimeout(function(){ 
            adSetCloseButtonPosision(); 
            computeVideoOffset();
            computeHSEWorkingAreaOffset();
        },500);
    }
    });

    function doGAMaybe(type, action, title, starttime, campaign) {   
        if (window.gaTrackHotspots!=undefined && window.gaTrackHotspots.doGA()) {
            window.gaTrackHotspots.registerEvent(type, action, title, starttime, 
               (campaign == '' ? "All" : campaign));
        }     
    }
                 
    var hslongId = '';
    var hsVideoTitle = 'VideoTitle'; //Tmp
    //Currently we use table in hsanalytics.js file
    /////var hsGATrackerId = 'UA-90565141-6'; //Will put in the mapping once the first step is good.
    //Now all send to my testing acct.
    var hsCSSPath = '';
    var hsHelperImagesPath = '';
    var hsJSONEndpointPath = '';
    var boxContaniner = jQuery('<div class="contaniner" ></div>').css('z-index','5').css('pointer-events', 'none');
    var redBoxContanier = jQuery('<div class="red-box"></div>').css({"position":"absolute","top":"0px","z-index": "9999","width":"100%","height":"94%","display":"none"});

    var getHelperCSSURL = function() {
        return hsCSSPath + 'hotspot-ad-style.css';
    }
    var getHelperImageURL = function(imagefile) {
        return hsHelperImagesPath + imagefile;
    }

    //the point is to let this hotspotSetParams gradually
    //replace the loose ones: e.g. hotspotSetServerBaseURL
    //e.g. hotspotSetVideoTitle (who knows what else is going to 
    //be needed!)
    //But in the interim, we still have to keep all of them.
    //Because the SP2 side's deployment is lagging (there are so many
    //WP sites)
    window.hotspotSetParams = function(paramObj) {
        if (paramObj.serverBaseURL != undefined) {
            var url = paramObj.serverBaseURL;
             if (url.slice(-1) != '/') 
                url = url + '/';
            hsJSONEndpointPath = url + 'hotspot/json/'
            hsCSSPath = url + 'css/hotspot/hseditor/';
            /////hsCSSPath = url + 'storage/hotspot/testing/';
            hsHelperImagesPath = url + 'images/hotspot/hseditor/';
        }
        if (paramObj.videoTitle != undefined) {
            hsVideoTitle = paramObj.videoTitle;    
        }
        //Not used as of now.
        //if (paramObj.gaTrackerId != undefined) {
          //  hsGATrackerId = paramObj.gaTrackerId;
        //}
        if (paramObj.entryId != undefined ||
            paramObj.longId != undefined) {
            hslongId = paramObj.longId;
            hotspotGetData(paramObj.entryId, paramObj.longId);
        }
        //By default GA is enabled
        if (paramObj.disableGA != undefined &&
            paramObj.disableGA == true) {
            if (window.gaTrackHotspots != undefined) 
                window.gaTrackHotspots.disableGA();
        }

    }            
    window.hotspotSetVideoTitle = function(title) {
        hsVideoTitle = title;
    }
    window.hotspotSetGATrackerId = function(id) {
        hsGATrackerId = id;
    }
    //From HSEditor side, called with 
    //hotspotSetServerBaseURL('../');
    //  OR
    //hotspotSetServerBaseURL('http://svc-staging.5kdg.com');
    //From WP side, called with hotspotSetServerBaseURL("http://svc-staging.5kdg.com");
    window.hotspotSetServerBaseURL = function(url) {
        //Hopefully this makes it a bit immune to failure under dir name changes,
        //as long as the dir name change is consistent across the js, css, images dirs.
        if (url.slice(-1) != '/') 
            url = url + '/';
        hsJSONEndpointPath = url + 'hotspot/json/'
        hsCSSPath = url + 'css/hotspot/hseditor/';
        /////hsCSSPath = url + 'storage/hotspot/testing/';
        hsHelperImagesPath = url + 'images/hotspot/hseditor/';
    }
    
    function preloadImages(theTime){
        var objects = jsonResultObj[theTime];
        
        for(index in objects){
            var image_url = objects[index]["infoWindow"].image_url;
            if (!imageSet.has(image_url)) {
        
                var downloadingImage = new Image();
                downloadingImage.onload = function(){
                        $('<img />').attr('src',this.src).appendTo('body').css('display','none');
                };
                downloadingImage.src = image_url;    
                imageSet.add(image_url);
            }
                
        }
    }
        
    var digestData = function(data) {

        JSON_RESULT=null;
        console.info("success");
        JSON_RESULT = data["data"];
        var results = JSON_RESULT["results"];

        OBJ_TABLE = data["data"]["obj_table"];
        var frame;
        for(index in results){
            frame = results[index]["frame"]
            jsonFrameSet.add(frame);
            jsonResultObj[frame] = results[index]["objects"];
        }
        //I will not try to preload all the images used by hotspots/banners
        //all at one go here. Potential wastage of bandwidth. But in the playheadupdated event, 
        //i will preload as the video progresses.
        //grep for 5000 elsewhere in the code: 
        //I will preload an image 5seconds before its time of appearance.
    
        preloadImages(1000);
        preloadImages(2000);
        preloadImages(3000);
        preloadImages(4000);
    
     

        var dimensions = JSON_RESULT["dimensions"].split('x');
        ORIGIN_WIDTH = dimensions[0];
        ORIGIN_HEIGHT = dimensions[1];

        //TODO If no hotspots no need to send anything ya
        //If this file is included as part of HSEditor the analytics js file will
        //not be pulled in, so we will not come here:
        if (results.length > 0) {
            if (window.gaTrackHotspots!=undefined && window.gaTrackHotspots.doGA()){
                window.gaTrackHotspots.init(entryId, hsVideoTitle, hslongId);
            }
        }
        //从七牛获取js信息
        //getWidthHeight(JSON_RESULT["file"]+"?avinfo");
    }
    var entryId = '';
    window.getJSONHandler = function(data) {
        if (data["status"]=="success") 
            digestData(data);
        else {
            console.error("fail: %o", data);
        }       
    }
    
    window.hotspotGetData = function(entryId0, longId) {
        entryId = entryId0;
        var url = hsJSONEndpointPath + entryId;
        if (longId != undefined && longId != '')
            url += "/" + longId;
        console.info("1. try to get json: " + url);
        //$.get(url, getJSONHandler, "json");
        $.ajax({
           url: url,
           dataType: "jsonp",
           jsonpCallback: "getJSONHandler"
        });

    };

    function injectContainers(playerId) {
        // Ready callback is issued for this player:
        console.log("readyCallback" + savedplayerId);
        /* HSEditor: we have a different sequence so OK 
           even if no data at this stage. TODO
           if (JSON_RESULT == null) {
            console.error("no json, just return");
            return;
        }
        */
        PLAYER_WIDTH = $('#wpb_video_wrapper').width();
        //PLAYER_WIDTH = kdp.evaluate("{video.player.width}");
        kdp = $('#' + playerId).get(0);
        kdpiframe = $('#' + playerId + ' iframe').get(0);
        //alert('#' + playerId + ' iframe'); 

        //
        //RENEE FIX HARDCODE iframe = $('iframe').get(0).contentWindow.document;
        
        iframe = $('#wpb_video_wrapper_ifp').get(0).contentWindow.document;
        //
        var link = document.createElement("link");
        link.rel = "stylesheet";
        link.type = "text/css";
        //link.href = host+'/wp-content/themes/sp3/css/hotspot-ad-style.css';
        //HSEditor:
        
        //link.href = '../../css/hotspot/hseditor/hotspot-ad-style.css?v=1.2';
        link.href = getHelperCSSURL();

        $(kdpiframe.contentWindow.document.head).append(link);

        var tmp = $(iframe.querySelector('.controlBarContainer'));
        hover = (tmp.length == 0 ? true: tmp.hasClass("hover"));
        boxContaniner.addClass((hover? "contaniner_hoverctrl" : "contaniner_blockctrl"));

        var outad= $('<div id="ad3out" class="adv-3-style-container">'+
         '<div class="adv-3-style">'+
             '<div class="adv-3-style-content">'+          
             '<img class="adv-3-style-pic">'+
             '<div class="adv-3-style-close"></div>'+                
         '</div>'+
         '</div>'+       
        '</div>');
        var indicator = $(
            (hover ? '<div id="ads" class="ads_hoverctrl">' : '<div id="ads" class="ads_blockctrl">' ) +
            '<!------------------------- 右上角文字链广告--- -->'+
            //'<div id="ad1" class="adv-1-style">'+
            //'<img class="adv-1-style-icon">'+
            //'</div>'+
            '<!------------------------- 暂停式广告 -->'+
            '<div id="ad2" class="adv-2-style-contaner">'+
            '<div class="adv-2-style">'+
            '<div class="adv-2-style-close"></div>'+
            '<div class="adv-2-style-content">'+
            '<img class="adv-2-style-pic">'+
            '<div class="adv-2-style-title"></div>'+
            '<div class="adv-2-style-text-contanier">'+
            '<div class="adv-2-style-text">'+
            '</div>'+
            '<div class="adv-2-style-btn">查看更多</div>'+
            '<div style="height: 55px"></div>'+
            '</div>'+
            '</div>'+
            '</div>'+
            '</div>'+
            '<!------------------------- 侧边栏广告-->'+
            '<div id="ad3" class="adv-3-style-container">'+
            '<div class="adv-3-style">'+
            '<div class="adv-3-style-content">'+
            '<img class="adv-3-style-pic">'+
            '<div class="adv-3-style-close"></div>'+
            '</div>'+
            '</div>'+
            '</div>'+
            '</div>');
        
    
        indicator.appendTo(kdpiframe.contentWindow.document.body);
        boxContaniner.insertAfter(kdpiframe.contentWindow.document.body.querySelector('.videoHolder'));
        redBoxContanier.appendTo(kdpiframe.contentWindow.document.body);
        outad.appendTo($(".video-container")[0]);

        //Now since the #ad covers also the slider area, we need to set pointer-events = none
        //so that the slider can still be responsive. But then the pic and the close box area
        //still need to receive click events.
        $(iframe.querySelector('#ads')).css('pointer-events','none');
        $(iframe.querySelector('#ad2')).css('pointer-events','none');
        $(iframe.querySelector('.adv-2-style-pic')).css('pointer-events','auto');
        $(iframe.querySelector('.adv-2-style-close')).css('pointer-events','auto');
        $(iframe.querySelector('#ad1')).css('pointer-events','auto');

    } /* RENEE ADDED THIS */

    window.playerReadyCb = function(playerId){
        savedplayerId = playerId;
        //<----------------
        PLAYER_WIDTH = $('#wpb_video_wrapper').width();
        //PLAYER_WIDTH = kdp.evaluate("{video.player.width}");
        kdp = $('#' + playerId).get(0);
        kdpiframe = $('#' + playerId + ' iframe').get(0);
        //alert('#' + playerId + ' iframe'); 

        //
        //RENEE FIX HARDCODE iframe = $('iframe').get(0).contentWindow.document;
        
        iframe = $('#wpb_video_wrapper_ifp').get(0).contentWindow.document;
        //
        var link = document.createElement("link");
        link.rel = "stylesheet";
        link.type = "text/css";
        //link.href = host+'/wp-content/themes/sp3/css/hotspot-ad-style.css';
        //HSEditor:
        
        //link.href = '../../css/hotspot/hseditor/hotspot-ad-style.css?v=1.2';
        link.href = getHelperCSSURL();

        $(kdpiframe.contentWindow.document.head).append(link);
        //------>
        
        kdp.kBind("firstPlay", function(){
            console.log("firstPlay");
            injectContainers(savedplayerId);
            // 第一次开始播放时，时间轴显示红点
            var duration = parseInt(kdp.evaluate('{duration}')) * 1000;
            playerRedDot(duration);
        });
        // 一旦移动某个时间点触发，就重新添加所有的frame到jsonFrameSet
        kdp.kBind('seeked', function (data) {
            console.log("seeked");
            //HSEditor:
            accurate_playheadMS = Math.round(data * 1000); 
            
            removeFrameSet.forEach(function(value1, value2, set){
                jsonFrameSet.add(value1);
            });
            removeFrameSet.clear();
            if(playerState=='paused'){
                 doPauseAction();
            }

            //renee-dev-209:
             //Added this for handling pausing of hotspots:
             //if the hotspot is paused and then followed by a seek
             //below the code clearTimeOutArray will get rid of all timers
             //and they will never be re-instated.
             //Then the hotspot will dangle there forever.
             //So add this call to clear the hotspot from view:
             ///TMP activeBannerObj = null;
             boxContaniner.empty();
             
            //删除所有定时器
            clearTimeOutArray();
            
            //If during preview mode, the user anyhow drags the seeker bar
            //then we just have to get out of preview mode. 
            unsetPreviewMode();

            //隐藏显示的广告
            $(iframe.querySelector('#ads')).css('display','none');
            $('#ad3out').css('display','none');
            $(iframe.querySelector('#ads')).children('div').css('display','none');
            $(iframe.querySelector('#ad3')).attr('show','close');
            $(document.querySelector('#ad3out')).attr('show','close');

            //文字链广告样式还原
            $(iframe.querySelector('.adv-1-style-icon')).stop(true).html('');
            $(iframe.querySelector('.adv-1-style-icon')).animate({
                width: '0px'
            });
            $(iframe.querySelector('.adv-1-style-icon')).fadeOut();

            //重置上一次广告时间
            lastAdTime=0;

        
        });

        //暂停触发,若该秒有红框，则显示红框
        kdp.kBind('doPause', function(){
            console.log("doPause");
            //currentTime
            if (playOrPauseEventCB) playOrPauseEventCB(false); //is play
            
            //HSEditor:
            //What if user clicks pause before the preview hotspot shows up?
            //unlikely but possible.
            hsePreviewObj = null; //no longer accessible 
            //This stuff here is to support the "correct" behavior for hotspots when a pause
            //is done during its showtime

            var entry;
            for (var i in timeOutArray) { 
                entry = timeOutArray[i];
                //Later we may have to set another time when user resumes play
                entry.remaining -= (currentTime - entry.frame);
                //console.log("time remaining " + entry.remaining + " " + currentTime + " " + entry.frame);
                entry.frame = currentTime; //if later pause again...
                if (entry.remaining > 0) {
                   window.clearTimeout(entry.currtid);
                   //console.log("Timer removed tid, remaining " + entry.currtid + " " + entry.remaining);
                }
            }
            doPauseAction();
        });

        //播放清除所有红框
        kdp.kBind('doPlay', function(){
            console.log("doPlay");
            if (playOrPauseEventCB) playOrPauseEventCB(true); //is play
            
            //清除红框
            //Cannot call empty() anymore. The hotspots need to persist after pause
            //and continue to stick around upon resuming of play.
            //boxContaniner.empty();
            
            //---** on video play if Advertisement opened,it will close
            adCloseVisible = false; //dev-295 added this. Not sure why there was never a call to set 
            //this var to false?!
            $(iframe.querySelector('.adv-2-style-contaner')).fadeOut();
            
            //renee-DEV-209
            //Set another timer for those hotspots which are now showing,
            //Continue to show for the remaining time, then the timeout comes
            // and entry.fcn will be run to remove the hotspot from view.
            
            var entry, tid;
            for (var i in timeOutArray) {
                entry = timeOutArray[i];
                if (entry.remaining > 0) {
                   //console.log("continue " + entry.tid + " " + entry.remaining);
                   tid = window.setTimeout(entry.fcn, entry.remaining);
                   //console.log("new tid=" + tid);
                   entry.currtid = tid;
                }
                else { //some freak timing we end up here, still need
                    //to do the timeout routine to make the hotspot disappear!
                    window.setTimeout(entry.fcn, 0);
                }
            }
        });

        var futureTime = 0;
        //播放过程中加广告
        kdp.kBind('playerUpdatePlayhead', function (data, id) {
            //HSE: I changed the round to floor.
            //Else it will often result in HS coming out too early and
            //disappearing too early.
            //Doing the floor way has a smaller possible max error:
            //Since updatePlayhead is called typically 4 times per sec:
            currentTime = Math.floor(data) * 1000;
            //if (currentTime == 6000)
                 //kdp.sendNotification("changeVolume", 0.8);
            //HSEditor:
            accurate_playheadMS = Math.round(data * 1000); 
            /****************
            if (hsePreviewObj) {
                if (hsePreviewObj.startTimeMS == currentTime && currentTime!=lastAdTime ){
                    showPreview(hsePreviewObj.obj, hsePreviewObj.startTimeMS );
                    hsePreviewObj = null; //no longer accessible
                    lastAdTime=currentTime;
                }
                return; //in preview mode we do not show anything else.
                //Thus we also must be careful not being locked in preview mode for ever.
                //if somehow the hsePreviewObj = null; above is not executed.
                //in pause, seek handlers, set hsePreviewObj = null;
            }
            //--> HSEditor
            ******************/

            // 如果这个frame存在，则加广告
            // 播放时同一秒只加一次广告
            // 显示广告
            if (currentTime + 5000 != futureTime) {
                futureTime = currentTime + 5000;
                if (jsonResultObj[futureTime])
                    preloadImages(futureTime);
            }
            if(( (hsePreviewObj && hsePreviewObj.startTimeMS == currentTime) 
                || jsonFrameSet.has(currentTime)) 
                &&currentTime!=lastAdTime){
                //console.log("before showAd fcn");
                
                showAdvertisement(currentTime);
                lastAdTime=currentTime;
            }
        });
        //记录播放器state
        var calledHSEOnPlayerStateChange = false;
        kdp.kBind('playerStateChange',function(data){
            console.log("playerStateChange " + data);
            //Renee comment: this below had been commented out.
            //Not sure why they were not using the ready event instead.
            //(Why they have to use call computeVideoOffset on every change to
            //"playing"?)
            //But then I dare not change it w/o really understanding the history.

            //if (data == "ready") { <-- ??
            if (data == "playing") { 
                computeVideoOffset();
                if (!calledHSEOnPlayerStateChange) { 
                   computeHSEWorkingAreaOffset();
                    calledHSEOnPlayerStateChange = true;
                }
            }
            
            playerState=data;
        });

        //HSEditor stuff:
        if (videoReadyCB)
            videoReadyCB();
    };

    var computeHSEWorkingAreaOffset = function() {
        //HSEditor stuff:
        if (areaInfoReadyCB) {
            areaInfoReadyCB(getVideoAreaSizePosition()); 
            if (!videoLengthMS)
                videoLengthMS = parseInt(kdp.evaluate('{duration}')) * 1000;  
        }
    }

    var computeVideoOffset = function(){
        //<-- HSEditor:
        if (ORIGIN_WIDTH == 0 || ORIGIN_HEIGHT == 0) {
            ORIGIN_WIDTH = parseFloat(kdp.evaluate('{mediaProxy.entry.width}'));
            ORIGIN_HEIGHT = parseFloat(kdp.evaluate('{mediaProxy.entry.height}'));
        }
        //--> HSEditor.

        var videoHolder = iframe.querySelector('.videoHolder');
        var height = $(videoHolder).height();
        var width = $(videoHolder).width();
        var computeHeight = width*ORIGIN_HEIGHT/ORIGIN_WIDTH;
        if (Math.abs(computeHeight-height)<1) computeHeight = height;
        //---** default style
        $(boxContaniner).css({
            width : "auto",
            margin : "auto"
        });
        
        if (computeHeight < height) {
            TOP_OFFSET = Math.floor((height-computeHeight)/2)
            LEFT_OFFSET = 0;
            $(boxContaniner).css({
                "height":computeHeight+"px",
                "margin-top":TOP_OFFSET+"px",
                "margin-bottom":TOP_OFFSET+"px"
            });
        }else{
            var computeWidth = height*ORIGIN_WIDTH/ORIGIN_HEIGHT;
            if (Math.abs(computeWidth-width)<1) computeWidth = width;
            LEFT_OFFSET = Math.floor((width-computeWidth)/2);
            TOP_OFFSET = 0;
            $(boxContaniner).css({
                "width":computeWidth+"px",
                "margin-left":LEFT_OFFSET+"px",
                "margin-right":LEFT_OFFSET+"px",
                "height" : ""
            });
        }
        
    };

    // 进度条红点显示
    var playerRedDot = function(duration){
        //var redDot = Math.floor(data) / 305 * 100;
        var dotPlace = [];
        // 寻找有广告的时间点
        for(var i in jsonResultObj){
            for(var j in jsonResultObj[i]){
                if(jsonResultObj[i][j].hasOwnProperty("infoWindow")){
                    dotPlace.push(i/duration);
                }
            }
        }
        // 显示红点到时间轴
        for(var dot in dotPlace){
            //HSEditor: we need to be able to
            //remove these dots later if necessary (but not other things
            //with the same parent. So must give these dots
            //a class. So added class name
            $('<span class="dotty"></span>').css({
                'position': 'absolute',
                'background-color': '#E95941',
                'width': '1%',
                'height': '8px',
                'left': dotPlace[dot]*100 + '%',
                'z-index': '9999',
                'border-radius': '5px'}
            ).appendTo(iframe.querySelector("[role='slider']"));
        }
    };

    //Use by HSEditor.
    var playerRedDotClear = function() {
        if (iframe) { //TMP
        var e = iframe.querySelector("[role='slider']");
        if ( e && e.hasChildNodes() ) {   
           //must do from the back! Else the idx will be shifted as you remove a child.
           for(var i= e.childNodes.length-1; i >= 0; i--) {
                if (e.childNodes[i].className == "dotty") {
                    e.removeChild(e.childNodes[i] ); 
                }
            }
        }
        }
    };


    // 显示红框
    var showRedBox = function (currentTime) {
        var objects = jsonResultObj[currentTime];
        var urlvar = 'url(\"' + getHelperImageURL("ad_indicator.png") + '\")';

        //红框面积排序，从大到小
        objects.sort(function(a,b){
            var l1 = a.rect.split(",").map(function(v){return parseInt(v)});
            var l2 = b.rect.split(",").map(function(v){return parseInt(v)});
            return -(l1[2]*l1[3]-l2[2]*l2[3]);
        });
        for(index in objects){
            //var boxRectInfo = zoomRedBox(objects[index]["rect"].split(","), TOP_OFFSET, LEFT_OFFSET);
            var boxRectInfo = objects[index]["rect"].split(",");
            //按照z-index堆叠红框，面积小的在最上面
            $('<div class="js-redbox" value='+currentTime+' id=' + objects[index]["db_id"] + '>' + '</div>').css({'z-index': index+1,
                //'position': 'absolute', 'background-image': 'url("'+host+'/wp-content/themes/sp3/images/ad_indicator.png")',
                //HSEditor:
                'position': 'absolute', 'background-image': urlvar,
                'background-size': '100% 100%',
                'left': boxRectInfo[0] + '%',
                'top': boxRectInfo[1] + '%',
                'width': boxRectInfo[2] + '%',
                'height': boxRectInfo[3] + '%'}).appendTo(boxContaniner);
        }
        //删除这一秒
        jsonFrameSet.delete(currentTime);
        removeFrameSet.add(currentTime);
    };

    function removeFromTimeOutArray(timerid) {
        for (var i in timeOutArray) {
            //console.log("remove attempt cf " + timeOutArray[i].tid, timerid);
            if (timeOutArray[i].tid == timerid) {
                timeOutArray.splice(i, 1);
                return;
            }
        }
        ///alert("cannot find timer " + tid);
    }

    var addStopBox = function(boxobj, frame){
        //boxContaniner.css('pointer-events', 'auto'); 

        var boxRectInfo = boxobj["rect"].split(",");
        var info = boxobj["infoWindow"];
        var urlvar = 'url(\"' + getHelperImageURL("ad_indicator.png") + '\")';
        //按照z-index堆叠红框，面积小的在最上面

        
        doGAMaybe(2, 0, info.title, boxobj.start_time, info.advertiser.name);
                 
        var that=$('<div class="js-redbox" id=' + boxobj["db_id"] + '>' + '</div>').css({
                //'position': 'absolute', 'cursor': 'pointer', 'background-image': 'url("'+host+'/wp-content/themes/sp3/images/ad_indicator.png")',
                //HSEditor:
                'position': 'absolute', 'cursor': 'pointer', 'background-image': urlvar,
                'background-size': '100% 100%',
                'pointer-events': 'auto',
                'top': boxRectInfo[0] + '%',
                'left': boxRectInfo[1] + '%',
                'width': boxRectInfo[2] + '%',
                'height': boxRectInfo[3] + '%'})
            .bind("click", function(){
                //HSEditor added the clickAdCB
                doGAMaybe(2, 1, info.title, boxobj.start_time, info.advertiser.name);
                hseClickHandling(boxobj, frame)
                adShowStopStyle(info, boxobj.start_time);
                kdp.sendNotification("doPause");
            }).appendTo(boxContaniner);

        // for HSEditor: it is the same enqueueing as before
        //But I just made the timeout callback function
        //more elaborate. Write it out like this for
        //clarity:
        var fcn = function(){
            //set preview stopped
            that.remove();
            removeFromTimeOutArray(timerID);
            hseTimeoutHandling(boxobj);
         }
         //set preview running
         var timerID = setTimeout(fcn, boxobj["duration"]);
         //console.log("initial timer id " + timerID);
         timeOutArray.push({ frame: currentTime, tid: timerID, currtid: timerID, remaining: boxobj["duration"], fcn : fcn} );
    };

    // 播放到暂停式广告时，显示有广告的框
    var showStopBox = function(currentTime){
        var objects = jsonResultObj[currentTime];
        if($(iframe.querySelector(".js-redbox[value='"+currentTime+"']")).length >= 1){
           return;
        }
        for(index in objects){
            //TODO Is this a bug? Why do like this?
            if(objects[index].hasOwnProperty("infoWindow")){
                addStopBox(objects[index], currentTime);
            }
        }
    };

    // 显示广告
    var showAdvertisement = function(currentTime){
        var objects;
        if (getPreviewMode()) {
            return; 
        }
        if (hsePreviewObj && hsePreviewObj.startTimeMS == currentTime) {
            objects = [ hsePreviewObj.obj ];
            hsePreviewObj = null; //no longer accessible.
            setPreviewMode(); 
        }
        else
            objects = jsonResultObj[currentTime];
        
        for(index in objects){
            var hsobj = objects[index];
            if(hsobj.hasOwnProperty("infoWindow")){
                //显示广告
                $(iframe.querySelector('#ads')).css('display','');
                var info = hsobj["infoWindow"];
                if( info["ad_type"] == 1){
                    adShowLinkStyle(hsobj, currentTime);
                }else if(info["ad_type"] == 2){
                    //adShowStopStyle(info);
                    if (hsobj.hasOwnProperty('preview'))
                        addStopBox(hsobj, currentTime);                    
                    else {
                        addStopBox(hsobj, currentTime);                    
                        //This is the original way in th code.
                        //Which goes thru a loop again..!! Sth fishy there.
                        //It means at currentTime if there were a banner
                        //they will also try to show it.
                        //showStopBox(currentTime); 
                    }

                }else if(info["ad_type"] == 3){
                    adShowSideStyle(hsobj, currentTime);
                }
            }
        }
    };

    function percent2Px(percentage, total, offset) {
        return Math.round((total*percentage/100) + offset);
    }
    //文字链广告
    var adShowLinkStyle = function(hsobj, tframe){
        doGAMaybe(1, 0, hsobj.infoWindow.title, hsobj.start_time, 
            hsobj.infoWindow.advertiser.name);
        var dir = (hsobj["infoWindow"].hasOwnProperty("ad_subtype") ? hsobj["infoWindow"]["ad_subtype"] : 1);
        var boxRectInfo = hsobj["rect"].split(",");
        var id = hsobj["db_id"];
        $('<div class="adv-1-style" id=ad1' + id + '>'+
            '<img class="adv-1-style-icon" id=adx1' + id + '>' +
            '</div>').css({
                'position': 'absolute',
                'background-size': '100% 100%',
                'pointer-events': 'auto',
                'top': boxRectInfo[0] + '%',
                'left': boxRectInfo[1] + '%',
                'width': boxRectInfo[2] + '%',
                'height': boxRectInfo[3] + '%'
            }).bind('click', function() {
                doGAMaybe(1, 2, hsobj.infoWindow.title, hsobj.start_time, 
                hsobj.infoWindow.advertiser.name);
                window.open(hsobj.infoWindow["url"]);
                hseClickHandling(hsobj, tframe);
                kdp.sendNotification("doPause");
                outerDiv.off('click'); //?
            }).appendTo(boxContaniner);
        
        var start_attr, end_attr;
        var outerDiv = $(iframe.querySelector('#ad1' + id));
        var innerDiv = $(iframe.querySelector('#adx1' + id));
        
        var attr = {};
        if (dir == 3 || dir == 4) 
            attr.width = '100%';
        else
            attr.height = '100%';
        innerDiv.css(attr);
        outerDiv.fadeIn();
        loadImage(hsobj.infoWindow["image_url"], innerDiv, function() {
                var end = (dir >= 3 ? Math.round(outerDiv.height()) : Math.round(outerDiv.width())); 
               
                if (dir == 1) { //left to right
                    start_attr = { width : '0px'};
                    end_attr = { width : '100%' };   
                }
                else if (dir == 2) { //right to left
                    start_attr = { left : end + 'px', width : '0px'};
                    end_attr = { left : '0px' , width : '100%' };   
                }
                else if (dir == 3) { //top down
                    start_attr = { height : '0px'};
                    end_attr = { height : end + 'px' };   
                }
                else if (dir == 4) {
                    start_attr = { top : end + 'px', height : '0px'};
                    end_attr = { top : '0px' , height : '100%' };   
                }
                innerDiv.css( start_attr );
                innerDiv.fadeIn(); 
                innerDiv.animate(end_attr, 1500, function () {
                var fcn = function(){
                    innerDiv.animate(start_attr, 1500, function () {
                        innerDiv.fadeOut();
                        hseTimeoutHandling(hsobj);
                        outerDiv.remove();
                    });
                }                    
                var timerID = setTimeout(fcn, hsobj["duration"]); 
                timeOutArray.push({ frame: currentTime, tid: timerID, currtid: timerID, remaining: hsobj["duration"], fcn : fcn} );
            });
        }); //end of load image
    }; //end of function
                                       
    //dev-295: added function loadImage()
    //Actually an early fix by colleague already understood the async nature of this
    //and so only try to take the width of the picture upon the loaded event: (chaining to onload)
    //    Old way: $(iframe.querySelector(".adv-2-style-pic")).attr("src", info["image_url"]).on('load', function(){
    //  Tried:    .load(function()...), also same problem.
    //Seems to have 2 types of problems. The "loaded" handler never gets called.
    //(in some situation, probably when the image is already in cache), or that
    //when the loaded handler is called, we still cannot get a good value for the
    //pic width etc.
    //This following approach *seems* to take care of all these issues.
    function loadImage(imageURL, imageContainer, postProcessing) {
        var downloadingImage = new Image();
        //Trick is : reload it into a non-DOM image object.
        //If it's cached, this will take no time at all, and the onload will 
        //still fire. If it isn't cached, it will fire the onload when the image 
        //is loaded, which should be the same time as the DOM version of the image 
        //finishes loading.
        downloadingImage.onload = function(){
            postProcessing();
        };
        imageContainer.attr('src', imageURL);
        downloadingImage.src = imageContainer.attr('src');
   }

    // 暂停式广告
    var adShowStopStyle = function(info, starttime){
        //RENEE FIX HARDCODE var iframe = $('iframe').get(0).contentWindow.document;
        var iframe = $('#wpb_video_wrapper_ifp').get(0).contentWindow.document;
        $(iframe.querySelector('.adv-2-style-contaner')).stop(true);
        $(iframe.querySelector('.adv-2-style')).stop(true);
        $(iframe.querySelector(".container")).fadeIn();
        
        $(iframe.querySelector('.adv-2-style-pic')).off('click');
        $(iframe.querySelector(".adv-2-style-pic")).click(function () {
            doGAMaybe(2, 2, info.title, starttime,
                info.advertiser.name);
            window.open(info["url"]);
        });

        $(iframe.querySelector('.adv-2-style-close')).click(function () {
            //---** When advertisement click to close then video will resume
            kdp.sendNotification("doPlay");
        });

        //This is a simple solution to a tricky problem in the HSEditor.
        //The sizing and positioning rect #hs_new is inside #working_area which must
        //be of a higher z-index than the video. (If that rect is embedded in the
        //video's iframe, then resizing won't work. So that's not a solution)
        //So when user clicks on a hotspot in HSEditor, it should both show the
        //ad-picture AND open the hotspot in edit-mode (which shows the hs_new element)
        //But problem is the hs_new is of a higher z-index so if it so happens that it
        //covers the ad picture's close box, we cannot close the pic unless we move
        //away the hs_new (which is also bad coz user may mess up the hotspot position)
        //So solution is to make the ad pic disappear when user clicks on the hs_new.
        var hs = $('#hs_new');  
        if (hs.length > 0)
            hs.on('click', function() {
                $(iframe.querySelector('.adv-2-style-contaner')).fadeOut();    
            });
        
        $(iframe.querySelector('.adv-2-style-contaner')).fadeIn();
        //查看更多

        loadImage(info["image_url"], $(iframe.querySelector(".adv-2-style-pic")),
         function() {
            adCloseVisible = true;
            adSetCloseButtonPosision();
            $(iframe.querySelector('.adv-2-style-title')).html(info["title"]);
            $(iframe.querySelector('.adv-2-style-text')).html(info["content"]);
            $(iframe.querySelector('#ads')).css('display','');
         });
      /******** dev-295: replaced by the above chunk of code 
        //Renee: Laravel integration
        $(iframe.querySelector(".adv-2-style-pic")).attr("src", info["image_url"]).on('load', function(){
            adCloseVisible = true;
            adSetCloseButtonPosision();
        });

        $(iframe.querySelector('.adv-2-style-title')).html(info["title"]); <-- useless
        $(iframe.querySelector('.adv-2-style-text')).html(info["content"]); <-- useless
        $(iframe.querySelector('#ads')).css('display',''); <--- also useless (You don't need this to make it visible)
        ***********/
    };


    var adSetCloseButtonPosision = function (lapse) {
        if (!adCloseVisible) {return};
        var width;
        var image = $(iframe.querySelector(".adv-2-style-pic"));
        width = image.width();
        //dev-295,
        //Actually with the new and improved loadImage function above, we don't
        //seem to run into this width == 0 situation anymore. (?!)
        //Need more testing. When confirmed, we can take out the following
        //retry mechanism for !width.
        if (!width) 
            console.log("width=0, adSetCloseButtonPosition--------------");
        if (!width  && (!arguments.length || lapse <= 2000)) {
            var totallapsed = (!arguments.length ? 100 : lapse + 100);
            setTimeout(function(){adSetCloseButtonPosision(100)},totallapsed);
        }
        else  {
            /* var videoHolder = iframe.querySelector('.videoHolder');
            var x = $(videoHolder).width();
            var percentage = 100*width/x;
            $(iframe.querySelector('.adv-2-style')).css({'width' : percentage + '%'}); */
            $(iframe.querySelector('.adv-2-style')).width(width);
            $(iframe.querySelector('.adv-2-style-close')).show();
        }
    }

    // 侧边栏广告
   var adShowSideStyle = function(hsobj, tframe){
        $('#ad3out').attr('show','open');
        $('#ad3out').css('z-index', 999); 
        $('#ad3out').css({'display':'inline-block', 'left': parseInt(PLAYER_WIDTH) - parseInt(245) + 'px'});
        $(".adv-3-style-pic").attr("src", hsobj.infoWindow["image_url"]);

        if($(document).width()-$('.video-container').width()-$('.video-container').offset().left>240){
            $('#ad3out').stop(true).animate({'left': parseInt(PLAYER_WIDTH) - parseInt(232) + 'px'}, function () {
                var timeOut=setTimeout(function () {
                      $('#ad3out').stop(true).animate({'left': parseInt(PLAYER_WIDTH) - parseInt(13) + 'px'});
                    timeOutArray.push(setTimeout(function () {
                        $('#ad3out').stop(true).animate({'left': parseInt(PLAYER_WIDTH) - parseInt(245) + 'px'},function(){
                            $('#ad3out').css('display','none');
                    hseTimeoutHandling(hsobj);

                        });
                    }, hsobj["duration"])); 
                }, 1000);
                timeOutArray.push(timeOut);
            });
        }
        else{
            var timeOut=setTimeout(function () {
                  //$('#ad3out').css('z-index', 10); 
                timeOutArray.push(setTimeout(function () {
                    $('#ad3out').css('z-index',1);
                    hseTimeoutHandling(hsobj);

                }, hsobj["duration"])); 
            }, 1000);
            timeOutArray.push(timeOut);
        }
        $(".adv-3-style-pic").off('click');
        $(".adv-3-style-pic").on('click',function () {
            doGAMaybe(3, 2, hsobj.infoWindow.title, hsobj.infoWindow.start_time,
                hsobj.infoWindow.advertiser.name);

            hseClickHandling(hsobj, tframe);
            kdp.sendNotification("doPause");
            window.open(hsobj.infoWindow["url"]);
        });
        $('.adv-3-style-close').click(function(){
            $('#ad3out').css('display','none');
            $('#ad3out').attr('show','close');
        });

        adShowSideStyleFullScreen(hsobj); //Renee modified the param

        if(!(document.fullScreen||document.webkitIsFullScreen||document.mozFullScreen||document.msFullscreenElement!=null)){
            $(iframe.querySelector('#ad3')).css('display','none');
        }

    };

    var adShowSideStyleFullScreen=function(hsobj){

        $(iframe.querySelector('#ad3')).attr('show','open');
        $(iframe.querySelector('#ad3')).css({
            'display':'inline-block',
            'right':  '-245px',
            'z-index': '99999',
            'overflow':'hidden'
        });
        $(iframe.querySelector('.adv-3-style-close')).css('left','20px');

        $(iframe.querySelector(".adv-3-style-pic")).attr("src", hsobj.infoWindow["image_url"]);
        $(iframe.querySelector('#ad3')).stop(true).animate({'right': '-232px'}, function () {
            var timeOut=setTimeout(function () {
                $(iframe.querySelector('#ad3')).stop(true).animate({'right':  '-2px'});
                timeOutArray.push(setTimeout(function () {
                    $(iframe.querySelector('#ad3')).stop(true).animate({'right': '-245px'});
                    hseTimeoutHandling(hsobj);

                }, 10000));
            }, 1000);
            timeOutArray.push(timeOut);
        });
        $(iframe.querySelector(".adv-3-style-pic")).off('click');
        $(iframe.querySelector(".adv-3-style-pic")).on('click',function () {
            window.open(hsobj.infoWindow["url"]);
        });
        $(iframe.querySelector('.adv-3-style-close')).click(function(){
            $(iframe.querySelector('#ad3')).css('display','none');
            $(iframe.querySelector('#ad3')).attr('show','close');
        })
    };

    //暂停时处理
    var doPauseAction=function(){
        return;
        var currentTime = Math.round(kdp.evaluate("{video.player.currentTime}")) * 1000;
        boxContaniner.empty();
        if(jsonFrameSet.has(currentTime)||removeFrameSet.has(currentTime)){
            showRedBox(currentTime);
            // 暂停时当前秒白框显示fitamos的名称，如果是新增的广告则只显示秒数（新增的广告没有id，但是有db_id）
            var obj = jsonResultObj[currentTime];
            for(index in obj){
                var db_id = obj[index]["db_id"];
                if(obj[index].hasOwnProperty("id")){
                    $(iframe.querySelector(".js-redbox[id='"+db_id+"']")).html('<b style="color: #ffdc28;text-align: center">'+OBJ_TABLE[obj[index]["id"]]+'</b>');
                }else{
                    //$(iframe.querySelector(".js-redbox[id='"+db_id+"']")).html('<b style="color: #ffdc28;text-align: center">第'+currentTime/1000+'秒</b>');
                    $(iframe.querySelector(".js-redbox[id='"+db_id+"']")).html('<b style="color: #ffdc28;text-align: center"></b>');
                }
            }


            $(iframe).find(".js-redbox").each(function(){
                var frame = $(this).attr("value");
                var obj = jsonResultObj[frame];
                var db_id = $(this).attr("id");
                for(index in obj){
                //有暂停式广告的，触发暂停式广告，否则不做任何操作
                    if((obj[index].hasOwnProperty("infoWindow")) && db_id == obj[index]["db_id"]&&obj[index]["infoWindow"]["ad_type"] == 2){
                        var boxobj=obj[index];
                        var info = boxobj["infoWindow"];
                        $(this).css('cursor','pointer');
                        $(this).bind( "click", function(){
                            clearTimeOutArray();
                            adShowStopStyle(info);
                        });
                    }
                }
            })
        }
    };

    //
    var clearTimeOutArray=function(){
        for(var i in timeOutArray){
            clearTimeout(timeOutArray[i]);
        }
        timeOutArray=[];
    };
    //切换全屏处理
    var screenChange=function(){
        //console.log("...............screen change");
        //scaleActiveBanner();
        //---** fullscreen changing time - again calculating video offset
        setTimeout(function(){
            adSetCloseButtonPosision();
            computeVideoOffset();
            computeHSEWorkingAreaOffset();
        }, 500);

        if($(iframe.querySelector('#ad3')).attr('show')=='close'||$(document.querySelector('#ad3out')).attr('show')=='close'){
            $(iframe.querySelector('#ad3')).css('display','none');
            $(document.querySelector('#ad3out')).css('display','none');
            return;
        }
        if($(iframe.querySelector('#ad3')).attr('show')!='close'||$(document.querySelector('#ad3out')).attr('show')!='close'){
            if((document.fullScreen||document.webkitIsFullScreen||document.mozFullScreen||document.msFullscreenElement!=null)){
                $(iframe.querySelector('#ad3')).css('display','');
                $(document.querySelector('#ad3out')).css('display','none');
            }
            else{
                $(iframe.querySelector('#ad3')).css('display','none');
                $(document.querySelector('#ad3out')).css('display','');
            }
        }
    };
    document.addEventListener("fullscreenchange", function () {
        screenChange();
    }, false);
    document.addEventListener("mozfullscreenchange", function () {
        screenChange();
    }, false);
    document.addEventListener("webkitfullscreenchange", function () {
       screenChange();
    }, false);
    document.addEventListener("MSFullscreenChange", function () {
       screenChange();
    }, false);   

    //---** Added device orentation event - works for only mobile device
    window.addEventListener("orientationchange", function() {
        //console.log("...............orientation change");
        //scaleActiveBanner();
        setTimeout(function(){
            adSetCloseButtonPosision();
            //For here we do not call
            //computeHSEWorkingAreaOffset coz the editor is meant
            //for desktop.
            computeVideoOffset()},500);
    });
////MOVE DOWN : })(jQuery);

    function hseClickHandling(hsobj, frame) {
        if (clickAdCB && !hsobj.hasOwnProperty('preview')) {
            clickAdCB(hsobj, frame);
            ////activeBannerObj = null;
            boxContaniner.empty(); 
            //删除所有定时器
            clearTimeOutArray(); 
        }
        //kdp.sendNotification("doPause");
    }

    function hseTimeoutHandling(hsobj) {
        if (hsobj.hasOwnProperty('preview'))
            kdp.sendNotification("doPause");
    }
    
    var previewMode = false;
    function setPreviewMode() { previewMode = true;}
    function unsetPreviewMode() { previewMode = false;}
    function getPreviewMode() { return previewMode; }
    var data = null;
    var videoLengthMS = 0;
    var accurate_playheadMS = 0;
    var hsePreviewObj = null;
    var areaInfoReadyCB = null;
    var playOrPauseEventCB = null;
    var videoReadyCB = null;
    var clickAdCB = null;

    //called by HSE 
    function hotspotDigestData() {
        JSON_RESULT = data["data"];
        var results = JSON_RESULT["results"];
        OBJ_TABLE = data["data"]["obj_table"];
        var frame;
        for(index in results){
            frame = results[index]["frame"]
            jsonFrameSet.add(frame);
            jsonResultObj[frame] = results[index]["objects"];
        }

        var dimensions = JSON_RESULT["dimensions"].split('x');
        //The video might be loaded before the JSON file (renee's new mode)
        //Thus we use the origin width height as set by querying the video side
        if (ORIGIN_WIDTH == 0 || ORIGIN_HEIGHT == 0)  {
            ORIGIN_WIDTH = dimensions[0];
            ORIGIN_HEIGHT = dimensions[1];
        }
        //getWidthHeight(JSON_RESULT["file"]+"?avinfo");
         //If this file is included as part of HSEditor the analytics js file will
        //not be pulled in, so we will not come here:
        //It should not be here but testing only TODO
        //TODO If no hotspots no need to send anything ya
        //if (results.length > 0) {
          //  if (window.gaTrackHotspots!=undefined && window.gaTrackHotspots.doGA()){
            //    window.gaTrackHotspots.init(entryId, hsVideoTitle, '122333335');
            //}
        //}
    };

    window.setGID = function(gid) {
          adsMgr.setGID(gid);
    }
    window.setHotspotsSetId = function(hssetid) {
          adsMgr.setHSSID(hssetid);
    }

    //return info about the actual movie area in an object {left, top, width, height}
    //This will remove the area of the control bar and the extra pair of
    //vertical or horizontal strips present (coz the aspect ratio of the movie may not
    //match that of the movie frame.
    function getVideoAreaSizePosition() {
        //var videoHolder = iframe.querySelector('.videoHolder');
        var videoHolder = $('#wpb_video_wrapper'); 
        var sliderElt = $(iframe.querySelector('.controlBarContainer'));
        var sliderThickness = sliderElt.height();
        
        sliderHeight = Math.round(sliderThickness); /* HSE needs this info */
        var playerHeight = $(videoHolder).height();  
        if (!sliderElt.hasClass( "hover" ))
            playerHeight -= sliderThickness; 
        var playerWidth = $(videoHolder).width(); 

        var videoWidth = parseFloat(kdp.evaluate('{mediaProxy.entry.width}'));
        var videoHeight = parseFloat(kdp.evaluate('{mediaProxy.entry.height}'));

        var offset = 0;
        var hsHeight, hsWidth;
        var toppx, leftpx;
        //1st situation is where the video's aspect ratio => that of the video frame
        //This is the situaion where there will be 2 horiz strips of blank
        //above and below the video.
        var hsHeight = ( playerWidth / videoWidth ) * videoHeight;
        if (hsHeight <= playerHeight) {
            offset = (playerHeight - hsHeight)/2;
            hsWidth = playerWidth;
            toppx = offset;
            leftpx = 0;
        }
        else { 
            //This is the situaion where there will be 2 vert strips of blank
            //left and right of the video.
            hsHeight = playerHeight;
            hsWidth = videoWidth * playerHeight / videoHeight;
            offset = (playerWidth - hsWidth)/2;
            toppx = 0;
            leftpx = offset;
        }
        var o = { left : leftpx, top : toppx, width : hsWidth, height : hsHeight,
        videoWidth : videoWidth, videoHeight : videoHeight };
        return o;
    };
    /*****************************************************************
     *
     ******************************************************************/

    videoMgr = new Object();
    
    //We cannot call any ask about this as we want it.
    //This info is available only when the player is ready.
    //Hmm, possible race condition
    videoMgr.registerVideoReadyCB = function(cb) {
        videoReadyCB = cb;
    }
    videoMgr.registerVideoAreaInfoCB = function(cb) {
        if (! cb) alert("videoMgr.registerVideoAreaInfoCB NULL cb!");
        areaInfoReadyCB = cb;
    };
    videoMgr.registerPlayOrPauseEventCB = function(cb) {
        if (! cb) alert("videoMgr.registerPlayOrPauseEventCB NULL cb!");
            playOrPauseEventCB = cb;
    };
    videoMgr.getSliderHeight = function() {
        return sliderHeight;
    }
    videoMgr.getVideoDurationMS = function() {
        return videoLengthMS;
    }
    videoMgr.getPlayhead = function() {
        return accurate_playheadMS;
    };
    function cloneObj(o) {
        return ( $.extend(true, {}, o) );
    }
    videoMgr.doPreview = function(hsobj, starttimeMS) {
        var dupobj = cloneObj(hsobj);
        dupobj.preview = 1;
        hsePreviewObj = new Object(); 
        hsePreviewObj.obj = dupobj; 
        hsePreviewObj.startTimeMS = starttimeMS;
        if (starttimeMS > 2000)
            starttimeMS = starttimeMS - 2000;

        //clear all those visible hotspots there.
        /////activeBannerObj = null;
        boxContaniner.empty();
        clearTimeOutArray();

        kdp.sendNotification("doSeek", Math.round(starttimeMS/1000)); 
        kdp.sendNotification("doPlay");

    };
    videoMgr.abortPreview = function() {
        hsePreviewObj = null;
    };
    videoMgr.doPause = function() {
        if (kdp)
            kdp.sendNotification("doPause");
    };
    videoMgr.doSeek = function(tframeMS) {
        kdp.sendNotification("doSeek", Math.round(tframeMS/1000)); 
    };
    adsMgr = new Object();

    function changeAllAds() {
        playerRedDotClear();
        if (videoLengthMS > 0)
           playerRedDot(videoLengthMS);  //else div by 0. Just in case
        fillListWithAds();
    }

    function reflectAdsListChange(type, hsobj, frame) {
        playerRedDotClear();
        if (videoLengthMS > 0)
            playerRedDot(videoLengthMS);  //else div by 0. just in case.
         //we should add 1 function to just change 1 DOT instead of redrawing all of them (TODO)
        fillListWithAds();
        /* With all good intentions for performance reasons,
           upding the list this way has its problems. e.g. 
           ordering... etc. Just delete everything in the list
           and refill!
           
        if (type == "add") 
            addAdToList(hsobj, frame);
        else if (type == "update") 
            updateAdInList(hsobj, frame);
        else if (type == "delete") 
            deleteAdFromList(hsobj, frame);
        */
    }
  
    //the last arg is optional. We need to know is addAd is
    //called from a real Add or as part of an update (change startime)
    //If call from a real add, the last param is not there.

    //Coz of the orig design of organizing by frames.
    //Now to support changing the start time involves taking deleting
    //the entry from the old frame group and adding it to the new frame
    //group
    adsMgr.addAd = function(obj, tframe, isreplace) {
        if (!jsonFrameSet.has(tframe))
            jsonFrameSet.add(tframe);
        var arr = jsonResultObj[tframe];
        if (arr) {
            var idx = arr.length;
            arr[idx] = obj;
        }
        else {
            jsonResultObj[tframe] = new Array();
            jsonResultObj[tframe][0] = obj;
        }
        if (isreplace)
            reflectAdsListChange("update", obj, tframe);
        else
            reflectAdsListChange("add", obj, tframe);
    };

    //internal helper function
    function deleteObjFromThisFrameMaybe(obj, tframe) {

        var arr = jsonResultObj[tframe];
        if (arr) {
            if (arr.length == 1 && jsonResultObj[tframe][0].db_id == obj.db_id)  {
                delete jsonResultObj[tframe]; 
                jsonFrameSet.delete(parseInt(tframe));
                return true;
            }
            else {
                for (var arridx in arr){
                    if (arr[arridx].db_id == obj.db_id)  {
                        arr.splice(arridx, 1);
                        return true;
                    } //if
                }//for arridex
            }//else
        }    //if arr
        return false; //not done. couldn't find obj.
    };

    //TODO: trigger redraw of red dots. Should get rid of all the timers?
    adsMgr.deleteAd = function(obj, tframe) {
        if (!obj) return false;
        //who knows if the tframe info is right. Just be paranoid.
        if (deleteObjFromThisFrameMaybe(obj, tframe)) {
            reflectAdsListChange("delete", obj, tframe);
            return true;
        }
        //We are still here. Means can't find the ad under that tframe?! 
        //Look for it by ID then.
        for (var frameStr in jsonResultObj){
            arr = jsonResultObj[frameStr];
            for (var arridx in arr){
                if (arr[arridx].db_id == obj.db_id) {
                    var ret = deleteObjFromThisFrameMaybe(obj, frameStr);
                    if (ret == true) {
                        reflectAdsListChange("delete", obj, frameStr);
                    }
                    return ret;
                }
            }//for
        }//for
        return false; //mission failed. cannot find the obj
    };

    //TODO: trigger redraw of red dots. Should get rid of all the timers?
    //The delete (worse is Update) is SO complicated because the original design
    //was not meant to support change of starttime (the whole data structure is
    //organized by starttime! So to change start time it is a big pain
    adsMgr.updateAd = function(obj, tframe, tframeNew) {
        if (!obj) return false;

        if (tframe == tframeNew) {
            var arr = jsonResultObj[tframe];
            if (arr) {
                if (arr.length == 1 && jsonResultObj[tframe][0].db_id == obj.db_id)  {
                    arr[0] = obj;
                    reflectAdsListChange("update", obj, tframeNew);
                    return true;
                }
                else {
                    for (var arridx in arr){
                        if (arr[arridx].db_id == obj.db_id)  {
                            arr[arridx] = obj;
                            reflectAdsListChange("update", obj, tframeNew);
                            return true;
                        }//if
                    }//for arridx
                } //else
            }    //if arr
        } //if tframe == tframeNew
        //The above is the most common use case. The following is when 
        //starttime (frame) has been changed to a new one.
        //Then it's quite troublesome.
        for (var frameStr in jsonResultObj){
            arr = jsonResultObj[frameStr];
            for (var arridx in arr){
                if (arr[arridx].db_id == obj.db_id) {
                    if (deleteObjFromThisFrameMaybe(obj, frameStr)) 
                        return (adsMgr.addAd(obj, tframeNew, true));
                    return false;
                }//if
            }//inner for
        } //outer for
        return false; 
    }

    adsMgr.getAd = function(db_id, tframe) {
        if (tframe != null) {
            var arr = jsonResultObj[tframe];
            if (arr) {
                if (arr.length == 1 && arr[0].db_id == db_id)  
                    return arr[0];
                else {
                    for (var arridx in arr) {
                        if (arr[arridx].db_id == db_id)  
                            return arr[arridx];
                    }
                }//if array is more than 1 elt long
            } //if arr 
        }//if tframe info is given
        //We are still here. Means can't find the ad under that tframe?! 
        //Look for it by ID then.
        for (var frameStr in jsonResultObj){
            arr = jsonResultObj[frameStr];
            for (var arridx in arr){
                if (arr[arridx].db_id == db_id)
                    return arr[arridx];
            }
        }
        return null;
    };

    adsMgr.doForAllHSObjs = function(cb) {
        for (var frameStr in jsonResultObj){
            arr = jsonResultObj[frameStr];
            for (var arridx in arr){
                cb(arr[arridx]);
            }//inner for
        } //outer for
    }
    adsMgr.getGID = function () { return adsMgr.gid; }
    adsMgr.getHSSID = function () { return adsMgr.hssetid; }
    adsMgr.getOwnerID = function () { return adsMgr.ownerid; }
    
    var adsListChangeUpdateCtrl = null;
    var adsListChangeUpdateCtrlFcn = null;

    function secs_to_HHMMSS(s) {
    //TODO: CHECKING
    var raw_secs = s | 0;
    var secs_micro = s % 60;
    var secs = raw_secs % 60;
    var raw_mins = raw_secs / 60 | 0;
    var mins = raw_mins % 60;
    var hours = raw_mins / 60 | 0;
    var str;
     var secs_str = (secs / 100).toFixed(2).substring(2);
    if (hours > 0)
        str = hours + ':' + mins + ':' + secs_str;
    else
        str = mins + ':' + secs_str;
    return str;
    }

    function formatHSObjTime(hsobj, frame) {
        var starttimeS = Math.round(frame/1000);
        var title = hsobj.hse_name;
        title = title == "" ? "&lt;untitled&gt;" : title;
        title = title + " (" + secs_to_HHMMSS(starttimeS);
        title = title + " - " + secs_to_HHMMSS(Math.round(hsobj.duration/1000) + starttimeS) +  ")";
        return title;
    }

    adsMgr.setAdsListChangeUpdateCtrl = function(ctrl, fcnname) {
        adsListChangeUpdateCtrl = ctrl;
        adsListChangeUpdateCtrlFcn = fcnname;
        fillListWithAds(); //TODO REMOVE FROM HERE
    }

    function deleteAdFromList(hsobj, frame) {
        if (!adsListChangeUpdateCtrl  || !adsListChangeUpdateCtrlFcn) 
            return;
        var row = $('#adlist-'+ hsobj.db_id);
        if (row.length == 1) {
            row.remove();
        }
        else
            alert("deleteAdFromListinconsistent state");
    }

    function updateAdInList(hsobj, frame) {
        if (!adsListChangeUpdateCtrl  || !adsListChangeUpdateCtrlFcn) 
            return;
        var db_id = hsobj.db_id;
        var row = $('#adlist-'+ hsobj.db_id);
        if (row.length == 1) {
            row.empty();
            var title = formatHSObjTime(hsobj, frame);
            row.append("<li id='adlist-" +hsobj.db_id + "'><a href='javascript:" + adsListChangeUpdateCtrlFcn+ "(" + hsobj.db_id + " ," + frame  + ")'><strong>" + title + "</strong></a></li>");
        }
        else
            alert("updateAdInList inconsistent state");
    }

    function addAdToList(hsobj, frame) {
        if (!adsListChangeUpdateCtrl  || !adsListChangeUpdateCtrlFcn) 
            return;
        var row = $('#adlist-'+ hsobj.db_id);
        if (row.length == 0) {
            var title = formatHSObjTime(hsobj, frame);
            adsListChangeUpdateCtrl.append("<li id='adlist-" +hsobj.db_id + "'><a href='javascript:" + adsListChangeUpdateCtrlFcn+ "(" + hsobj.db_id + " ," + frame  + ")'>" + title + "</a></li>");
        }
        else {
            alert("addAdToListinconsistent state");
        }
    }
    //Internal:
    function fillListWithAds() {
        if (!adsListChangeUpdateCtrl  || !adsListChangeUpdateCtrlFcn) 
            return;
        adsListChangeUpdateCtrl.empty();
        var hsobj;
        var title;
        for (var frameStr in jsonResultObj){
            for (var arridx in jsonResultObj[frameStr]){
                hsobj = jsonResultObj[frameStr][arridx];
                title = formatHSObjTime(hsobj, frameStr);
                if (hsobj.hse_gid == adsMgr.getGID())
                    adsListChangeUpdateCtrl.append("<li id='adlist-" +hsobj.db_id + "'><a href='javascript:" + adsListChangeUpdateCtrlFcn+ "(" + hsobj.db_id + " ," + frameStr  + ")'><font color=\"red\">" + title + "</a></li>");
                else    
                    adsListChangeUpdateCtrl.append("<li id='adlist-" +hsobj.db_id + "'><a href='javascript:" + adsListChangeUpdateCtrlFcn+ "(" + hsobj.db_id + " ," + frameStr  + ")'>" + title + "</a></li>");
            }//for
        }//for
    };
    adsMgr.setGID = function(gid0) {
        adsMgr.gid = gid0;
    }
    adsMgr.setHSSID = function(hssetid0) {
        adsMgr.hssetid = hssetid0;
    }
    adsMgr.setOwnerID = function(ownerid0) {
        adsMgr.ownerid = ownerid0;
    }
    adsMgr.registerClickAdCB = function(cb) {
        clickAdCB = cb;
    };
    adsMgr.setData = function(dataobj) {
        data = dataobj;
        hotspotDigestData(dataobj); 
        changeAllAds();
    }

/*****************
    function generate() {    
        return; //hacky
    //For now we need some fake data to test the GA reports:
    var fakebanner = function myLoop(i, t, title, campaign) {          
        setTimeout(function () {   
            generateFakeData(1 , ((i%2 == 0 && t <= 60000) ? 2 : 0), t, title, campaign);
            t += 60000;
            if (t == 60000*4) {
                i -= 1;
                t = 0;
            }
            if (i > 0)
                myLoop(i, t, title, campaign);
            else if (window.gaTrackHotspots!=undefined) 
                window.gaTrackHotspots.printDump();
    
        }, 3000)
    }

    var fakehotspot = function myLoop1(i, t, title, campaign) {          
        setTimeout(function () {   
            var response = 0;
            if (t <= 60000) {
                var mod = i%3;
                if (mod == 0)
                    response = 0;
                else if (mod == 1)
                    response = 1;
                else
                    response = 2;
            }
            generateFakeData(2 , response, t, title, campaign);
            
            t += 60000; //every 60 sec a hotspot appears
            if (t == 60000*4) { // a 4-minute long video
                i -= 1;
                t = 0;
            }
            if (i > 0)
                myLoop1(i, t, title, campaign);
            else if (window.gaTrackHotspots!=undefined) 
                window.gaTrackHotspots.printDump();
    
        }, 3000) //3000
    }

    
    fakebanner(29, 0, "Fountain of Youth", "PhaseShop|HK Campaign");  //banner
    
    
    fakehotspot(35, 0, "Fountain of Youth", "PhaseShop|HK Campaign");  //hotspot

    
    
    fakebanner(10, 0, "24K Gold Capsules", "PhaseShop|HK Campaign");  //banner
    fakehotspot(35, 0, "Herbal Mask", "PhaseShop|HK Campaign");  //hotspot

    fakebanner(21, 0, "Direct Flight to Shanghai", "AirAsia");  //banner
    fakehotspot(35, 0, "Direct Flight to Shanghai", "AirAsia");  //hotspot
       
    } //function generate()

    function generateFakeData(type, response, t, title, campaign) {
        //The basic of the hotspot of banner being shown.
        //I think no need?
        //This will be generated independently. It happens when the hotspot comes out.
        //the most basic one.
        var tid = hsGATrackerId;
        doGAMaybe(type, 0, title, t, campaign, tid);
    
        //The time is to be treated as a string.

        if (type == 1 && response == 2) { //is banner and clicked on it
            //2 == view ad URL
            setTimeout(function() {
                doGAMaybe(type, 2, title, t, campaign, tid);
            }, 2000);
        }
        else if (type == 2 && response == 2) { //is hotspot and user clicked on hotspot and the pic
            //simulate 2 events.
            //1 == view pic
            //2 == view ad URL
            setTimeout( function() {                
                doGAMaybe(type, 1, title, t, campaign, tid);
                setTimeout( function() {
                    doGAMaybe(type, 2, title, t, campaign, tid);
                }, 1000);
            }, 2000);
        }                       
        else if (type == 2 && response == 1) { //is hotspot and user clicked on hotspot
            setTimeout(function() {                
                doGAMaybe(type, 1, title, t, campaign, tid);
            }, 2000);            
        }
    }
*****************/
})(jQuery);
var videoMgr;
var adsMgr;



