/*
 * the core part of hotspot (player agnostic) interacts with Kaltura player
 * thru the functionalities here.
 *
 */
(function() {
    //To find own script's path: Must be called as soon as script loaded
    var scripts = document.getElementsByTagName('script');
    var stylespath = scripts[scripts.length - 1].src;
    stylespath = stylespath.split('/');
    stylespath.pop();
    stylespath = stylespath.join('/');
    stylespath = stylespath + '/../styles/';
    if (stylespath.indexOf('ivideosmart') == -1)
        stylespath = 'https://player.ivideosmart.com/ivideotouch/v1/styles/';

    var getOneElementByID = function getOneElementByID(cxt, ID) {
        return cxt.getElementById(ID);
    }
    var getOneElementByClassName = function getOneElementByClassName(cxt, ID) {
        return cxt.getElementsByClassName(ID)[0];
    }
    var insertAfter = function insertAfter(el, referenceNode) {
        referenceNode.parentNode.insertBefore(el, referenceNode.nextSibling);
    }

    var containerId = 'wpb_video_wrapper'; //default value of div id
    //can be changed. Depends on what window.playerReadyCb is called with
    var kdp;
    var kdpiframe;
    var iframe;
    var currentTime; //this is in seconds
    var playerProxy = {};
    var coreFcnsObj = {};
    window.hseInKaltura = true;

    var initFunctionsForCore = function initFunctionsForCore() {
        var setupListFavButton = function() {
            var elm = document.createElement('div');
            elm.className = 'ivs-hotspot-holder';
            elm.style.position = 'absolute';
            elm.style.top = 0;
            elm.style.right = 0;
            elm.style.width = '4%';
            elm.style.height = '7%';
            ////elm.style.width = '45px';
            ////elm.style.height = '45px';
            elm.style.display = 'block';
            var parent = getOneElementByID(kdpiframe.contentWindow.document, //.body,
                'ivs-real-video-area');
            parent.appendChild(elm);
            elm.onclick = function() {
                if (coreFcnsObj['list_fav_button_clicked'])
                    coreFcnsObj['list_fav_button_clicked']();
            }
            var elmimg = document.createElement('img');
            elmimg.src = stylespath + 'star-favourite.png';
            elm.appendChild(elmimg);
        }

        playerProxy['getDimensions'] = function() {
            var videoHolder = getOneElementByID(document, containerId);
            var sliderElt = getOneElementByClassName(iframe, 'controlBarContainer');
            var sliderThickness = 0;
            if (sliderElt && window.getComputedStyle) {
                var computedStyle = window.getComputedStyle(sliderElt, null);
                tmpstr = computedStyle.width;
                tmpstr = tmpstr.slice(0, -2);
                sliderThickness = parseInt(tmpstr);
            }
            

            var videoWidth = parseFloat(kdp.evaluate('{mediaProxy.entry.width}'));
            var videoHeight = parseFloat(kdp.evaluate('{mediaProxy.entry.height}'));

            if (window.getComputedStyle) {
                var computedStyle = window.getComputedStyle(videoHolder, null)
                tmpstr = computedStyle.width;
                tmpstr = tmpstr.slice(0, -2);
                playerWidth = parseInt(tmpstr);
                tmpstr = computedStyle.height;
                tmpstr = tmpstr.slice(0, -2);
                playerHeight = parseInt(tmpstr);
            }

            playerHeight = playerWidth / 1.77;

            var offset = 0;
            var hsHeight, hsWidth;
            var toppx, leftpx;
            var hsHeight = (playerWidth / videoWidth) * videoHeight;
            if (hsHeight <= playerHeight) {
                offset = (playerHeight - hsHeight) / 2;
                hsWidth = playerWidth;
                toppx = offset;
                leftpx = 0;
            } else {
                //This is the situaion where there will be 2 vert strips of blank
                //left and right of the video.
                hsHeight = playerHeight;
                hsWidth = videoWidth * playerHeight / videoHeight;
                offset = (playerWidth - hsWidth) / 2;
                toppx = 0;
                leftpx = offset;
            }
            return {
                slideHeight: sliderThickness,
                videoHeight: videoHeight,
                videoWidth: videoWidth,
                playerHeight: playerHeight,
                playerWidth: playerWidth,
                left: leftpx,
                top: toppx,
                width: hsWidth,
                height: hsHeight
            }
        }
        playerProxy['getCurrentTime'] = function() {
            return currentTime;
            //var currentTime = Math.round(kdp.evaluate("{video.player.currentTime}")) * 1000;
        }
        playerProxy['getDuration'] = function() {
            return (parseInt(kdp.evaluate('{duration}')) * 1000);
        }
        playerProxy['doAddOverlay'] = function(elt) {
            var e = getOneElementByClassName(kdpiframe.contentWindow.document.body, 'videoHolder');
            insertAfter(elt, e); //?
            return;
        }
        playerProxy['getVideoElement'] = function() {
            var f = getOneElementByID(kdpiframe.contentWindow.document.body, '#pid_' + containerId);
            return f;
        }
        playerProxy['doPlay'] = function() {
            kdp.sendNotification("doPlay");
        }
        playerProxy['doPause'] = function() {
            kdp.sendNotification("doPause");
        }
        playerProxy['doSeek'] = function(tsSeconds) {
            kdp.sendNotification("doSeek", tsSeconds);
        }
        playerProxy['doAddListFavButton'] = function() {
            setupListFavButton();
        }
    }

    //playerProxy['getPlayerType'] = function() {
      //  return 'kaltura';
    //}

    var setupForHotspots = function setupForHotspots(adUnits) {     
        bindMiscKHandlers(); //subscribe to the other Kaltura events we need

        kdpiframe = getOneElementByID(document, containerId + '_ifp');

        iframe = getOneElementByID(document, containerId + '_ifp').contentWindow.document;
        var link = document.createElement("link");
        link.rel = "stylesheet";
        link.type = "text/css";
        link.href = stylespath + 'core.css';
        kdpiframe.contentWindow.document.head.appendChild(link);
        //prepares function objects which hotspot render would need to call to
        //talk to the video player (eg. make video pause)
        initFunctionsForCore(); 
        //Call the window.hotspotsCore function which is declared in the renderer js
        //Initialization:
        var fcnsObj = window.hotspotsCore;
        fcnsObj(playerProxy, adUnits, false);  
        //console.log("____________________________completed init ");
        coreFcnsObj = window.hotspotsFcns;
        state.setupForHotspotsDone = true;
    }        

    //This function requires info which will only be available after the 
    //firstPlay and not too early.
    //Thus only run its contents if (1) firstPlay event came (2) there are
    //hotspots (only found out async). We cannot assume the order these 2 events
    //arrive
    var durationHandlerOneCall = function durationHandlerOneCall() {
        if (state.durationHandlerDone || !state.firstPlayEvtCame) {
            return;
        }
        state.durationHandlerDone = true;
        if (!state.setupForHotspotsDone) return;
        if (window.hotspotsFcns) {
            var container = iframe.querySelector("[role='slider']"); 
            if (coreFcnsObj['playerready']) {
                var duration = parseInt(kdp.evaluate('{duration}')) * 1000;
                coreFcnsObj['playerready'](duration, container);
            }
        }
    }

    var firstPlayEventHandler = function firstPlayEventHandler() {
        state.firstPlayEvtCame = true;
        durationHandlerOneCall();
    }
    
    var bindMiscKHandlers = function bindMiscKHandlers() {
        kdp.kBind('ivs_list_fav_button_clicked', function(data) {
            if (coreFcnsObj['list_fav_button_clicked'])
                coreFcnsObj['list_fav_button_clicked'](data);
        });

        kdp.kBind('seeked', function(data) {
            if (coreFcnsObj['seeked'])
                coreFcnsObj['seeked'](data);
        });

        kdp.kBind('doPause', function() {
            if (coreFcnsObj['paused'])
                coreFcnsObj['paused']();
        });
        kdp.kBind('doPlay', function() {
            if (coreFcnsObj['resumed'])
                coreFcnsObj['resumed']();
        });
        kdp.kBind('playerUpdatePlayhead', function(data, id) {
            //Remove this when you remove function playerReadyiVideoTouchCb_demo
            firstPlayEventHandler(); //as good as a first play event.
            //only for this demo!!
            //coz we are simulating the firstPlay event by timer.
            //Maybe unnecessarily late.
            currentTime = data;
            if (coreFcnsObj['timeupdate'])
                (coreFcnsObj['timeupdate'])(data);
        });
    };

    var hotspotsFetchedEventHandler = function hotspotsFetchedEventHandler(adUnits) {
        setupForHotspots(adUnits);
        //console.log("____________________________finished setupForHotspots");
        durationHandlerOneCall();
    }

    var fetchHotspotsAsync = function(videoID) {
        var hotspotsrc = "https://ivx-api.ivideosmart.com/api/v1/entry/" + videoID + "/ivt_units?key=f0f358bd0b4c6fcb70b77e41a8f52e41&token=637e0ec083506af9398a3ed014fb55cb";
        //console.log(hotspotsrc);
        if (true) {
            var xmlhttp;
            if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else { // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4) {
                    if (xmlhttp.status == 200) {
                        var hotspotsjson = JSON.parse(this.responseText);

                        if (hotspotsjson.status == 'success') {
                            //state.hotspotResponded = true;
                            //state.hasHotspots = true;
                            state.adunits = { 
                                results : hotspotsjson.results, 
                                metainfo : hotspotsjson.metainfo ? hotspotsjson.metainfo : 0
                            };
                            //console.log("____________________________hotspots fetched");
                            hotspotsFetchedEventHandler(state.adunits);
                        }
                    }
                }
            };
            xmlhttp.open("GET", hotspotsrc, true);
            xmlhttp.send();
        }
    };

    var state = {
        firstPlayEvtCame : false,
        durationHandlerDone : false,
        setupForHotspotsDone : false
    }

    var initKDPObject = function initKDPObject(containerId0) {
        containerId = containerId0;
        kdp = getOneElementByID(document, containerId);
        if (kdp === undefined || !kdp) {
            //console.log('KDP NOT THERE');
            return false; 
        }
        if (!('kBind' in kdp)) {
            //console.log("kBIND NOT THERE")            ;
            return false;
        }
        //console.log("kBIND YYYYYYYYYY")   
        return true;
    }

    var initKDPObject_demo = function initKDPObject_demo(containerId0) {
        containerId = containerId0;
        kdp = getOneElementByID(document, containerId);
        if (kdp === undefined || !kdp) {
            //console.log('KDP NOT THERE');
            return false; 
        }
        if (!('kBind' in kdp)) {
            //console.log("kBIND NOT THERE")            ;
            return false;
        }
        //console.log("kBIND YYYYYYYYYY")   
        return true;
    }
    
    window.playerReadyiVideoTouchCb = function(containerId0, videoID) {
        initKDPObject(containerId0);
        kdp.kBind('firstPlay', firstPlayEventHandler);
        fetchHotspotsAsync(videoID);
    }

    //This one is before we get a proper way of hook up to Kaltura.
    //This is the "hack" way.
    //Please visit function Remove this when you remove function playerReadyiVideoTouchCb_demo
    //When we remove this function in the future:
    //remember to remove call to firstPlayEventHandler from kdp.kBind('playerUpdatePlayhead'
    window.playerReadyiVideoTouchCb_demo = function(containerId0, videoID) {
        var timesTried = 0;
        (function tryInit() {
        setTimeout(function() {
            if (!initKDPObject_demo(containerId0) && timesTried <= 5) {
                timesTried++;
                tryInit();
            }
            else
                fetchHotspotsAsync(videoID);       
        }, 2000);
        })();
    }

})();



