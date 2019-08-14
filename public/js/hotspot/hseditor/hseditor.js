(function($){
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
    var paramObj = {
        url: url,
        data: mydata,
        type:'post',
        error:function(response){
            alert("ajax call err.");
            errhandler(response);
        },
        success:function(response){
            //alert("ajax call succeeded.")
            successhandler(response);
         }
    }; //TODO bad bad bad
    if (url == 'uploadimage') {
        paramObj.processData = false;
        paramObj.contentType = false;
    }

    $.ajax(paramObj).done(function( response ) {
            $("body").css("cursor", "default");
         }
    );
}


function addOrUpdateGraphicNHotspotAsync(isNewObj, obj, starttime, fcn) {
    if (!obj) return;
    //The form needs to have the other stuff too.
    //Target_URL and name
    var data = new FormData($("#imageupload_form")[0]);
    genericAjaxCall('uploadimage', data,
        function(response) {
            if (response.status == "success") {
                obj.hse_gid = response.id;
                obj.infoWindow.image_url = response.imagepath;
                addGraphicToListMaybe(obj);
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

function deleteHotspotAsync(obj, fcn) {
    if (!obj) return;
    var data = {
            "id" : obj.db_id /* HS object ID is unique in whole table */
    };
    genericAjaxCall('int_deleteHotspot', data,
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
        "gid": obj.hse_gid
    };
    if (isNewObj)
        data.hssetid = adsMgr.getHSSID();
    else
        data.id = obj.db_id;

    if (data.hssetid == -1) {
        //We will need to create the hotspot set.
        data.partnerId = currentVideo.partnerId;
        data.videourl = currentVideo.url;
        data.entryId = currentVideo.entryId;
        data.hsname = currentVideo.name;
        data.dim = dimensions;
    }

    genericAjaxCall((isNewObj ? 'int_addHotspot' : 'int_updateHotspot'), data,
        function(response) { fcn(response); },
        function(response) {});
}


function getAdGraphicsByKeywordsAsync(keywords, fcn) {
    var data = {
        "keywords" : keywords,
        "ownerid" : adsMgr.getOwnerID()
    };

    genericAjaxCall('int_getAdGraphicsByKeywords', data,
        function(response) { setDBContentsAdGraphicsPanel(response.data); },
        function(response) {});
}

function queryHotspotsJSONAsync() {
    var data = {
        "hssetid": adsMgr.getHSSID()
    };
    genericAjaxCall('int_retrieveHotspotsJSON', data,
        function(response) {
            if (response.status != "success" && adsMgr.getHSSID() != -1) {
                alert("Error retrieving hotspots for video (" + response.data + ")");
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
        return "Hotspot";
    else if (ad_type == 1)
        return "Banner";
    else
        return "Vertical Rectangle";
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
 /* we need to get the URL, preview image, etc from the right
    tab, depending on whether user selected search-creative
    or make new creative tab */
function isNewCreative() {
    return (getCreativesTabPrefix() == 'makenew_');

}

 function getCreativesTabPrefix() {
    if ($('#searchRadio').prop('checked') == true)
        return "search_";
    else
        return "makenew_";
  }

 window.populateCreativesTab = function(initialPopulation, gid, name, image_url, target_url) {
     if (gid == -1) {
        name = '';
        image_url = '';
        target_url = '';
    }
    var prefix = "#search_";
    $(prefix + "gid").val(gid);
    $(prefix + "contenturl").val(target_url);
    $(prefix + "contentname").val(name);
    if (image_url == '')
        $(prefix + "previewImage").attr('src', '').slideUp();
    else {
        //Not sure if it is bug or design.
        //You cannot just use the prop('title', ...) to change the tooltip
        //string (some report on this in the web). The bootstrap will
        //show the "previous" one taken from data-title-original
        //So no choice but to get rid of the old one and start new one
        var tt = $('#search_previewImage-a');
        tt.tooltip('dispose');
        //tt.prop('title', 'super new tooltip');
        tt.prop('title', "The current creative used: <br>" + target_url + "<br>(Keywords:" +
            name + ")");
        //alert(tt.length);
        tt.tooltip({html : true, delay:{ "show": 500  }});
        $(prefix + "previewImage").attr('src', image_url).slideDown();//WOOWOO
    }
    prefix = "#makenew_";
    $(prefix + "gid").val('-1');
    $(prefix + "contenturl").val('');
    $(prefix + "contentname").val('');
    $(prefix + "previewImage").attr('src', ''); 
    ///$('#search-tab-li').trigger('click'); //when all ready first?
    if (!initialPopulation) {

        $("#hse_searchstr").click();
        editHSObjSetDirty();
    }
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
    htmlStr += "<span>Creatives used in this video:</span><br>";

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

function convertImageToString(blob, failHandler) {
    var reader = new FileReader();
    var prefix = "#" + getCreativesTabPrefix(); /* should be makenew_ */
 
    reader.onload = function(e) {
        if (e.target.result.substr(0, 10) == "data:image") {
            var image = new Image();
            image.src = reader.result;
            image.onload = function() {
                $(prefix + "previewImage").attr('src', e.target.result); 
                $(prefix + "previewImage").slideDown();
                ////alert("width and height = " + image.width + " " + image.height);
            };
        }            
        else {
            $(prefix + "previewImage").attr('src', ''); //WOOWOO
            alert("Please select a valid image file or provide a valid image URL.");
            failHandler();
        }

    };
    reader.readAsDataURL(blob);
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

function editHSObjValidateAdGraphicFields(previewonly) {
    var prefix = "#" + getCreativesTabPrefix();
    var gid = parseInt($(prefix + "gid").val(), 10);
    var url = $(prefix + "contenturl").val().trim(); //should check, ideally.
    var gname = $(prefix + "contentname").val().trim(); //should check, ideally.

    if (!previewonly) {
        if ($(prefix + "previewImage").attr('src') == '') {
            alert("Please upload an image for this ad first, or choose one of your existing creatives.");
            return false;
        }
        if (gname == '') {
            alert("Please supply a name or some keywords for this creative.");
            $(prefix + "contentname").focus();
            return false;
        }
        if (url == '') {
            alert("Please supply the URL for this ad first, or choose one of your existing creatives");
            $(prefix + "contenturl").focus();
            return false;
        }
        if (!url.match(/^[a-zA-Z]+:\/\//)) {
            alert("Please enter the full URL e.g. http://www.mysite.com");
            $(prefix + "contenturl").focus();
            return false;   
        }
    }
    
    if (isNewCreative())
        alert("To associate this Creative with an advertiser, please go to the Manage Creatives view");
    var image_url = $(prefix + "previewImage").attr("src");
    currObj.infoWindow.url = url;
    currObj.infoWindow.image_url = image_url;
    currObj.infoWindow.hse_gname = gname;
    currObj.hse_gid = gid;

    return true;
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
        currObj.rect = boxRectInfo.join();
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
        //console.log("furthest bottom " + furthestBottom);
        //not just that
        if (top + h > furthestBottom) {
            var diff = top + h - furthestBottom;
            top = Math.max(0, top - diff);
            //console.log("repaired top is " + top);
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
    //console.log("DEBUG SLIDER HEIGHT " + videoMgr.getSliderHeight());

    var height_format_string = 'calc(100% -  '  +  videoMgr.getSliderHeight() + 'px)';
    //console.log("format string " + height_format_string);
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
    //console.log('   presetPositioningRect ar=' + aspect_ratio);
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
        //console.log('handleCreativeChanged (currObj valid, ' + cxt + ", ad_type=" + currObj.infoWindow.ad_type);
        if (currObj.infoWindow.ad_type != 1)
            return;
    }
    else {
        //console.log('handleCreativeChanged (currObj NULL, ' + cxt);
        return;
    }
    currObj.infoWindow.ad_subtype = parseInt($("#hse_bannersubtypeCombo" ).val());
    //console.log('   banner subtype=' + currObj.infoWindow.ad_subtype);

    var img = $( "#" + getCreativesTabPrefix() + "previewImage"); 
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
        rectMsg = "Position & Size Hotspot";
        bannerImageSegment = '';
    }
    else {
        bannerImageSegment = '<img src="' + imgsrc + '">';
        if (imgsrc == '') {
            cannot_use = true;
            rectMsg = 'Select creative first!';
        }
        else 
            rectMsg = ((subtype == subtype_banner_left_to_right || subtype == subtype_banner_right_to_left) ?
                "Resize, move up/down" : "Resize, move sideways");
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
    var editPanel = $(".add-panel");
    if (editPanel.is(":hidden")) {
        editPanel.slideDown();
        return true;
    }
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

    isCurrObjDirty = false;
    $('#hs_new').remove();
    if (isToHidePanel) {
        /////toggleCreativePanel(false);
        $(".add-panel").slideUp();
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
        if (confirm('You have unsaved changes. Are you sure you want to open a different ad?')) {
            editHSObjCleanup(true); //no new obj to show. just roll back the pane.
            return true;
        }
    }
    else if (confirm('You have unsaved changes. Are you sure you want to open a different ad?')) {
        editHSObjCleanup(false); //keep the panel there.
        return true;
    }
    return false;
}


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

//Called from user selecting item from the drop down list
//Can only call with numbers, not obj.
//So we need to do an extra look up here
//we will see 

window.editHSObjInPanelMaybe = function(db_id, frame) {
    var obj = adsMgr.getAd(db_id, frame);
    if (!wasHSOBjPanelHidden() && !isCanSwitchHSEditObj(obj))
        return;
    var obj = cloneIt(obj);

    videoMgr.doSeek( frame );
    //Somehow calling kaltura to do seek and pause in close succession does not work!

    /* function wait(ms){
       var start = new Date().getTime();
        var end = start;
        while(end < start + ms) {
        end = new Date().getTime();
        }
    }*/
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
    //This is a big hacky
    currObj = null;
    $('#search-tab-li').trigger('click'); 

    isNewObj = isnewobj;
    if (!isnewobj) {
        $("#hse_commitBtn").val('Update');
        $("#hse_deleteBtn").show();
    }
    else {
        $("#hse_deleteBtn").hide()
        $("#hse_commitBtn").val('Add');
    }
    currObjFrame = frame;
    currObjNewFrame = frame;

    $("#hse_imageuploadBtn").val('');//clear the selection from
    //earlier use of File dialog this panel. May not work for all
    //browsers. On my Mac Chrome/Safari OK.

    
    $("#hse_searchstr").val('');
    setBasicContentsAdGraphicsPanel();
    /* 1st param is false coz no need to for the adgraphics dropdown to
       slide back up. This is just a quiet populating of the fields */
    
    $("#hse_name").val(obj.hse_name);

    $("#hse_adtype").html("<h3>" + (isnewobj? "New " : "Edit this ") + adTypeToStr(obj.infoWindow.ad_type) + "</h3>");
    //TODO: to have or not to have $("#hse_adtitle").val(obj.infoWindow.title);
    var starttimeS = Math.round(frame/1000);
    if (starttimeS == 0 && isnewobj) starttimeS = 1;
    $("#hse_starttime").val(seconds_to_HHMMSS(starttimeS));
    var durationS = Math.round(obj.duration/1000);
    if ($( "#hse_stopordurationCombo" ).val() == "duration")
        $("#hse_stoptimeorduration").val(durationS); //can it format? TODO
    else
        $("#hse_stoptimeorduration").val(seconds_to_HHMMSS(durationS + starttimeS));
    if (obj.infoWindow.ad_type == 1) 
        $( "#hse_bannersubtypeCombo" ).show().val(obj.infoWindow.ad_subtype);
    else
        $( "#hse_bannersubtypeCombo" ).hide();
    
    $('#search-tab-li').trigger('click'); 
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

function editHSObjDelete() {
    if (!confirm('Are you sure to remove this hotspot from your list?'))
        return;
    var fcn = function(data) {
        if (data.status != 'success') {
            alert("Error in deleting entry from database (" + data.info + ")");
            editHSObjCleanup(true);
            return;
        }
        adsMgr.deleteAd(currObj, currObjFrame);
        editHSObjCleanup(true);
    }
    deleteHotspotAsync(currObj, fcn);
    return;
}

function editHSObjValidate(previewonly) {
    if (!previewonly) {
        var name = $("#hse_name").val().trim();
        if (name == '') {
            alert("Please supply a name to identify this hotspot internally.");
            return false;
        }
        currObj.hse_name = name;
    }

    //if (!editHSObjIsDirty()) return true; MIOW can don't do this step?
    if (!editHSObjValidateAdGraphicFields(previewonly)) return false;
    //TODO Check junk situation.
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
        alert("Please specify a valid time range.");
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
    //This is a pain in the neck HSE has to save the video dim info into
    //hotspot set table (to put into the JSON to WP side.)
    //But such queries are not always possible depending on the state of
    //the Kaltura player side. so we might have saved the dim info
    //and we use this chance to perform the save.
    //Wow there are 3 sync calls here. Jia Laat.
    //if (needSaveVideoDimToDB)
      //  saveVideoDimInfo();
    if (!editHSObjValidate(false))  //previewonly = false
        return;

    var fcn = function(data) {
        if (data.status != "success") {
            alert("Error saving to database (" + data.info + " )");
            editHSObjCleanup(true);
            return;
        }

        if (data.hasOwnProperty("hssetid") && data.hssetid != -1) {
            adsMgr.setHSSID(data.hssetid);
        }

        if (isnewobj) {
            //alert("Just added: data.id " + data.id);
            currObj.db_id = data.id;
            currObj.id = data.id;
            adsMgr.addAd(currObj, currObjNewFrame);
        }
        else {
            //alert("update db_id" + currObj.db_id)
            adsMgr.updateAd(currObj, currObjFrame, currObjNewFrame);
        }
        addGraphicToListMaybe(currObj);
        editHSObjCleanup(true);
    }
    if (currObj.hse_gid != -1)
        addOrUpdateHotspotAsync(isnewobj, currObj, currObjNewFrame, fcn);
    else
        addOrUpdateGraphicNHotspotAsync(isnewobj, currObj, currObjNewFrame, fcn);
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

    $(document).ready(function() {
        if (window.adsMgr) {
            adsMgr = window.adsMgr;
            adsMgr.registerClickAdCB(hse_clickAdCB);
            adsMgr.setAdsListChangeUpdateCtrl($("#hse_existingAdsDpn"), "editHSObjInPanelMaybe");
        }
        else
            alert("adsMgr not initialized! Please reload page and try again.");
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
            alert("videoMgr not initialized! Please reload page and try again");

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

    $("#search_previewImage").on('load', function() { 
        handleCreativeChanged('search_previewImage onload');
        return;
    });
    $("#makenew_previewImage").on('load', function() { 
       handleCreativeChanged('makenew_previewImage onload');
       return;
    });
    $( "#hse_bannersubtypeCombo" ).change( function() {
        handleCreativeChanged('hse_bannersubtypeCombo change');
    });

    $("#hse_starttime").change(function () {
        var tS = HHMMSS_OR_MMSS_to_secs($(this).val());
        if (!isTimeWithinVideo(tS))
            alert("Please enter a valid starting time.");
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
                alert("Please enter a valid duration (in seconds).");
            else
                $(this).val(tS); //format properly for him
            }
        else {//from duration to stop time view.
             tS = HHMMSS_OR_MMSS_to_secs($(this).val());
             if (!isTimeWithinVideo(tS))  {
                tS = 0;
                alert("Please enter a valid starting time.");
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
        $('html, body').animate({
            scrollTop: ($('#hse_video_frame').offset().top)
        },500);

        if (currObj.infoWindow.image_url == "") {
            //This is when there is no file URL yet. (just chosen file, not saved)
            //This is either empty or the stream of bytes read it.
            var x = $("#makenew_previewImage").attr('src');
            currObj.infoWindow.image_url = x;
        }

        videoMgr.doPreview(currObj, currObjNewFrame);
    });
    $("#hse_commitBtn").click(function () { //add or update
        editHSObjSave(isNewObj);
    });
    $("#hse_closeBox").click(function () {

         var form_clean = $("#addPanel :input").serialize();
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

    var searchTabSelected = true;
        /////tt.tooltip({html : true, show: {effect:"none", delay:0}});

    $('[data-toggle="tooltip"]').tooltip({delay: { "show": 500  }});
        /* Coz even if the user clicks on exactly on the radio button
           but on the tab itself, we also want to regard that as selection
           AND select the radio button */
    $('#search-tab-li').click(function (e) {
        $('#search-tab-a').tab('show');
        $("#searchRadio").addClass('checked');
        $("#searchRadio").prop('checked', true);
        $("#makenewRadio").prop('checked', false);
        $("#makenewRadio").removeClass('checked');
        if (!searchTabSelected) {
            handleCreativeChanged("search-tab-li click");
        }
        searchTabSelected = true;
    
            //This is necessary else if we come here because the user actually
            //clicked on the radio button, we have to stop the even later going
            //to the button (else it will toggle again and become deselected)
        e.stopPropagation();
    });
    $('#makenew-tab-li').click(function (e) {
        $('#makenew-tab-a').tab('show');
        $("#makenewRadio").addClass('checked');
        $("#makenewRadio").prop('checked', true);
        $("#searchRadio").prop('checked', false);
        $("#searchRadio").removeClass('checked');
        if (searchTabSelected) {
            handleCreativeChanged("makenew-tab-li click");
        }
        searchTabSelected = false;
        e.stopPropagation();
    });

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



