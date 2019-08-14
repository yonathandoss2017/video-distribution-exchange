//before validate 
function hideError(str) { 
    $("#hse_error").text('').slideUp();    
}

//Best is something to give a different colour to the offending control
function showError(str) { 
    alert(str);
    ////$("#hse_error").text(str).slideDown();    
}

function showFatalError(str) { alert(str); }
function showDBError(str, details) { alert(str + '(' + details + ')'); }
function dbgMsg(str) { alert(str); }





(function($){
    window.openHotspot = function(db_id, frame) {
        editHSObjInPanelMaybe(db_id, frame);
    };

    window.deleteHotspot = function(db_id, frame) {

        if (currObj && currObj.db_id == db_id)
            editHSObjDelete();
        else {
            var obj = adsMgr.getAd(db_id, frame);
            editHSObjDelete(obj, frame);
        }
    }

    //add creative OK, but edit??
    window.creativeUpdated  = function(db_id, frame) {
        showFatalError(hseError['err_notsupported']);
        //basically the picture needs to be reloaded.
        //Note: it is already in the system (hotspot.js system)
        //How to update one and all?!
    };

    //gid, URL, graphic fullurl, advertiser
    window.useCreative = function(gid, name, image_url, target_url, advertiser) {

        if (!currObj) 
            showError(hsePrompt['needHotspot']);
        else {       
            window.populateCreativesTab(true, gid, name, image_url, target_url);
            ////$('#creative-tab-li').trigger('click'); 
        }
    };


    var adsMgr, videoMgr;
    var currentVideo = {
            'partnerId' : 0,
            'videourl' : "",
            'entryId' : "",
            "dim" : "",
            "name" : ""
            };
    //We only create the hotspotset entry from HSEditor, when user
    //adds the first hotspot for the video.
    //Otherwise, the video link may not even be valid, and we have a useless
    //hotspotset table row.
    window.keepVideoParams = function(entryId, partnerId, url, name) {
        currentVideo.entryId = entryId;
        currentVideo.partnerId = partnerId;
        currentVideo.url = url;
        currentVideo.name = name;
    }

/************************************************************
 *
 * UTILITIES
 *
 ************************************************************/

/*******************************************************
 *  Talking to the Database via AJAX
 *******************************************************/
function genericAjaxCall(url, mydata, successhandler, errhandler) {
    $("body").css("cursor", "progress");
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    mydata.property_id = window.propertyid;

    var paramObj = {
        url: url,
        data: mydata,
        type:'post',
        error:function(response){
            showDBError(hseError['ajax']);
            errhandler(response);
        },
        success:function(response){
            successhandler(response);

         }
    }; 
    
    if (url.indexOf('creative') != -1 && url.indexOf('search') == -1) {
        paramObj.processData = false;
        paramObj.contentType = false;
    }

    $.ajax(paramObj).done(function( response ) {
            $("body").css("cursor", "default");
         }
    );
}

function deleteHotspotAsync(obj, fcn) {
    if (!obj) return;
    var data = {
            "id" : obj.db_id /* HS object ID is unique in whole table */
    };
    genericAjaxCall(
        '/manage/' + window.propertyid + '/ivwp/hotspots/hotspot/destroy',
        //'/hotspot/hotspot/destroy', 
        data,
        function(response) { fcn(response) },
        function(response) {});
}

function addOrUpdateHotspotAsync(isNewObj, obj, starttime, fcn) {
    if (!obj) return;
    var data = {
        "name" : obj.hse_name,
        "start_time" : starttime,
        "duration" : obj.duration,
        "ad_type": obj.infoWindow.ad_type,
        "ad_subtype": obj.infoWindow.ad_subtype,
        "indicator" : 1,
        "rect": obj.rect,
        "adgraphic_id": obj.hse_gid
    };
    if (isNewObj)
        data.hotspotset_id = adsMgr.getHSSID();
    else
        data.id = obj.db_id;

    if (data.hotspotset_id == -1) {
        //We will need to create the hotspot set.
        data.partner_id = currentVideo.partnerId;
        data.service_url = currentVideo.url;
        data.entry_id = currentVideo.entryId;
        data.video_name = currentVideo.name;
        data.dimensions = dimensions;
    }

    genericAjaxCall((isNewObj ? 
        '/manage/' + window.propertyid + '/ivwp/hotspots/hotspot/store' :
        '/manage/' + window.propertyid + '/ivwp/hotspots/hotspot/update'),
        data,
        function(response) { fcn(response); },
        function(response) {});
}

function addOrUpdateGraphicNHotspotAsync(isNewObj, obj, starttime, fcn) {
    if (!obj) return;
    //The form needs to have the other stuff too.
    //Target_URL and name
    var data = new FormData($("#imageupload_form")[0]);
    genericAjaxCall(
        '/manage/' + window.propertyid + '/ivwp/hotspots/creative' ,
        data,
        function(response) {
            if (response.status == "success") {
                obj.hse_gid = response.id;
                obj.infoWindow.image_url = response.image_url;
                addGraphicToListMaybe(obj);
                showCreativesMaybe();
                setBasicContentsAdGraphicsPanel();
                //So that this can show up in the list of graphics
                //referenced in this video user is editing.
                //setBasicContentsAdGraphicsPanel(); //hmmm, what is the
                //best place to call this.
                addOrUpdateHotspotAsync(isNewObj, obj, starttime, fcn);
            }
            else {
                alert("Error in uploading graphic. ");
                editHSObjCleanup(true);
            }
        },
        function(response) {
            alert("Unknown error in uploading graphic. ");
            editHSObjCleanup(true);
        });
}

function getAdGraphicsByKeywordsAsync(keywords, fcn) {
    var data = {
        "keywords" : keywords
    };
    genericAjaxCall(
       '/manage/' + window.propertyid + '/ivwp/hotspots/creative/search' ,
        data,
        function(response) { setDBContentsAdGraphicsPanel(response.data); },
        function(response) {});
}

function queryHotspotsJSONAsync() {
    var data = {
        "hssetid": adsMgr.getHSSID()
    };
    genericAjaxCall(
        '/manage/' + window.propertyid + '/ivwp/hotspots/editor/json' ,
        data,
        function(response) {
            if (response.status != "success" && adsMgr.getHSSID() != -1) {
                showDBError(hseError['read'], response.data);
                return;
            }
            adsMgr.setData((response));
            adsMgr.doForAllHSObjs(addGraphicToListMaybe);
            setBasicContentsAdGraphicsPanel();
            enableHSEditor();
        },
        function(response) {});
}

/*******************************************************
 *  General stuff
 *******************************************************/
function getDefaultHSObj(adtype) {
    if (adtype == 2)
        return cloneIt(defaultNewHotspot);
    else if (adtype == 1)
        return cloneIt(defaultNewBanner);
    else
        return cloneIt(defaultNewVertRect);
};

function adTypeToStr(ad_type) {
    if (ad_type == 2)
        return hseLabelInt['hotspot'];
    else if (ad_type == 1)
        return hseLabelInt['banner'];
    else
        return hseLabelInt['vertrect'];
}

function seconds_to_HHMMSS(s) {
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

function HHMMSS_OR_MMSS_to_secs(str) {
   var s, m;
   var p = str.split(':'),
      s = 0, m = 1;
   //TODO: CHECK FOR ERRORS if (p.length < 2)
    while (p.length > 0) {
        s += m * parseInt(p.pop(), 10);
        m *= 60;
    }
    return s;
}

//For deep cloning of an HS object (or any object)
function cloneIt(o) {
   return ( $.extend(true, {}, o) );
}


/*******************************************************
 *  AdGraphics related GUI parts
 *******************************************************/
window.populateCreativesTab = function(initialPopulation, gid, gname, image_url, target_url) {
    var tt = $('#hse_previewImage-tt');
    //??? once enable the jq inclusion this gives problem
    tt.tooltip('dispose');
    //tt.prop('title', 'super new tooltip');
    tt.prop('title', hseLabelInt['creative'] + ': <br>' + 
        gname + '<br>' + 
        target_url );
    
    tt.tooltip({html : true, delay:{ "show": 500  }});

    $("#hse_img_panel").css('display', 'none');
    $("#hse_previewImage").css('visibility', 'hidden');

    if (image_url != ''){
        $("#hse_img_panel").css('display', 'block');
        $("#hse_previewImage").css('visibility', 'visible');
        $("#hse_previewImage").attr('src', image_url).slideDown();
    }
    /* should not do it here ? */
    
    currObj.hse_gid = gid;
    currObj.infoWindow.image_url = image_url;
    currObj.infoWindow.url = target_url;
    currObj.infoWindow.hse_gname = gname;
    

    if (!initialPopulation) 
        $("#hse_searchstr").click();
    /* if (initialPopulation) {
        $("#hse_searchstr").click();
        editHSObjSetDirty();
    }*/
}

function setDBContentsAdGraphicsPanel(data) {
    var obj;
    var htmlStr = '';
    for (var i = 0; i < data.length; i++) {
        obj = data[i];
        if (!obj.hasOwnProperty("image_url"))
            obj.image_url = obj.folder + "/" + obj.filename;
        htmlStr += "<a href='javascript:populateCreativesTab(false, " + obj.id +
            ", \"" + escape(obj.hse_gname) + "\", \"" + obj.image_url + "\", \"" +
            obj.target_url +
        "\")'><img height=\"52\" width=\"90\" src=\"";
        htmlStr += obj.image_url + "\" class=\"hastip  tiny_adgraphics_img\" title=\"";
        htmlStr += obj.target_url + "," + obj.hse_gname;
        htmlStr += "\"/></a>";
    }
    var contents = $(htmlStr);
    var dest = $("#hse_graphicsPanel");
    dest.empty();
    contents.appendTo(dest);
    $( ".hastip").tooltip({show: {effect:"none", delay:0}, html: true});
}

//choose one the action is populate the current one.
function setBasicContentsAdGraphicsPanel() {
    var htmlStr = "";
    //make the dropdown disappear and put the cursor at the right text box...?
    /*
    htmlStr += "<span><a href='javascript:setAdGraphicsFields(false, -1, \"\" , \"\", \"\")'>Add a new creative.</a></span><hr><p>";
    htmlStr += "<span>Hint: Type words to search (by name) your existing creatives.</span><hr>";
    */
    htmlStr += '<span>' + hseLabelInt['creatives'] + ':</span><br>';

    for (var gid in graphicObjs){
        var obj = graphicObjs[gid];

        htmlStr += "<a href='javascript:populateCreativesTab(false, " + gid +
            ", \"" + escape(obj.hse_gname) + "\", \"" + obj.image_url + "\", \"" +
            obj.target_url +
        "\")'><img height=\"52\" width=\"90\" src=\"";
        htmlStr += obj.image_url + "\" class=\"hastip tiny_adgraphics_img\" title=\"";
        htmlStr += obj.target_url + "," + obj.hse_gname;
        htmlStr += "\"/></a>";
    }
    var contents = $(htmlStr);
    var dest = $("#hse_graphicsPanel");
    dest.empty();
    contents.appendTo(dest);
    $( ".hastip").tooltip({show: {effect:"none", delay:0}});
}

var graphicObjs = {}; //all graphic objects used in this
//This is used for displaying the adgraphics used in THIS hotspotset
function addGraphicToListMaybe(hsobj) {
    if (hsobj.infoWindow.hse_gname == '') return;
    var gid = hsobj.hse_gid;
    var obj = graphicObjs[gid];
    if (!obj) {
        graphicObjs[gid] = obj = cloneIt(defaultNewInfoWindow);
        obj.content = "";
        obj.target_url = hsobj.infoWindow.url;
        obj.image_url = hsobj.infoWindow.image_url;
        obj.ad_type = hsobj.infoWindow.ad_type;
        obj.hse_gname = hsobj.infoWindow.hse_gname;
    }
}

/*******************************************************
 *  Rect frame (size position of hotspot)-related GUI parts
 *******************************************************/
var wpb_video_wrapper = null;
function get_wpb_video_wrapper() {
   if (wpb_video_wrapper)
       return wpb_video_wrapper;
   wpb_video_wrapper = $("#wpb_video_wrapper");
   return wpb_video_wrapper;
}

function editHSObjSetRectFrame(boxRectInfo) {
    if (boxRectInfo) {
        //console.log('updating the currObj.rect b4:' + currObj.rect);
        currObj.rect = boxRectInfo.join();
        //console.log('updating the currObj.rect after:' + currObj.rect);
        editHSObjSetDirty();
    }
}

function editHSObjCaptureRectFrameAux(subtype) {
    var that = $('#hs_new');
    if (that.length == 0)
        return null;
    var wa_h = jqeltWorkingArea.height();
    var wa_w = jqeltWorkingArea.width();
    if (!wa_h || !wa_w) return null; //possible for fullscreen mode COPYCOPY

    var top, left;
    var str = that.css("top");
    if (str.indexOf("%") !== -1)
        top = Math.round((parseInt(str.replace("%", ""))));
    else 
        top = Math.round((parseInt(str.replace("px", ""))*100)/wa_h);
    
    //The thing sometimes gives % sometimes gives px....
    str = that.css("left");
    if (str.indexOf("%") !== -1)
        left = Math.round((parseInt(str.replace("%", ""))));
    else {
        left = Math.round((parseInt(str.replace("px", ""))*100)/wa_w);
    }

    var w, h;
    str = that.css("width"); //that.get(0).style.width;
    if (str && str.indexOf("%") !== -1)
        w = Math.round((parseInt(str.replace("%", ""))));
    else { /* This one not so reliable. Use this if no better ways */
        w = Math.round((parseInt(str.replace("px", "")) * 100)/wa_w);
        //dangerous
        //w = Math.round((that.width()*100)/wa_w);
        ////that.css("width", w+"%");
    }

    //str = that.get(0).style.height;
    str = that.css("height");
    if (str && str.indexOf("%") !== -1)
        h = Math.round((parseInt(str.replace("%", ""))));
    else { /* This one not so reliable. Use this if no better ways */
        //h = Math.round((that.height()*100)/wa_h);
        h = Math.round((parseInt(str.replace("px", "")) * 100)/wa_h);
        ////that.css("height", h+"%");
    }
    if (subtype != subtype_hotspot) {
        //We can only do that for BANNER AH!!!
        var furthestBottom = 100 - Math.round(videoMgr.getSliderHeight()*100/jqeltWorkingArea.height());
        console.log("furthest bottom " + furthestBottom);
        //not just that
        if (top + h > furthestBottom) {
            var diff = top + h - furthestBottom;
            top = Math.max(0, top - diff);
            console.log("repaired top is " + top);
            //need to fix this up.
            that.css("top", top + "%");
        }
        if (subtype == subtype_banner_right_to_left) {
            left = 100-w; //cannot use 100, must -1 ...
            that.css("left", (left-1) + "%");   

        }
    }
    var boxRectInfo = [0,0,0,0];
    boxRectInfo[0] = top;
    boxRectInfo[1] = left;
    boxRectInfo[2] = w;
    boxRectInfo[3] = h;
    return boxRectInfo;
}

function adjustDraggingConstrainingElt(subtype, boxRectInfo) {
    var tbda = jqeltBannerConstraint;
    tbda.css('left', '0%');
    tbda.css('top', '0%');
    tbda.css('width', '100%');
    console.log("DEBUG SLIDER HEIGHT " + videoMgr.getSliderHeight());

    var height_format_string = 'calc(100% -  '  +  videoMgr.getSliderHeight() + 'px)';
    console.log("format string " + height_format_string);
    //tbda.css({ 'height': 'calc(100% -  36px)' });
    tbda.css({ 'height': height_format_string });

    if (subtype == subtype_banner_left_to_right)
        tbda.css('width', boxRectInfo[2] + '%');
    else if (subtype == subtype_banner_right_to_left) {
        var width = parseInt(boxRectInfo[2]);
        tbda.css('width', width + '%');
        var left = 100-width-1;
        tbda.css('left', left + '%');
    } 
    else if (subtype == subtype_banner_top_down) {
        tbda.css('top', '0%');
        tbda.css('height', boxRectInfo[3] + '%');
    }
    else  {
        var tmp = Math.round(videoMgr.getSliderHeight()*100/jqeltWorkingArea.height());
        tbda.css('top', 100-tmp-boxRectInfo[3]+ '%');
        tbda.css('height', boxRectInfo[3] + '%');
    }
}

var subtype_hotspot = 0;
var subtype_banner_left_to_right = 1;
var subtype_banner_right_to_left = 2;
var subtype_banner_top_down = 3;
var subtype_banner_bottom_up = 4;

var allowed_resize_directions = [
    'n, e, s, w, ne, se, sw, nw',  /* hotspot rect resize */
    'ne, se', /* left to right banner */
    'nw, sw', /* right to left banner */
    'se, sw', /* top down banner */
    'ne, nw' /* bottom up banner */
];


function editHSObjPresetPositioningRect(aspect_ratio) {
    console.log('   presetPositioningRect ar=' + aspect_ratio);
    //var aspect_ratio = ((width == 0 || height == 0) ? 1.5 : width/height);
    var video_aspect_ratio = jqeltWorkingAreaAR;
    var type = currObj.infoWindow.ad_subtype;
    var top, left, width, height;

    //first case is we have a new graphic and hence new aspect_ratio 
    //information.
    if (aspect_ratio != -1) {
        if (aspect_ratio > video_aspect_ratio) {
            width = 70;
            height = Math.round(70*video_aspect_ratio/aspect_ratio);
        }
        else {
            height = 70;
            width = Math.round(70*aspect_ratio/video_aspect_ratio);   
        }
    }
    else { //stick to existing sizing. Only change is direction (hence start pos) of banner
        var boxRectInfo = currObj.rect.split(",");
        width = parseInt(boxRectInfo[2]);    
        height = parseInt(boxRectInfo[3]);    
    }
        
    if (type == subtype_banner_left_to_right) {
        top = Math.round((100-height-videoMgr.getSliderHeight()*100/jqeltWorkingArea.height())/2);
        left = 0;
    }
    else if (type == subtype_banner_right_to_left) {
        top = Math.round((100-height-videoMgr.getSliderHeight()*100/jqeltWorkingArea.height())/2);
        left = 100-width;
     
    }
    else if (type == subtype_banner_top_down) {
        top = 0;
        left = Math.round((100-width)/2);
    }
    else  {
        left = Math.round((100-width)/2);
        top = 100-height-Math.round(videoMgr.getSliderHeight()*100/jqeltWorkingArea.height());
    }
    currObj.rect = top + "," + left + "," + width + "," + height;
    editHSObjSetDirty();
    currObj.keeprect = false;//hack
}

//This is for banner only. If the creative is changed, then we need to put
//different suggested initial banner outline on the video area.
//Different creatives have different aspect ratios, hence this is needed.
//If the user choose "add new creative", but have yet to specify any, then
//we will have a default aspect ratio & in the rect we will give a hint for him
//to specify the graphic.
function handleCreativeChanged(cxt) {
    if (currObj) {
        console.log('handleCreativeChanged (currObj valid, ' + cxt + ", ad_type=" + currObj.infoWindow.ad_type);
        if (currObj.infoWindow.ad_type != 1)
            return;
    }
    else {
        console.log('handleCreativeChanged (currObj NULL, ' + cxt);
        return;
    }
    currObj.infoWindow.ad_subtype = parseInt($("#hse_bannersubtypeCombo" ).val());
    console.log('   banner subtype=' + currObj.infoWindow.ad_subtype);

    //var img = $( "#" + getCreativesTabPrefix() + "previewImage"); 
    var img = $("#hse_previewImage");
    var elmnt = img[0];
    var ar = 1.5;
    if (elmnt && elmnt.naturalWidth > 0 && elmnt.naturalHeight > 0)
        ar = elmnt.naturalWidth/elmnt.naturalHeight;
    if (!currObj.keeprect) {
        //the rect info for banner positioning must have been from the database
        //just use it then
        editHSObjPresetPositioningRect(ar);
    }
    editHSObjDrawPositioningRect(img.attr('src'), ar);
    currObj.keeprect = false;
}


/*
 * Sets up the div the user uses to position the hotspot, banner
 * Simple for hotspot actually - can be any artibrary size / pos within the video area
 * Less so for banner: 4 directions (each with constraints on placement), and apsect
 * ratio of banner graphic must be kept
 * Some hacks required coz the jquery draggable, resizable (seems quite well known) to 
 * have bugs.
 */   
 
function editHSObjDrawPositioningRect(imgsrc, aspect_ratio) {
    //console.log('   editHSObjDrawPositioningRect ar=' + aspect_ratio);
    var visible = true;
    var oldhs = $('#hs_new');
    if (oldhs.length > 0) 
        visible = oldhs.is(":visible");
    oldhs.remove();
    var cannot_use = false;
    var subtype = currObj.infoWindow.ad_subtype;
    var resize_dir = allowed_resize_directions[subtype];
    var bannerImageSegment, rectMsg;
    if (subtype == subtype_hotspot) { 
        rectMsg = hseLabelInt['rect4Hotspot'];
        bannerImageSegment = '';
    }
    else {
        bannerImageSegment = '<img src="' + imgsrc + '">';
        if (imgsrc == '') {
            cannot_use = true;
            rectMsg = hseLabelInt['selectCreative'];
        }
        else 
            rectMsg = ((subtype == subtype_banner_left_to_right || subtype == subtype_banner_right_to_left) ?
                hseLabelInt['rect4LRBanner'] : hseLabelInt['rect4TBBanner']);
    }
    //At this juncture, a sensible default value (respecting aspect ratio) should be
    //represent in "rect" already
    var boxRectInfo = currObj.rect.split(","); 
    var attr = {
        'position': 'absolute',
        'pointer-events': 'auto',
        'background-size': '100% 100%',
        'top': boxRectInfo[0] + '%',
        'left': boxRectInfo[1] + '%',
        'height': boxRectInfo[3] + '%',
        'width':  boxRectInfo[2] + '%',
    }
    var that = $('<div id="hs_new" class="hotspot hsetooltip hs_hotspot">'
    + bannerImageSegment + '<div class="resize_repos_box_title"><span>' + rectMsg + '</span></div></div>').css(attr).appendTo(jqeltWorkingArea);
    
    if (cannot_use) return; //If the creative is not chosen for the case of banner, 
    //Just show the rect outline, but it cannot be resized or moved.

    if (subtype != subtype_hotspot) {
        //constraint box for dragging of banner.
        //e.g. Left to right must have its left edge co-inciding with the left of video area.
        adjustDraggingConstrainingElt(subtype, boxRectInfo);
    }
                    
     that.resizable({
            handles: resize_dir,
            aspectRatio:  subtype == subtype_hotspot ? false : aspect_ratio,
            containment: jqeltWorkingArea, //ideally it is not jqeltWorkingArea, but that minus
            //the control bar (hover). However, I just ran into TOO MANY problems with that with the
            //jquery resizable+draggable ctrl.
            //So let's just use the jqeltWorkingArea. If the banner covers the control bar,
            //it will be fixed up in the editHSObjCaptureRectFrameAux
            create: function() {
            },
            start: function (event, ui) {
                get_wpb_video_wrapper().css({'pointer-events': 'none'});
            },
            stop: function (event, ui) {
                get_wpb_video_wrapper().css({'pointer-events': 'auto'});
                var boxRectInfo = editHSObjCaptureRectFrameAux(subtype);
                editHSObjSetRectFrame(boxRectInfo);
                //We have to think whether to store it.
                if (subtype != subtype_hotspot) 
                    adjustDraggingConstrainingElt(subtype, boxRectInfo);
            }
        }).draggable(
            {
                containment: (subtype == subtype_hotspot ? jqeltWorkingArea : jqeltBannerConstraint),
                scroll: false,
                drag: function(){
                },
                stop: function(){
                editHSObjSetRectFrame(editHSObjCaptureRectFrameAux(subtype));
                }
            });
    if (visible)
        that.show();
    else
        that.hide();
}

/************************************************************
 *
 * ADD / EDIT PANEL
 *
 ************************************************************/
//somehow we need to capture the original frame of the hs object edited

//Maybe we can get rid of this?
//Because the clone part is controlled by us!
var isNewObj = false;
var currObjFrame = null;
var currObjNewFrame = null;
var currObj = null;
var isCurrObjDirty = false;
var jqeltWorkingArea = null;
var jqeltBannerConstraint = null;
var jqeltWorkingAreaAR = 1;
var dimensions = null;

function wasHSOBjPanelHidden() {
    //var editPanel = $(".add-panel");
    /*
    if ($("#hse_panel").css('visibility') == 'hidden') {
        $("#hse_panel").css('visibility', 'visible');
        $("#hse_buttons_panel").css('visibility', 'visible');
        return true;
    }
    */
    if ($("#hse_panel").is(":hidden")) {
        $("#hse_blank_panel").hide();
        $("#hse_buttons_panel").css('visibility', 'visible');
        $("#hse_panel").show();
        return true;
    }
    /* if (editPanel.is(":hidden")) {
        editPanel.slideDown();
        return true;
    }*/
    return false;
}

function editHSObjSetDirty() {
    isCurrObjDirty = true;
}

function editHSObjIsDirty() {
    return isCurrObjDirty;
}

function editHSObjCleanup(isToHidePanel) {
    var control = $("#hse_image");
    control.replaceWith( control = control.clone( true ) );
    currObj = null;
    currObjFrame = null;
    currObjNewFrame = null;
    isNewObj = false;

    $("#searchRadio").prop("checked", true);
    $("#hse_cnew_panel").hide();
    $("#hse_csearch_panel").show();
    $("#hse_cnew_url").val('');
    $("#hse_cnew_gname").val('');
    $("#hse_imageuploadBtn").val(null);
    $("#hse_previewImage").attr('src', '')
    
    isCurrObjDirty = false;
    $('#hs_new').remove();
    if (isToHidePanel)  {
        $("#hse_panel").hide();
        $("#hse_blank_panel").show();
        $("#hse_buttons_panel").css('visibility', 'hidden');
    }
    return;
}

function editHSObjClose() {
    isCanSwitchHSEditObj(null);
}

//Before we close the obj currently being edited, we check with user
//whether he is sure to abort any unsaved changes.
function isCanSwitchHSEditObj(nextobj) {
//if I want to switch to another new object.
    if (!editHSObjIsDirty()) {
        editHSObjCleanup(nextobj == null ? true : false); //if no new obj to open, roll back up panel
        return true; //no editing done with the current ad, just close then.
    }
    if (nextobj && (nextobj.db_id != -1) && nextobj.db_id == currObj.db_id)
        return false; //false coz no need to switch
    if (!nextobj) {
        if (confirm(hsePrompt['confirmReset'])) {
            editHSObjCleanup(true); //no new obj to show. just roll back the pane.
            return true;
        }
    }
    else if (confirm(hsePrompt['confirmReset'])) {
        editHSObjCleanup(false); //keep the panel there.
        return true;
    }
    return false;
}

//This is called by hotspot.js
function hse_areaInfoReadyCB(dim) {
    jqeltBannerConstraint = $("#banner_dragging_area");
    jqeltWorkingArea = $("#working_area");
    jqeltWorkingArea.css("left", dim.left);
    jqeltWorkingArea.css("top", dim.top);
    jqeltWorkingArea.width(dim.width);
    jqeltWorkingArea.height(dim.height);
    if (dimensions == null) {
        dimensions = dim.videoWidth + "x" + dim.videoHeight;
        //still do not save it yet. need to save to database record
        //Wait till user saves first hotspot
    }
    jqeltWorkingArea.show();
    
    jqeltWorkingAreaAR = dim.width/dim.height;

}


window.editHSObjInPanelMaybe = function(db_id, frame) {
    var obj = adsMgr.getAd(db_id, frame);
    if (!obj || (!wasHSOBjPanelHidden() && !isCanSwitchHSEditObj(obj)))
        return;
    var obj = cloneIt(obj);

    videoMgr.doSeek( frame );
    //Somehow calling kaltura to do seek and pause in close succession does not work!
    setTimeout(function (){
        videoMgr.doPause();
        editHSObjPopulatePanel(obj, frame, false, true);
    }, 500); // How long do you want the delay to be (in milliseconds)?
}

//a callback we register with adsMgr.
//this gets called when the user clicks on a hotspot in the playback area.
function hse_clickAdCB(obj, frame) {
    //if the object is already open then no need lah.
    if (!wasHSOBjPanelHidden() && !isCanSwitchHSEditObj(obj))
        return;
    var obj = cloneIt(obj);
    editHSObjPopulatePanel(obj, frame, false, false); //3rd param is isnewobj
    //4th is whether to show the resize-repositioning rect by default
}

//Called from our side (Add hotspot button, dropdown menu item)
function addHSObjInPanelMaybe(adtype, frame) {
    if (!wasHSOBjPanelHidden() && !isCanSwitchHSEditObj(getDefaultHSObj(adtype)))
        return;
    var frame = videoMgr.getPlayhead();
    var obj = getDefaultHSObj(adtype);
    cloneIt(obj);
    editHSObjPopulatePanel(obj, frame, true, true); //param 3 is NEW obj or not,
    //param 4 is whether should show the resize repos rect when you open
}

function editHSObjPopulatePanel(obj, frame, isnewobj, showrect) {
    isNewObj = isnewobj;
    if (!isnewobj) {
        $("#hse_commitBtn").html('UPDATE');
        $("#hse_deleteBtn").show();
        
    }
    else {
        $("#hse_deleteBtn").hide()
        $("#hse_commitBtn").html('ADD');
        ///Bad effect $("#hse_previewImageDiv").slideUp();
    }
    currObj = obj;
    currObjFrame = frame;
    currObjNewFrame = frame;
  
    $("#hse_searchstr").val('');
    setBasicContentsAdGraphicsPanel();
    

    $("#hse_name").val(obj.hse_name);
    $("#hse_titletext").text((isnewobj? hseLabelInt['new'] : hseLabelInt['edit']) + ' ' + adTypeToStr(obj.infoWindow.ad_type));
    //TODO: to have or not to have $("#hse_adtitle").val(obj.infoWindow.title);
    var starttimeS = Math.round(frame/1000);
    if (starttimeS == 0 && isnewobj) starttimeS = 1;
    $("#hse_starttime").val(seconds_to_HHMMSS(starttimeS));
    var durationS = Math.round(obj.duration/1000);
    if ($( "#hse_stopordurationCombo" ).val() == "duration")
        $("#hse_stoptimeorduration").val(durationS); 
    else
        $("#hse_stoptimeorduration").val(seconds_to_HHMMSS(durationS + starttimeS));

    /////toggleCreativePanel(!isnewobj);
    if (obj.infoWindow.ad_type == 1) {
        $( "#hse_bannersubtypeDiv" ).show();
        $( "#hse_bannersubtypeCombo" ).val(obj.infoWindow.ad_subtype);
    }
    else
        $( "#hse_bannersubtypeDiv" ).hide();
    //$('#search-tab-li').trigger('click'); 
    currObj = obj;
    currObj.keeprect = true; //ok this is a hack..
    //For an existing banner, the load image below will 
    populateCreativesTab(true, obj.hse_gid, obj.infoWindow.hse_gname,
        obj.infoWindow.image_url, obj.infoWindow.url);
    if (obj.infoWindow.ad_type == 1) {
        if (isNewObj) {
            editHSObjPresetPositioningRect(1.5);
            editHSObjDrawPositioningRect('', 1.5);
        }
    }
    else {
        editHSObjDrawPositioningRect('');
    }
}

function editHSObjDelete(obj, frame) {
    /*
    if (obj == undefined) {
        alert("editHSObjDelete argument is undefined");
    }
    else
        alert("editHSObjDelete argument is defined");
    */

    var deleteObj = (obj == undefined ? currObj : obj);
    var deleteObjFrame = (obj == undefined ? currObjFrame : frame);
    if (!confirm(hsePrompt['confirmDelete']))
        return;
    var fcn = function(data) {
        if (data.status != 'success') {
            showDBError(hseError['delete'], data.info);
            editHSObjCleanup(true);
            return;
        }
        adsMgr.deleteAd(deleteObj, deleteObjFrame);
        editHSObjCleanup(true);
        showHotspotsMaybe(); //update the iframe.
    }
    deleteHotspotAsync(deleteObj, fcn);
    return;
}

function isNewCreative() {
    var sel = $('input[name=creativeRadioGrp]:checked').val(); 
    return (sel==1);
}

function editHSObjValidateNewCreative(previewonly) {
    var url = $("#hse_cnew_url").val().trim(); //should check, ideally.
    var gname = $("#hse_cnew_gname").val().trim(); //should check, ideally.

    if (!previewonly) {
        if ($("#hse_previewImage").attr('src') == '') {
            alert("Please upload an image for this ad first, or choose one of your existing creatives.");
            return false;
        }
        if (gname == '') {
            alert("Please supply a name or some keywords for this creative.");
            $("#hse_cnew_gname").focus();
            return false;
        }
        if (url == '') {
            alert("Please supply the URL for this ad first, or choose one of your existing creatives");
            $("#hse_cnew_url").focus();
            return false;
        }
        if (!url.match(/^[a-zA-Z]+:\/\//)) {
            alert("Please enter the full URL e.g. http://www.mysite.com");
            $("#hse_cnew_url").focus();
            return false;   
        }
    }
    alert("To associate this Creative with an advertiser, please go to the Manage Creatives view");
    currObj.infoWindow.url = url;
    //There is no proper image_url per-se (the image not uploaded to system)
    //If in preview mode, we just use the stream of bytes read in (preview obj)
    currObj.infoWindow.image_url = $("#hse_previewImage").attr('src');
    currObj.infoWindow.hse_gname = gname;
    currObj.hse_gid = -1;
    return true;
}

function editHSObjValidate(previewonly) {
    if (!previewonly) {
        var name = $("#hse_name").val().trim();
        if (name == '') {
            showError(hsePrompt['needName']); 
            return false;
        }
        currObj.hse_name = name;
    }

    //Case of no known creative:
    if (currObj.hse_gid == -1 && (currObj.infoWindow.ad_type != 2 || !previewonly)) { 
        //for banner or vert rect, even in 
        //preview mode we need the graphic.
        //for hotspot, for preview mode, no creative specified still ok.
        //but to save the hotspot, must have creative
        if (isNewCreative()) {
           if (!editHSObjValidateNewCreative())
            return false;
        }
        else {
            showError(hsePrompt['needCreative']); 
            return false;
        }
    }
    
    var starttimeMS = 1000*HHMMSS_OR_MMSS_to_secs($("#hse_starttime").val());
    var durationMS = 0;
    if ($( "#hse_stopordurationCombo" ).val() == "duration")
        durationMS = 1000*parseInt($("#hse_stoptimeorduration").val());
    else //chosen to specify stop time.
        durationMS = 1000*HHMMSS_OR_MMSS_to_secs($("#hse_stoptimeorduration").val()) - starttimeMS;

    //We already trap all the editing of start, stop or duration fields.
    //There should be NO invalid field that the user can SEE visually.
    //However, so far we did not eliminate the case of start time > stop time
    //(we have allowed this on purpose. Coz sometimes he was planning to fix up one
    //after another)
    //This is the only check we need now.
    if (durationMS <= 0)  {
        showError(hsePrompt['needRange']); 
        return false;
    }

    currObjNewFrame = starttimeMS;


    currObj.duration = durationMS;

    currObj.id = currObj.db_id; //Don't know why have to duplicate leh? Historical reasons...
    //NO need to capture the rect pos and size as it is already
    //done in the handler of the resizing and repositioning
    return true;
}

function editHSObjSave(isnewobj) {
    if (!editHSObjValidate(false))  //previewonly = false
        return;

    var fcn = function(data) {
        if (data.status != "success") {
            showDBError(hseError['write'], data.info);
            editHSObjCleanup(true);
            return;
        }
        //console.log(data);
        if (window.hssetid == -1 && data.hasOwnProperty("hssetid") && data.hssetid != -1) {
            window.hssetid = data.hssetid;
            adsMgr.setHSSID(data.hssetid);
        }
        if (isnewobj) {
            currObj.db_id = data.id;
            currObj.id = data.id;
            adsMgr.addAd(currObj, currObjNewFrame);
        }
        else {
            adsMgr.updateAd(currObj, currObjFrame, currObjNewFrame);
        }
        addGraphicToListMaybe(currObj);
        editHSObjCleanup(true);
        showHotspotsMaybe(); //update the iframe.
    }
    if (currObj.hse_gid != -1)
        addOrUpdateHotspotAsync(isnewobj, currObj, currObjNewFrame, fcn);
    else
        addOrUpdateGraphicNHotspotAsync(isnewobj, currObj, currObjNewFrame, fcn);
}

window.closeHSPanel = function() {
    editHSObjClose();
}

window.addNewHSObj = function(adtype) {
    videoMgr.doPause();
    addHSObjInPanelMaybe(adtype);
}

/************************************************************
 *
 * HANDLERS
 *
 ************************************************************/

function enableHSEditor() {
    var grp = $(".hse_class_disablefirst");
    grp.prop("disabled", false);
    //grp.removeClass("disabled-btn");
    //This does not quite work.
}

function isValidDuration(tS) {
    return (tS > 0);
}

//TODO
function isTimeWithinVideo(tMS) {
    return (tMS>=0);
}

//TODO: now don't support this quarter min nonsense.
function showtime(ctrl1, ctrl2) {
    var frame = Math.round(videoMgr.getPlayhead());
    videoMgr.doPause();
    $(this).val(seconds_to_HHMMSS(frame)); //format properly for him

    /* do not support this in version 0
    var mainpart = Math.floor(frame / 1000);
    var rmod1K = frame - mainpart*1000;
    var rounded = 0;
    if (rmod1K > 0)
        rounded = 250 * Math.floor(rmod1K/250);
    console.log("o1 o2, x y z" + frame + " " + mainpart + " " + rmod1K + " " +  rounded);
    ctrl1.val(mainpart);
    ctrl2.val(rounded);
    */
}

function convertImageToString(blob, failHandler) {
    var reader = new FileReader();
    reader.onload = function(e) {
        if (e.target.result.substr(0, 10) == "data:image") {
            var image = new Image();
            image.src = reader.result;
            image.onload = function() {
                $("#hse_img_panel").css('display', 'block');
                $("#hse_previewImage").css('visibility', 'visible');
                $("#hse_previewImage").attr('src', e.target.result).slideDown();
            };
        }            
        else {
            $("#hse_previewImage").attr('src', ''); 
            alert("Please select a valid image file or provide a valid image URL.");
            failHandler();
        }

    };
    reader.readAsDataURL(blob);
}

$(document).ready(function() {
            
    if (window.adsMgr) {
        adsMgr = window.adsMgr;
        adsMgr.registerClickAdCB(hse_clickAdCB);
        adsMgr.setAdsListChangeUpdateCtrl($("#hse_existingAdsDpn"), "editHSObjInPanelMaybe");
    }
    else
        showFatalError(hseError['fatal']);
    if (window.videoMgr) {
        videoMgr = window.videoMgr;
        videoMgr.registerVideoAreaInfoCB(hse_areaInfoReadyCB);
        //Require more testing before releasing this.
        videoMgr.registerPlayOrPauseEventCB(function(isPlay) {
        if (isPlay)
            $('#hs_new').hide();
        else
            $('#hs_new').show();
        });
    }
    else
        showFatalError(hseError['fatal']);
    $("#hse_bannersubtypeDiv").hide();


    $('input[name=creativeRadioGrp]').change(function() {       
        //either 0 or 1
        //Must set the preview object to blank again.

        $("#hse_previewImage").attr('src', '').slideUp(); //Do not show yet.
        var sel = $('input[name=creativeRadioGrp]:checked').val(); 

        if (sel == 0) { 
            $("#hse_cnew_panel").hide();
            $("#hse_csearch_panel").show();
            populateCreativesTab(true, -1, '','','');
        }
        else {
            $("#hse_csearch_panel").hide();
            $("#hse_cnew_panel").show();
            populateCreativesTab(true, -1, '','','');
        }
    });

    $("#hse_imageuploadBtn").change(function () {
          var ctrl = this;
        if (this.files && this.files[0]) {
            convertImageToString(this.files[0], function() {
                 $(ctrl).parent().find("input").val("");
                });
            editHSObjSetDirty();
        }
    });


    $("#hse_searchstr").keyup(function() {
        if ($(this).val().length > 0)
            getAdGraphicsByKeywordsAsync($(this).val(), setDBContentsAdGraphicsPanel);
        else {
            $("#hse_searchstr").click();
            setBasicContentsAdGraphicsPanel();
        }
    });

    $( "#hse_bannersubtypeCombo" ).change( function() {
        handleCreativeChanged('hse_bannersubtypeCombo change');
    });
    $("#hse_starttime").change(function () {
        var tS = HHMMSS_OR_MMSS_to_secs($(this).val());
        if (!isTimeWithinVideo(tS))
            showError(hseError['needStartTime']);
        $(this).val(seconds_to_HHMMSS(tS)); //format properly for him
        //editHSObjSetDirty();
    });

    $("#hse_stoptimeorduration").change(function () {
        var tS;
        //from stop time to duration view
        if ($( "#hse_stopordurationCombo" ).val() == "duration") {
            tS = parseInt($(this).val());
            //get everything from all 4 fields.
            //from duration to stop time
            //if diff is -ve force him to fix it back first.
            if (!isValidDuration(tS))
                showError(hseError['needDuration']);
            else
                $(this).val(tS); //format properly for him
            }
        else {//from duration to stop time view.
             tS = HHMMSS_OR_MMSS_to_secs($(this).val());
             if (!isTimeWithinVideo(tS))  {
                tS = 0;
                showError(hseError['needStopTime']);
             }
             $(this).val(seconds_to_HHMMSS(tS)); //format properly for him
        }
    });
    $("#hse_stopordurationCombo").change(function () {
        var starttimeS = HHMMSS_OR_MMSS_to_secs($("#hse_starttime").val());
        if ($( "#hse_stopordurationCombo" ).val() == "duration") { //means the value inside WAS stop time
            var stoptimeS = HHMMSS_OR_MMSS_to_secs($("#hse_stoptimeorduration").val());
            var durationS = stoptimeS - starttimeS;
            $("#hse_stoptimeorduration").val(durationS);
        }
        else { //WAS duration
            var durationS = parseInt($("#hse_stoptimeorduration").val());
            var stoptimeS = starttimeS + durationS;
            $("#hse_stoptimeorduration").val(seconds_to_HHMMSS(stoptimeS));
        }
    });

    $("#hse_setstarttimeBtn").click(function () {
        var frame = Math.round(videoMgr.getPlayhead()/1000);
        videoMgr.doPause();
        $("#hse_starttime").val(seconds_to_HHMMSS(frame)); //format properly for him
    });
    $("#hse_setstoptimeBtn").click(function () {
        var stoptimeS = Math.round(videoMgr.getPlayhead()/1000);
        videoMgr.doPause();

        //var durationS = stoptimeS - starttimeS;
        //if (durationS <= 0) {
        //    alert("Stop time is before start time! Remember to change your start time later.");
        //    return; //do nothing.
        //}
        $( "#hse_stopordurationCombo" ).val("stoptime");
        $("#hse_stoptimeorduration").val(seconds_to_HHMMSS(stoptimeS));
        ////editHSObjSetDirty();
    });
    $("#hse_previewBtn").click(function () { //add or update
        if (!editHSObjValidate(true)) //previewonly = true
           return;
        /* $('html, body').animate({
            scrollTop: ($('#hse_video_frame').offset().top)
        },500); */

        if (currObj.infoWindow.image_url == "") {
            //This is when there is no file URL yet. (just chosen file, not saved)
            //This is either empty or the stream of bytes read it.
            var x = $("#hse_previewImage").attr('src');
            currObj.infoWindow.image_url = x;
        }

        videoMgr.doPreview(currObj, currObjNewFrame);
    });
    
    $("#hse_commitBtn").click(function () { //add or update
        
        editHSObjSave(isNewObj);
    });
    $("#hse_closeBox").click(function () {
        editHSObjClose();
    });
    $("#hse_deleteBtn").click(function () {
        editHSObjDelete();
    });
    
    $(".dirtyIfChange").change(function () {
        editHSObjSetDirty();
    });

    $(".dirtyIfClick").click(function () {
        editHSObjSetDirty();
    });
    /////tt.tooltip({html : true, show: {effect:"none", delay:0}});
    $('[data-toggle="tooltip"]').tooltip({delay: { "show": 500  }});

    $("#hse_previewImage").on('load', function() { 
        handleCreativeChanged('hse_previewImage on.load');
    });
    /******    
    $('#creative-tab-li').click(function (e) {
       $('#creative-tab-a').tab('show');
    });
    $('#timing-tab-li').click(function (e) {
        $('#timing-tab-a').tab('show');
    });
    $('#timing-tab-li').trigger('click');
    ******/
    
    $("#hse_panel").hide();
    $("#hse_buttons_panel").css('visibility', 'hidden'); 
    $("#hse_blank_panel").show();
    
    videoMgr.registerVideoReadyCB(
        function() {
            queryHotspotsJSONAsync();
            //$('.hse_class_disablefirst').prop("disabled", false);
        });

    }//anon function
    ); //of doc ready

var defaultNewInfoWindow = {
      "content": "",
      "url": "",
      "image_url": "",
      "ad_type": 2,
      "ad_subtype": 0,
      "title": "Ad Title"
};

var defaultNewHotspot = {
   "db_id": -1,
   "display": "always",
   "hse_gid" : -1,
   "hse_name": '',
   "infoWindow": {
      "advertiser": {
           "name":""
      },
      "hse_gname" : "",
      "content": "",
      "url": "",
      "image_url": "",
      "ad_type": 2,
      "ad_subtype": 0,
      "title": "Ad Title"
   },
    "id": -1,
   "duration": 3000,
   "rect": "10,10,40,40"
};

var defaultNewBanner = {
   "db_id": -1,
   "display": "always",
   "hse_gid" : -1,
   "hse_name" : '',
   "infoWindow": {
      "advertiser": {
           "name":""
      },
      "hse_gname" : "",
      "content": "",
      "url": "",
      "image_url": "",
      "ad_type": 1,
      "ad_subtype": 1,
      "title": "Ad Title"
   },
    "id": -1,
   "duration": 3000,
   "rect": "0,0,60,30"
};

var defaultNewVertRect = {
   "db_id": -1,
   "display": "always",
   "hse_gid" : -1,
   "hse_name" : '',
   "infoWindow": {
      "advertiser": {
           "name":""
      },
      "hse_gname" : "",
      "content": "",
      "url": "",
      "image_url": "",
      "ad_type": 3,
      "ad_subtype": 0,
      "title": "Ad Title"
   },
    "id": -1,
   "duration": 3000,
   "rect": "34,66,11,19"
};



})(jQuery);



