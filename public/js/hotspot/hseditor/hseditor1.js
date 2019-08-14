/************************************************************
 *
 * UTILITIES
 *
 ************************************************************/

/*******************************************************
 *  Talking to the Database via AJAX
 *******************************************************/
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

function genericAjaxCall(url, mydata, successhandler, errhandler) {
    $("body").css("cursor", "progress");
    //mydata["ajax"] = 1;
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
    if (url == '/hotspot/creative/store') {
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
    genericAjaxCall('/hotspot/creative/store', data,
        function(response) {
            if (response.status == "success") {
                obj.hse_gid = response.id;
                obj.infoWindow.image_url = response.image_url;
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
    genericAjaxCall('/hotspot/hotspot/destroy', data,
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

    genericAjaxCall((isNewObj ? '/hotspot/hotspot/store' : '/hotspot/hotspot/update'), data,
        function(response) { fcn(response); },
        function(response) {});
}


function getAdGraphicsByKeywordsAsync(keywords, fcn) {
    var data = {
        "keywords" : keywords
    };

    genericAjaxCall('/hotspot/creative/search', data,
        function(response) { setDBContentsAdGraphicsPanel(response.data); },
        function(response) {});
}

function queryHotspotsJSONAsync() {
    var data = {
        "hssetid": adsMgr.getHSSID()
    };
    genericAjaxCall('/hotspot/jsonForHSE', data,
        function(response) {
            if (response.status != "success" && adsMgr.getHSSID() != -1) {
                alert("Error retrieving hotspots for video (" + response.data + ")");
                return;
            }
            //////alert("dim from JSON: " + response.data['dimensions']);
            /*
            if (response.data['dimensions'] == "0x0") {
                needSaveVideoDimToDB = true;
            }
            else {
                needSaveVideoDimToDB = false;
            }
            */
            adsMgr.setData((response));
            adsMgr.doForAllHSObjs(addGraphicToListMaybe);
            setBasicContentsAdGraphicsPanel();
            enableHSEditor();
            //if (needSaveVideoDimToDB)
              //  saveVideoDimInfo();
        },
        function(response) {});
}

/*******************************************************
 *  General stuff
 *******************************************************/
var wpb_video_wrapper = null;
function get_wpb_video_wrapper() {
   if (wpb_video_wrapper)
       return wpb_video_wrapper;
   wpb_video_wrapper = $("#wpb_video_wrapper");
   return wpb_video_wrapper;
}

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

//TODO: CHECKING
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

 function populateCreativesTab(initialPopulation, gid, name, image_url, target_url) {
    $('#search-tab-li').trigger('click');
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
    $(prefix + "previewImage").attr('src', ''); //WOOWOO
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
    receiver_str = null;
    var prefix = "#" + getCreativesTabPrefix(); /* should be makenew_ */
    reader.onload = function (e) {
        if (e.target.result.substr(0, 10) == "data:image") {
            $(prefix + "previewImage").attr('src', e.target.result); //WOOWOO
            $(prefix + "previewImage").slideDown();
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
function editHSObjCaptureRectFrame(rect) {
    if (!rect) {
        return;
    }
    currObj.rect = rect.top + "," + rect.left + "," + rect.w + "," + rect.h;

    //console.log("HSE: captured(top left width height=" + currObj.rect);
    ////XYZXYZ$("#hse_rectLabel").text("Left " + left + "%, Top " + top + "%, Width " + w + "%, Height " + h + "%");
    editHSObjSetDirty();
}

function editHSObjCaptureRectFrameAux() {
    var that = $('#hs_new');
    if (that.length == 0)
        return null;
    var wa_h = jqeltWorkingArea.height();
    var wa_w = jqeltWorkingArea.width();

    var top, left;
    var str = that.css("top");
    ////console.log("    csstop=" + str);
    if (str.indexOf("%") !== -1)
        top = Math.round((parseInt(str.replace("%", ""))));
    else
        top = Math.round((parseInt(str.replace("px", ""))*100)/wa_h);

    //The thing sometimes gives % sometimes gives px....
    str = that.css("left");
    ////console.log("    cssleft=" + str);
    if (str.indexOf("%") !== -1)
        left = Math.round((parseInt(str.replace("%", ""))));
    else
        left = Math.round((parseInt(str.replace("px", ""))*100)/wa_w);

    var w, h;
    str = that.get(0).style.width;
    ////console.log("    stylew:x,y=" + str + " " + that.width());
    if (str && str.indexOf("%") !== -1)
        w = Math.round((parseInt(str.replace("%", ""))));
    else { /* This one not so reliable. Use this if no better ways */
        w = Math.round((parseInt(str.replace("px", "")) * 100)/wa_w);
        //dangerous
        //w = Math.round((that.width()*100)/wa_w);
    }

    str = that.get(0).style.height;
    ////console.log("    styleh:x,y=" + str + " " + that.height());
    if (str && str.indexOf("%") !== -1)
        h = Math.round((parseInt(str.replace("%", ""))));
    else { /* This one not so reliable. Use this if no better ways */
        //h = Math.round((that.height()*100)/wa_h);
        h = Math.round((parseInt(str.replace("px", "")) * 100)/wa_h);
    }
    //alert("Capture: top left w h " + top + " " + left + " "  + w + " " + h)
    var rect = { top: top, left: left, w : w, h: h};
    return rect;
}

function adjustDraggingConstrainingElt(constrainingElt, subtype, videoWidth, videoHeight, bannerWidthPercent, bannerHeightPercent) {
    var allowance = 7;
    if (subtype == subtype_banner_left_to_right) {
        constrainingElt.width(bannerWidthPercent*videoWidth*0.01 + allowance);
    }
    else if (subtype == subtype_banner_right_to_left) {
        var tmp = bannerWidthPercent*videoWidth*0.01;
        constrainingElt.width(tmp);
        tmp = videoWidth*(100 - bannerWidthPercent)*0.01 - allowance;    
        //console.log("TMP = " + tmp);
        constrainingElt.css('left', tmp + 'px');
    }
    else if (subtype == subtype_banner_top_down) {
        var tmp = bannerHeightPercent*videoHeight*0.01 + allowance;
        constrainingElt.height(tmp); //move sideways in a horizontal strip.
    }
    else if (subtype == subtype_banner_bottom_up) {
        var tmp = bannerHeightPercent*videoHeight*0.01;
        constrainingElt.height(tmp); //move sideways in a horizontal strip.
        tmp = videoHeight - allowance - videoMgr.getSliderHeight() - videoHeight *bannerHeightPercent/100;
        constrainingElt.css('top', tmp + 'px');
        //console.log("TOP=" + constrainingElt.css('top'));
    }
}

function makeSameAsWorkingArea(to, bottom_unusable_height) {
    to.css('left', jqeltWorkingArea.css('left'));
    to.css('top', jqeltWorkingArea.css('top'));
    ////alert(" " + jqeltWorkingArea.css('left') + " " + jqeltWorkingArea.css('top'))
    to.height(jqeltWorkingArea.height() - bottom_unusable_height);
    to.width(jqeltWorkingArea.width());
    to.show();
}
var defaultBannerAspectRatio = 2.5
var subtype_hotspot = 0;
var subtype_banner_left_to_right = 1;
var subtype_banner_right_to_left = 2;
var subtype_banner_top_down = 3;
var subtype_banner_bottom_up = 4;

//editHSObjSetupRectFrameMaybe can be called from a number of places.
//Depending on the trigger, editHSObjSetupRectFrameMaybe behaves differently
var reason_resize = 1;
var reason_open = 2;
var reason_change_creative = 3;
var reason_change_banner_cfg = 4;

var allowed_resize_directions = [
    'n, e, s, w, ne, se, sw, nw',  /* hotspot rect resize */
    'ne, se', /* left to right banner */
    'nw, sw', /* right to left banner */
    'se, sw', /* top down banner */
    'ne, nw' /* bottom up banner */
];
   
   /*
function placeSuitableDefaultOutline() { 
 else if (subtype == subtype_banner_left_to_right) {
        //HEIGHT and TOP are the anchor values.
        //LEFT (0) and WIDTH are derived
        attr.height = boxRectInfo[3] + '%';
        height_percent = boxRectInfo[3];
        console.log("HSE videoW=x and videoH=y" + " " + videoWidth + " " + videoHeight);
        tmp = boxRectInfo[3]*aspect_ratio*videoHeight/videoWidth;
        attr.top = boxRectInfo[0] + '%';

        attr.left = '0%';

        attr.width =  tmp + '%';
        width_percent = tmp;
        console.log("width_percent=x" + tmp);

        attr.width = Math.round(tmp*videoWidth*0.01) + 'px';//hack
        attr.height = Math.round(boxRectInfo[3]*videoHeight*0.01) + 'px'; //hack
    }
    else if (subtype == subtype_banner_right_to_left) {
        //HEIGHT and TOP are the anchor values.
        //LEFT and WIDTH are derived
        
        tmp = boxRectInfo[3]*aspect_ratio*videoHeight/videoWidth;
        attr.top = boxRectInfo[0] + '%';

        attr.height = boxRectInfo[3] + '%';
        height_percent = boxRectInfo[3];

        attr.width =  tmp + '%';   
        width_percent = tmp;

        tmp = 100 - tmp;
        attr.left = tmp + '%';
    }
    else if (subtype == subtype_banner_top_down) {
        //LEFT and WIDTH are the anchor values
        //HEIGHT is derived. TOP (0)
        tmp = boxRectInfo[2]*videoWidth/(aspect_ratio*videoHeight);
        attr.left = boxRectInfo[1] + '%';

        attr.width = boxRectInfo[2] + '%';
        width_percent = boxRectInfo[2];
        attr.height =  tmp + '%';   
        height_percent = tmp;

        attr.top = '0%';
    }
    else {
        tmp = boxRectInfo[2]*videoWidth/(aspect_ratio*videoHeight);
        attr.left = boxRectInfo[1] + '%';
        attr.width = boxRectInfo[2] + '%';
        width_percent = boxRectInfo[2];
        attr.height =  tmp + '%';   
        height_percent = tmp;
        var slidersPercentage = 100*videoMgr.getSliderHeight()/videoHeight;
        //The problem is with the slider.
        tmp = 100 - tmp - slidersPercentage;
        attr.top = tmp + '%';   

    }
   */

/* the size/position adjusting frame for hotspot and banners */    
function editHSObjSetupRectFrameMaybe(reason) {
    if (reason == reason_resize) msg = "resize video";
    if (reason == reason_open) msg = "populate ad";
    if (reason == reason_change_creative ) msg = "change creative";
    if (reason == reason_change_banner_cfg) msg = "banner type change";
    //console.log("LOGGING " + msg +  (!currObj ? " NoCurrObj": " HasCurrObj"));

    if (!currObj) return; 
    var visible = true;
    var oldhs = $('#hs_new');

    if (currObj.infoWindow.ad_type == 3) //Vert rect == 3
        return;
    if (currObj.infoWindow.ad_type == 2 &&
        (reason != reason_open && reason != reason_resize))
        return;
    if (!oldhs.length) {
        //Nothing to change. The rect is not out there currently.
        if (! reason == reason_open) return;
    }
    else {
        visible = (reason == reason_open ? true : oldhs.is(":visible"));
        oldhs.remove();
    }
    var preserve_aspect_ratio = (currObj.infoWindow.ad_type == 1);
    var subtype = currObj.infoWindow.ad_subtype;
    
    var tmp;
    var aspect_ratio;
    var resize_dir = allowed_resize_directions[subtype];
    var boxRectInfo = currObj["rect"].split(",");
    var twa = jqeltWorkingArea;
    var tba, tbda;
    var bannerImageSegment;
    var rectMsg;
    if (subtype == subtype_hotspot) { //for banners only.
        rectMsg = "Position & Size Hotspot";
        bannerImageSegment = '';
    }
    else {
        tba = $("#banner_area");
        makeSameAsWorkingArea(tba, videoMgr.getSliderHeight()); 
        tbda = $("#banner_dragging_area");
        makeSameAsWorkingArea(tbda, videoMgr.getSliderHeight());
        var imgID = getCreativesTabPrefix() + "previewImage"
        var img = $( "#" + imgID); 
        bannerImageSegment = '<img src="' + img.attr('src') + '">';
        if (img.attr('src') == '') {
            aspect_ratio = defaultBannerAspectRatio;
            rectMsg = 'Select creative before you size/position!';
        }
        else {
            if (subtype == subtype_banner_left_to_right || subtype == subtype_banner_right_to_left)
                rectMsg = "ScaleAdjust up/down";
            else 
                rectMsg = "Adjust sideways";
            var elmnt = document.getElementById(imgID);
            aspect_ratio = (elmnt && elmnt.naturalHeight > 0 ?  elmnt.naturalWidth/elmnt.naturalHeight : defaultBannerAspectRatio);
            //console.log("HSE: ASPECT RATIO =" + aspect_ratio + " " + elmnt.naturalWidth + " " + elmnt.naturalHeight)
        }

    }
    var that = $('<div id="hs_new" class="hotspot hsetooltip hs_hotspot">'
    + bannerImageSegment + '<div class="resize_repos_box_title"><span>' + rectMsg + '</span></div></div>');

    var attr = {
       'position': 'absolute',
       'pointer-events': 'auto'
   };
    var videoWidth = jqeltWorkingArea.width();
    var videoHeight = jqeltWorkingArea.height();
    var width_percent, height_percent;
    if (subtype == subtype_hotspot) { //normal hotspot
        attr.top = boxRectInfo[0] + '%';
        attr.left = boxRectInfo[1] + '%';
        attr.height = boxRectInfo[3] + '%';
        attr.width =  boxRectInfo[2] + '%';
    }
    else if (subtype == subtype_banner_left_to_right) {
        //HEIGHT and TOP are the anchor values.
        //LEFT (0) and WIDTH are derived
        attr.height = boxRectInfo[3] + '%';
        height_percent = boxRectInfo[3];
        //console.log("HSE videoW=x and videoH=y" + " " + videoWidth + " " + videoHeight);
        tmp = boxRectInfo[3]*aspect_ratio*videoHeight/videoWidth;
        attr.top = boxRectInfo[0] + '%';

        attr.left = '0%';

        attr.width =  tmp + '%';
        width_percent = tmp;
        //console.log("width_percent=x" + tmp);

        attr.width = Math.round(tmp*videoWidth*0.01) + 'px';//hack
        attr.height = Math.round(boxRectInfo[3]*videoHeight*0.01) + 'px'; //hack
    }
    else if (subtype == subtype_banner_right_to_left) {
        //HEIGHT and TOP are the anchor values.
        //LEFT and WIDTH are derived
        
        tmp = boxRectInfo[3]*aspect_ratio*videoHeight/videoWidth;
        attr.top = boxRectInfo[0] + '%';

        attr.height = boxRectInfo[3] + '%';
        height_percent = boxRectInfo[3];

        attr.width =  tmp + '%';   
        width_percent = tmp;

        tmp = 100 - tmp;
        attr.left = tmp + '%';
    }
    else if (subtype == subtype_banner_top_down) {
        //LEFT and WIDTH are the anchor values
        //HEIGHT is derived. TOP (0)
        tmp = boxRectInfo[2]*videoWidth/(aspect_ratio*videoHeight);
        attr.left = boxRectInfo[1] + '%';

        attr.width = boxRectInfo[2] + '%';
        width_percent = boxRectInfo[2];
        attr.height =  tmp + '%';   
        height_percent = tmp;

        attr.top = '0%';
    }
    else {
        tmp = boxRectInfo[2]*videoWidth/(aspect_ratio*videoHeight);
        attr.left = boxRectInfo[1] + '%';
        attr.width = boxRectInfo[2] + '%';
        width_percent = boxRectInfo[2];
        attr.height =  tmp + '%';   
        height_percent = tmp;
        var slidersPercentage = 100*videoMgr.getSliderHeight()/videoHeight;
        //The problem is with the slider.
        tmp = 100 - tmp - slidersPercentage;
        attr.top = tmp + '%';   

    }
    that.css(attr);
    //var that_px_halfwidth = boxRectInfo[2]*videoWidth*0.005;
    //var that_px_halfheight = boxRectInfo[3]*videoHeight*0.005;
    //console.log("HSE attr dump left top width height " + attr.left + " " + attr.top + 
      //   " " + attr.width + " " + attr.height);
    that.appendTo(jqeltWorkingArea);
    if (!subtype == subtype_hotspot) {
        adjustDraggingConstrainingElt(tbda, subtype, videoWidth, videoHeight, 
        width_percent, height_percent);
    }
     that.resizable({
            handles: resize_dir,
            aspectRatio: aspect_ratio,
            containment: tba,
            create: function() {
                /* if (subtype == subtype_hotspot) {
                    $('.ui-resizable-n').css({ left : that_px_halfwidth + 'px'});
                    $('.ui-resizable-s').css({ left : that_px_halfwidth + 'px'});
                    $('.ui-resizable-e').css({ top : that_px_halfheight + 'px'});
                    $('.ui-resizable-w').css({ top : that_px_halfheight + 'px'});
                }*/
            },
            start: function (event, ui) {
                get_wpb_video_wrapper().css({'pointer-events': 'none'});
            },
            stop: function (event, ui) {
                get_wpb_video_wrapper().css({'pointer-events': 'auto'});
                var rect = editHSObjCaptureRectFrameAux();
                if (subtype != subtype_hotspot && rect)
                    adjustDraggingConstrainingElt(tbda, subtype, videoWidth, videoHeight, rect.w, rect.h);
                editHSObjCaptureRectFrame(rect);
                
            }
        }).draggable(
            {
                //containment: jqeltWorkingArea,
                containment: (subtype == subtype_hotspot ? jqeltWorkingArea : tbda),
                scroll: false,

                drag: function(){
                },
                stop: function(){
                var rect = editHSObjCaptureRectFrameAux();
                editHSObjCaptureRectFrame(rect);
                }
            });
    if (visible)
        that.show();
    else
        that.hide();
}

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
        toggleCreativePanel(false);
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

var dimensions = null;

function hse_areaInfoReadyCB(dim) {
    var twa = $("#working_area");
    //console.log(dim.left + " " + dim.top + " " + dim.width + " " + dim.height);
    twa.css("left", dim.left);
    twa.css("top", dim.top);
    twa.width(dim.width);
    twa.height(dim.height);
    //console.log("here again " + dim.videoWidth + " " + dim.videoHeight);
    if (dimensions == null) {
        dimensions = dim.videoWidth + "x" + dim.videoHeight;
        //still do not save it yet. need to save to database record
        //Wait till user saves first hotspot
    }
    twa.show();
    //This is about hs_new (the hotspot resizing and repositioning rectangle)
    //If the user does a zoom or other resize, it seems we have to remove and
    //recreate it for the position to be correct!
    editHSObjSetupRectFrameMaybe(reason_resize);
    //////if ($('#hs_new').length > 0 && currObj != null)
        /////editHSObjShowRectFrame(true, currObj.infoWindow.ad_subtype); //<-- remove and recreate the rect
}

//Called from user selecting item from the drop down list
//Can only call with numbers, not obj.
//So we need to do an extra look up here
function editHSObjInPanelMaybe(db_id, frame) {
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
    isNewObj = isnewobj;
    if (!isnewobj) {
        $("#hse_commitBtn").val('Update');
        $("#hse_deleteBtn").show();
    }
    else {
        $("#hse_deleteBtn").hide()
        $("#hse_commitBtn").val('Add');
    }
    currObj = obj;
    currObjFrame = frame;
    currObjNewFrame = frame;

    $("#hse_imageuploadBtn").val('');//clear the selection from
    //earlier use of File dialog this panel. May not work for all
    //browsers. On my Mac Chrome/Safari OK.

    
    $("#hse_searchstr").val('');
    setBasicContentsAdGraphicsPanel();
    /* 1st param is false coz no need to for the adgraphics dropdown to
       slide back up. This is just a quiet populating of the fields */
    populateCreativesTab(true, obj.hse_gid, obj.infoWindow.hse_gname,
            obj.infoWindow.image_url, obj.infoWindow.url);

    $("#hse_name").val(obj.hse_name);

    $("#hse_adtype").html("<h3>" + (isnewobj? "New " : "Edit this ") + adTypeToStr(obj.infoWindow.ad_type) + "</h3>");
    //TODO: to have or not to have $("#hse_adtitle").val(obj.infoWindow.title);
    var starttimeS = Math.round(frame/1000);
    $("#hse_starttime").val(seconds_to_HHMMSS(starttimeS));
    var durationS = Math.round(obj.duration/1000);
    if ($( "#hse_stopordurationCombo" ).val() == "duration")
        $("#hse_stoptimeorduration").val(durationS); //can it format? TODO
    else
        $("#hse_stoptimeorduration").val(seconds_to_HHMMSS(durationS + starttimeS));

    toggleCreativePanel(!isnewobj);
    if (obj.infoWindow.ad_type == 1) 
        $( "#hse_bannersubtypeCombo" ).show().val(obj.infoWindow.ad_subtype);
    else
        $( "#hse_bannersubtypeCombo" ).hide();
    editHSObjSetupRectFrameMaybe(reason_open);
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

function addNewHSObj(adtype) {
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

//TODO
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

//to remove it soon if the change to TABbed view of creatives is to stay
function toggleCreativePanel(showit) {
    return;
    /*
    var panel = $("#creative_panel");
    if (showit == undefined)
        showit = !panel.is(':visible')

    if (showit) {
        $("#showOrHideCreativePanelTxt").text('Hide');
        panel.slideDown();
    }
    else {
        $("#showOrHideCreativePanelTxt").text('Show');
        panel.slideUp();
    }
    */
}

$(document).ready(
    function() {
        if (adsMgr) {
           ///////OBSOLETE db_id_allocator = adsMgr.getDataMaxDBID() + 1; //From the orig data.
           adsMgr.registerClickAdCB(hse_clickAdCB);

           adsMgr.setAdsListChangeUpdateCtrl($("#hse_existingAdsDpn"), "editHSObjInPanelMaybe");
        }
        else
            alert("adsMgr not initialized! Please reload page and try again.");
        if (videoMgr) {
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

        toggleCreativePanel(false);
        //XYZXYZ $('#checkArea').hide();
        //XYZXYZ $('#hse_showrectcheck').prop('checked', true);
        /* XYZXYZ
        $('#hse_showrectcheck').change(function() {
            if ($('#hse_showrectcheck').is(":checked")) {
                $("#hs_new").show();
            }
            else {
                $("#hs_new").hide();
            }
        });*/

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
            setBasicContentsAdGraphicsPanel();
        }
    });

    $(window).resize(function () {
        //TODO
    });
    
    //WOOWOO even if the tab is switched, I also need to call the event!!!
    $("#search_previewImage").on('load', function() { 
        editHSObjSetupRectFrameMaybe(reason_change_creative);
    });
    
    $("#makenew_previewImage").on('load', function() { 
        editHSObjSetupRectFrameMaybe(reason_change_creative);
    });

    $("#hse_starttime").change(function () {
        var tS = HHMMSS_OR_MMSS_to_secs($(this).val());
        if (!isTimeWithinVideo(tS))
            alert("Please enter a valid starting time.");
        $(this).val(seconds_to_HHMMSS(tS)); //format properly for him
        //editHSObjSetDirty();
    });

    $( "#hse_bannersubtypeCombo" ).change( function() {
        if (currObj) 
            currObj.infoWindow.ad_subtype = parseInt($(this).val());
        //alert("subtype=" + currObj.infoWindow.ad_subtype);
        editHSObjSetupRectFrameMaybe(reason_change_banner_cfg); 
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

    /* $("#searchRadio").change( function() {
        var value = this.val();
        alert(value);
        editHSObjSetupRectFrameMaybe(false);
    });
    $("#makenewRadio").change( function() {
        editHSObjSetupRectFrameMaybe(false);
    }); */
    
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
            //WOOWOO: also need to do the banner thing
            $('#search-tab-a').tab('show');
            $("#searchRadio").addClass('checked');
            $("#searchRadio").prop('checked', true);
            $("#makenewRadio").prop('checked', false);
            $("#makenewRadio").removeClass('checked');
            if (!searchTabSelected) 
                editHSObjSetupRectFrameMaybe(reason_change_creative);
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
            if (searchTabSelected) 
                editHSObjSetupRectFrameMaybe(reason_change_creative);
            searchTabSelected = false;
                
            //We don't have to call it yet. No image selected yet.
            //editHSObjSetupRectFrameMaybe(false);
            e.stopPropagation();
        });


    videoMgr.registerVideoReadyCB(
        function() {
            queryHotspotsJSONAsync();
            jqeltWorkingArea = $("#working_area");

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
   "rect": "60,20,60,30"
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



