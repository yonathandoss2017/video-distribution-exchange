/*
 * The first self-running code is the GA stuff.
 */
(function(global,d){
    var debugGA = false; //keep this here during development phase.
    if (debugGA) {
   (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(global,d,'script','https://www.google-analytics.com/analytics_debug.js','ga');
    }
    else {
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(global,d,'script','https://www.google-analytics.com/analytics.js','ga');
    }
    var gLastHotspotID = -1;
    var gDoGA = true;
    var gUA = "UA-77307024-14"; //<--- default endpoint, if the ga_tracking_id in the metainfo is
    //present, then we will use that one (should also be the same value)

    //var gUA = "UA-90565141-9"; //testing ivideotouch analytics
    
    var gTrackerNames = {};
    
    var eActionString = [ 
        "_showed",
        "_clicked",
        "_visited",
        "_liked"
    ];
    var getEActionString = function getEActionString(action) {
        if (action >= 0 && action <= 3)
            return eActionString[action];
        return '';
    }

    var addHotspotDuration = function(customObj, duration) {
            customObj['metric6'] = duration;
    }
    var getCustomObject = function getCustomObject(action) {
        var retObj;
        var field = null;

        switch(action) {
            case 0: //viewing of the hotspot
                field = 'metric1';
                break;
            case 1: //infoCard
                field = 'metric2'; 
                break;
            case 2: //click to visit product site 
                field = 'metric3';
                break;
            case 3: //press LIKE button
                field = 'metric5';
                break;
            case -1: //start playing a hotspot enabled video
                field = 'metric4';
                break;
        }           
        if (field == null) return null; //Dunno what type of action is this.
        retObj = {
            metric1 : 0,
            metric2 : 0,
            metric3 : 0,
            metric4 : 0,
            metric5 : 0,
            metric6 : 0
        };
        retObj[field] = 1;
        return retObj;
    }

    var ga_createwithlog = function ga_createwithlog( trackerId, trackerName) {
        ////console.log(trackerId + " " + trackerName);
        if (debugGA)
            window.ga_debug = {trace: true};
        ga('create', trackerId, 'auto', {'name': trackerName });        
        //if (debugGA && location.hostname == 'localhost') 
          //   ga('set', 'sendHitTask', null);
    }       
    var gaCommonInfo = null;
    
    global.gaSetCommonInfo = function gaSetCommonInfo(metainfo) {
        if (metainfo) {
            spID = 
            gaCommonInfo = { eventLabel : metainfo.video_id + '|' + metainfo.title,
                         spID : (metainfo.sp_id ? metainfo.sp_id.toString() : ''),
                         eventCategory : global.location.hostname };
        }
        if (metainfo.ga_tracking_id)
            gUA = metainfo.ga_tracking_id;
        //one-off event: playing of hotspot-enabled video
        window.gaTrackHotspots.logEvent(-1);
    }
    var initMaybe = function initMaybe() {
        return (gaCommonInfo != null);
    }
    global.gaTrackHotspots = {  
        //doGA : function doGA() {
          //  return gDoGA;
        //},
        logEvent : function logEvent(/* type, */ action, advertiser, campaign, creative, hotspot, durationMaybe) {  
            if (!initMaybe()) return; //can't do a thing now.
            
            if (gUA == '') return; //nothing to do.
            var thisUA = gUA;
            
            if (gTrackerNames[thisUA] == undefined) {
                gTrackerNames[thisUA] = "Tracker" + Math.floor(Math.random() * 100000000000);
                thisUAName = gTrackerNames[thisUA];
                ga_createwithlog(thisUA, thisUAName);
                //console.log("--------------------UA name--------------");
                //console.log(thisUA);
                //console.log(thisUAName);
            }
            /* recent changes to add customDIm5 and use the Domain
               as eventCategory vs the SPID as in the past */
            if (action == -1) {
                var customObj0 = getCustomObject(-1);
                customObj0['dimension5'] = gaCommonInfo.spID;
                ga( thisUAName+'.send', 'event',
                    gaCommonInfo.eventCategory,
                    "videoStarted",
                    gaCommonInfo.eventLabel,
                    1,
                    customObj0);
                return;
            }
        
            if (hotspot != -1) 
                gLastHotspotID = hotspot;
            var customObj = getCustomObject(action);
            if (!customObj) return;
            //some how no hotspot info:
            if (hotspot == -1 && gLastHotspotID == -1) return;
            //This is a last minute request from MediaCorp: need hotspot duration
            addHotspotDuration(customObj, durationMaybe !== undefined ? durationMaybe : 0);
            
            customObj['dimension1'] = (advertiser ? advertiser.toString() : '');
            customObj['dimension2'] = (campaign ? campaign.toString() : '');
            customObj['dimension3'] = (creative ? creative.toString() : '');
            customObj['dimension4'] = (hotspot == -1 ? gLastHotspotID.toString() : hotspot.toString());
            customObj['dimension5'] = gaCommonInfo.spID;
            //////if (hotspot == -1 && gLastHotspotID == -1) return;
            
            ga( thisUAName+'.send', 'event',
                gaCommonInfo.eventCategory,
                "hotspot" +  getEActionString(action),
                gaCommonInfo.eventLabel,
                1,
                customObj);
                
                //console.log("event to spit out-----------------------------");
                //console.log(gaCommonInfo);
                //console.log(customObj);

        }//function logEvent
    }

})(window, document); 



(function(window) {

//---------

var relResourcePath = '../../ivideotouch/v1/styles/';
//in the same-origin-iframe version of ivideostream we cannot rely on the
//relative pathing to work. Instead we will rely on this which is set for 
//ivideostream
if (window.searchParam && window.searchParam.serverPath) {
    relResourcePath = window.searchParam.serverPath  + 'ivideotouch/v1/styles/';
}
else {
    var scripts = document.getElementsByTagName('script');
    var csssrc = scripts[scripts.length - 1].src;
    if (csssrc.indexOf('core.js') >= 0) { 
        csssrc = csssrc.split('/');
        csssrc.pop();           
        csssrc = csssrc.join('/'); 
        csssrc = csssrc  + '/../styles/'; 
        relResourcePath = csssrc;
        //actually is absolute path
    }
}

var stylesPath = '/styles/';

function notifyHSEMaybe(msgtype, obj) {
  if (isHotspotEditor && window.ivtouch_fireEvent)
    window.ivtouch_fireEvent(msgtype, obj);
}

/********************************************************
 *
 * Aux routines for banner animation (since we do not use jQuery
 *
 ********************************************************/
    var AnimationInterval = 50; //milliseconds

    //asynchronous loading of image.
    var loadImage = function(imageURL, imageContainer, postProcessing) {
      var downloadingImage = new Image();
        downloadingImage.onload = function() {
          postProcessing();
      };
      _setElmSrc(imageContainer, imageURL);
      downloadingImage.src = imageURL;
    },
    Animate = function(element, AnimationStep, IsVert, IsTopOrLeftAnchored, currpercent, fcn) {
        var newpercent = currpercent + AnimationStep;
        var done = false;
        if (AnimationStep > 0) {
            if (newpercent >= 100) { newpercent = 100; done = true;}
        }
        else {
            if (newpercent <= 0) { newpercent = 0;  done = true;}
        }
        
        if (IsVert) {
          if (!IsTopOrLeftAnchored)
              element.style.top = (100 - newpercent) + "%";
          element.style.height = newpercent + "%";
        }
        else {
          if (!IsTopOrLeftAnchored)
              element.style.left = (100 - newpercent) + "%";
          element.style.width = newpercent + "%"; 
        }

        if (!done) {
            window.setTimeout(function() {
                Animate(element, AnimationStep, IsVert, IsTopOrLeftAnchored, newpercent, fcn);
                    }, AnimationInterval);
            return false;
        }
        fcn();
        return true;
    },


/********************************************************
 *
 * Aux routines for banner animation and general use
 * (since we do not use jQ
 *
 ********************************************************/
    _addEvent = function(object, type, callback) {
        //so far only for resize events
        if (object == null || typeof(object) == 'undefined') return;
        if (object.addEventListener) {
            object.addEventListener(type, callback, false);
        } else if (object.attachEvent) {
            object.attachEvent("on" + type, callback);
        } else {
            object["on"+type] = callback;
        }
    },
    _makeVisible = function makeVisible(elm) { 
        if (elm) elm.style.display = 'block'; 
    },
    _makeInvisible = function makeInvisible(elm) { 
        if (elm) elm.style.display = 'none'; 
    },
    _getElmById = function getElmById(id) {
        return document.getElementById(id);
    },
    _openURL = function openURL(url) {
        //Provide option to stay in current page?
        //location.href = url;
        //No: won't work coz this time we are running in an iframe!!!
        window.open(url);
    },
    _addClass = function addClass(elm, className) {
      if (elm.classList)
          elm.classList.add(className);
      else        
          elm.className += " " + className;
    },
    _removeClass = function removeClass(elm, className) {
      if (elm.classList)
          elm.classList.remove(className);
      else      
         elm.className  = elm.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');  
    },

    _setElmSrc = function setElmSrc(elm, src) {

        elm.src = src;
    },
    _getElmWidth = function getElmWidth(elm) {
        //return elm.offsetWidth; //clientWidth;
        return elm.clientWidth;
    },
    _getElmHeight = function getElmHeight(elm) {
        //return elm.offsetWidth; //clientWidth;
        return elm.clientHeight;
    },

    _setElmWidth = function setElmWidth(elm, w) {
        elm.setAttribute("style", "width:" + w + "px");
        //elm.offsetWidth = w;
        //elm.clientWidth = w;
    },
    _showElm = function showElm(elm) {
        elm.style.display = 'block';
    },
    _hideElm = function hideElm(elm) {
        elm.style.display = 'none';
    },
    _createElm = function createElm(params) {
        var elm = document.createElement(params.type),
            c = elm.style;
        if (params.classes)
            elm.className = params.classes;
        if (params.src)
            elm.src = params.src;
        if (params.id)
            elm.id = params.id;
        for (var s in params.styles) {
            elm.style[s] = params.styles[s];
        }
        return elm;
    },
    _setElmClickHandler = function setElmClickHandler(elm, fcn) {
        elm.onclick = fcn;
    },
  _setElmStyles = function setElmStyles(elm, styles) {
        for (var s in styles) {
            elm.style[s] = styles[s];
        }
    },
    _emptyElm = function emptyElm(parent) {
        while (parent.firstChild) parent.removeChild(parent.firstChild);
    },
    _append = function append(parent, children) {
        for (var i = 0; i < children.length; i++) {
            if (children[i]) parent.appendChild(children[i]);
        }
        return parent;
    },
    _removeElm = function(elm) {
        if (!elm.parentNode) {
            console.log('grave error');
            return;
        }
        elm.parentNode.removeChild(elm);
    };

  //var el = document.getElementById("div1");
  var 
  _fadeIn = function fadeIn(el) {
      el.style.opacity = 0;
      var tick = function() {
          el.style.opacity = +el.style.opacity + 0.01;
          if (+el.style.opacity < 1) {
              (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16)
          }
      };
      tick();
  },
  _fadeOut = function fadeOut(el) {
      el.style.opacity = 1;
      var tick = function() {
          el.style.opacity = +el.style.opacity - 0.01;
          if (  +el.style.opacity > 0) {
              (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16)
          }
      };
      tick();
  };

/********************************************************
 * This is a very useful wrapper to wrap around each hotspot obj
 * defined in the JSON file
 ********************************************************/

function HSWrapper(frame, hsobj0) {
    var hsobj = hsobj0;
    var adtype = hsobj.ad_type;
    var ClickHandler;

    var endtime = hsobj.start + hsobj.duration;
    this.intro = (hsobj.intro ? hsobj.intro : ""); //this is just for demo
    this.end = endtime;
    this.start = frame;
    this.id = hsobj0.id;
    this.hsobj = hsobj;
    var that = this;


    if (hsobj.creativeInfo) {
        //useful redundancy
        hsobj.creativeInfo.id = hsobj.id;
    }

    if (adtype == 'banner') { 
        //After all the changes of ideas, looks like now only have redirect!
        //No more infoCard as level-2 object for banner!
        ClickHandler = (true || hsobj.l2_type == 'redirect') ? 
            function () { 
                player.doPause(); 
                //Acc to DB: clicking on the banner etc will not open the thing
                //in editor. So commenting htis out.
                //notifyHSEMaybe('adClicked', hsobj);
                _openURL(hsobj.creativeInfo.url);
            } :
            function () { 
                //notifyHSEMaybe('adClicked', hsobj);
                _infoCardModal.showModal(hsobj.creativeInfo); 
            };
        this.createOverlay = function(container) {
            return new Banner(  hsobj.id, endtime, hsobj, ClickHandler, container );
        }
    }
    else if (adtype == 'hotspot-legacy') { 
      hsobj.creativeInfo.thumbnail_url = relResourcePath + "ad_indicator.png"; 
        ClickHandler = 
            function () { 
                //notifyHSEMaybe('adClicked', hsobj);
                _hotspotModal.showModal(hsobj.creativeInfo); 
            }; 
        this.createOverlay = function(container) {
            return new Hotspot(  hsobj.id, endtime, hsobj, ClickHandler, container );
        }
    }
    else if (adtype == 'hotspot') { 
        if (!hsobj.creativeInfo.thumbnail_url) 
            hsobj.creativeInfo.thumbnail_url = relResourcePath + "small_qmark.png";
        //Temporary while we wait for the ivx side to add these info into the JSON.
        //renee tmp hor
        ClickHandler = 
            function () { 
                _infoCardModal.showModal(hsobj.creativeInfo); 
                //notifyHSEMaybe('adClicked', hsobj);
            };
        this.createOverlay = function(container) {
            return new Hotspot(hsobj.id, endtime, hsobj, ClickHandler, container);
        }
    }
    /***
    Management decided not to include these features for now.
    else if (adtype == 'leaderboard') { 
        ClickHandler = hsobj.l2_type == 'redirect' ? 
            function () { player.doPause(); _openURL(hsobj.creativeInfo.url);
                          notifyHSEMaybe('adClicked', hsobj);
                      } :
            function () { _infoCardModal.showModal(hsobj.creativeInfo);                 
                          notifyHSEMaybe('adClicked', hsobj);
                        };
        this.createOverlay = function(container) {
            return new Leaderboard(hsobj.id, endtime, hsobj.creativeInfo, ClickHandler, container);
        }
    }
    else if (hsobj.ad_type == 'adtag') { 
        this.createOverlay = function(container) {
            _adNetwkOverlay.settag(endtime, hsobj.url);
            return _adNetwkOverlay;
        }
    }***/
  
}

/********************************************************
 *
 * banner multiple-ton object
 * Exposed functions: 'constructor' 
                      start (make the object appear)
 *                    stop (make it disappear) 
 * Exposed var:       endtime, id
 *
 ********************************************************/
function Banner(id, endtime, hsobj, clickHandler, parent) {
    this.id = id; 
    this.endtime = endtime;

    function BannerGetAnchor(dir) { return (dir == 'left2right' || dir == 'top2bottom');}
    function BannerIsVert(dir) { return (dir == 'top2bottom' || dir == 'bottom2top');}
    function BannerIsT2B(dir) { return (dir == 'top2bottom'); }
    function BannerIsB2T(dir) { return (dir == 'bottom2top'); }
    function BannerIsL2R(dir) { return (dir == 'left2right'); }
    function BannerIsR2L(dir) { return (dir == 'right2left'); }

    var box = hsobj.rect.split(",");
    
    var holderDiv = _createElm({
        type: 'div',
        id: 'ivs-banner-holder-' + id, //<-- really necessary?
        classes: 'ivs-banner-holder',
        styles: {
           'top': box[0] + '%',
           'left': box[1] + '%',
           'width': box[2] + '%',
           'height': box[3] + '%'
        }
    });
    _append(parent, [holderDiv]);
    _setElmClickHandler(holderDiv, clickHandler );
    
    var overlayImage = _createElm({
        type: 'img',
        id: 'ivs-banner-image-' + id, //<--- is this really necessary??
        //styles: {
          // 'opacity': 0.7
        //}
    });
    _append(holderDiv, [overlayImage]);
    //dirty or not.

    var dir = hsobj.direction;
    var image_url = (hsobj.image_url ? hsobj.image_url : hsobj.creativeInfo.image_url);

    
    //dirty means e.g. banner means to show 20thsec - 30thsec
    //user seek from 0 to 25th second.
    //Then the banner will just be drawn. No start with animation.
    this.start = function(dirty) {
        if (dirty != 'undefined' && dirty) {
             _setElmStyles(overlayImage, {
                'width': '100%',
                'height': '100%',
            });
            loadImage(image_url, overlayImage, function() {});
            return;
        }
        animateBanner(holderDiv, overlayImage, dir, image_url);
    }
    this.stop = function(dirty) {
        
        if (dirty != 'undefined' && dirty) {
            //remove from DOM without animation effects
            if (holderDiv) {
                _removeElm(holderDiv);
                holderDiv = null;
            }
            return;
        }
        Animate(overlayImage, -5, BannerIsVert(dir), BannerGetAnchor(dir), 100,
            function() {
                _fadeOut(overlayImage);
                _removeElm(holderDiv); 
                holderDiv = null;
        });
    }
    var animateBanner = function(outerDiv, innerDiv, dir, image_url) {
        var start_attr;
        if (BannerIsVert(dir)) {
            _setElmStyles(innerDiv, {
                'width': '100%'
            });
        } else {
            _setElmStyles(innerDiv, {
                'height': '100%'
            });
        }
        _fadeIn(outerDiv);
        loadImage(image_url, innerDiv, function() {
            var end = (BannerIsVert(dir) ? Math.round(_getElmHeight(outerDiv)) :
                Math.round(_getElmWidth(outerDiv)));
            if (BannerIsL2R(dir)) { 
                start_attr = {
                    width: '0px'
                };
            } else if (BannerIsR2L(dir)) { 
                start_attr = {
                    left: end + 'px',
                    width: '0px'
                };
            } else if (BannerIsT2B(dir)) { 
                start_attr = {
                    height: '0px'
                };
            } else if (BannerIsB2T(dir)) { 
                start_attr = {
                    top: end + 'px',
                    height: '0px'
                };
            }
            _setElmStyles(innerDiv, start_attr);
            _fadeIn(innerDiv);
            var step = 5; //each time 5%
            var LOrTAnchored = (BannerGetAnchor(dir));
            Animate(innerDiv, step, BannerIsVert(dir), LOrTAnchored, 0, function() {});
        }); //end of load image
    }; //end of animate banner}
}

/********************************************************
 * 
 * This "class" takes care of hotspots legacy and new styles
 * Old style hotspot = the faint matt white rectangle
 * Can be multiple instances of this object. Exposed:
 * Exposed functions: 'constructor' 
                      start (make the object appear)
 *                    stop (make it disappear) 
 * Exposed var:       endtime, id
 
 *
 ********************************************************/
 function Hotspot(id, endtime, hsobj, clickHandler, parent)  { 
    this.id = id; 
    this.endtime = endtime;

    var cssClass = '';
    switch (parseInt(hsobj.position)) {
        case 1:
            cssClass = 'top-left';
            break;
        case 2:
            cssClass = 'top-right';
            break;
        case 3:
            cssClass = 'bottom-left';
            break;
        case 4:
            cssClass = 'bottom-right';
            break;
        default:
            cssClass = 'top-left';
    }
    
    var image = new Image();
    
    // this will execute when image loaded. handle the vertical image.
    image.onload = function() {
        if( this.width < this.height ) {
           document.getElementById('ivs-hotspot-holder-' + id).className += " vertical"; 
        } 
    }
    image.src = hsobj.creativeInfo.thumbnail_url;
    
    // Set class if ratio is 4:3
    if ( Math.ceil( (parent.offsetWidth*3) / (parent.offsetHeight*4) ) === 1 ) { 
       document.getElementById('ivs-hotspot-holder-' + id).className += " ratio-4by3"; 
    }

    var idstr = 'ivs-hotspot-holder-' + id;
    var holderDiv = _createElm({
        type: 'div',
        id: idstr,
        classes: 'ivs-hotspot-holder ' + cssClass
    });
    _append(parent, [holderDiv]);

    //if there is a secondary image to be shown in for the hotspot, 
    //use that one (creativeInfo is the one when the hotspot "opens up" 
    //upon click)
    var image_url = (hsobj.image_url ? hsobj.image_url : hsobj.creativeInfo.thumbnail_url);

    var overlayImage = _createElm({
        type: 'img',
        src: image_url
    });

    //What is a better way to differentiate
    if (image_url.indexOf(stylesPath) == -1)  {
        //i.e. it is not our own standard icons but something from IVX
        _addClass(overlayImage, 'circular--square');
    }        
    
    _append(holderDiv, [overlayImage]);
    holderDiv.onclick = clickHandler;

    this.start = function() {
        //doGAMaybe get the info from the hsobj.
        //hotspot id is from the outside. (difficult)
        //but all the rest are from inside (good)
        if (window.gaTrackHotspots)
            window.gaTrackHotspots.logEvent(0,  /* 0 == type hotspot show up */
                hsobj.creativeInfo.advertiser_id, 
                hsobj.creativeInfo.campaign_id, 
                hsobj.creativeInfo.creative_id, 
                id,
                hsobj.duration);
        holderDiv.style.display = 'block';
    }
    this.stop = function() {
        if (holderDiv) {
            _removeElm(holderDiv);
            holderDiv = null;
        }
    }
 }
    

 
/********************************************************
 * 
 * NOT USED RIGHT NOW
 *
 * These helper functions are used for leaderboard and 
 * infocard.
 * This is for layout out the textual description info. 
 * Sometimes the text is too long and we need to use ... 
 * to abbreviate it
 *
 ********************************************************/
var _truncate = function(elt, options) {
    var clientHeight = elt.clientHeight;
    var offsetHeight = elt.offsetHeight;
    var height = parseInt(clientHeight);
    var content = elt.innerHTML;
    while (elt.scrollHeight > height) {
      content = content.replace(/\s+\S*$/, "");
      elt.innerHTML = content + options;
    }
}

var reformatProductDesc = function(handles) {
    //When the video is resized etc, if the infocard etc is 
    //visible at that time, need to redo the text.
    handles.descdiv.innerHTML = handles.fulldesc;
    setTimeout(function() {
        _truncate(handles.descdiv, "...");
    }, 200);
}

var addProductDesc = function(card, infoCard) {
    _emptyElm(card); //clear child
    var photodiv, /* photodiv0, */ photoimg;
    var titlediv, /* titlediv0, */ titlespan; 
    var descdiv, /* descdiv0, */ descspan;

    var photodiv = _append(
        //arg1
        /* photodiv0 = */ _createElm({
            type: "div",
            classes: "photodiv"
        }),
        //arg2
        [
            photoimg = _createElm({
                type: "img",
                classes: "photo"
            })
        ]
    );
    var titlediv = _append(
        //arg1
        /* titlediv0 = */ _createElm({
            type: "div",
            classes: "title"
        }),
        //arg2
        [
            titlespan = _createElm({
                type: "span"
            })
        ]
    );
    var descdiv = _append(
        //arg1
        /* descdiv0 = */ _createElm({
            type: "div",
            classes: "description"
        }),
        //arg2
        [
            descspan = _createElm({
                type: "span"
            })
        ]
    );
    _append(card, [photodiv, titlediv, descdiv]);
    titlespan.innerHTML = infoCard.title;
    descspan.innerHTML = infoCard.description;
    photoimg.src = infoCard.image_url;

    //Sometimes it takes a while for the field to be set ?!!
    if (descdiv.clientHeight != 0)
        _truncate(descdiv, "...");
    else {
        setTimeout(function() {
            _truncate(descdiv, "...");
        }, 100);
    }
    return { 'descspan': descspan, 'descdiv': descdiv, 'fulldesc' : infoCard.description };
    //need deep clone?

}

/********************************************************
 *
 * A bunch of JS objects to manage some "modal" things.
 * 
 * Hotspot Modal
 //* Adnetwork overlay
 * infoCard Modal
 * infoCards Deck
 *
 ********************************************************/
 
var _hotspotModal = null;
//////var _adNetwkOverlay = null;
var _favourites = null;
var _infoCardModal = null;
var _infoCardsDeck = null;

/* 
There are 4 self-running functions inside this _initModals
LONG.
 */
//Wrap these self-running functions in here so that
//they don't run too early
var _initModals = function() {

    /********************************************************
    * the Traditional Hotspot Modal
    * Included for backwards comp
    * Singleton class to manage that modal dlg box. Exposes:
    * -fixCloseButtonPosition
    * -init
    * -showItem
    * -hide
    *********************************************************/
    _hotspotModal = (function() {
    var modalInner;
    var modalClose;
    var modalContent;
    var modalPic;
    var modalDiv;
    var modalVisible;

    var parent;
    var showCB = null;
    var selfCloseCB = null;
    var init = false;

    var createObj = function() {
        return new HotspotModal();
    }
    var HotspotModal = function() {
        this.onresize = function() {
          if (!modalVisible) return;
            var width = _getElmWidth(modalPic);
            _setElmWidth(modalInner, width);
            _showElm(modalClose);
        }
        //if called this way, no we do not need to call play
        this.closeModal = function() {
            if (modalVisible) {
                _hideElm(modalDiv);
                modalVisible = false;
            }
        }
        this.init = function(parent0, showCB0, selfCloseCB0) {
            showCB = showCB0;
            selfCloseCB = selfCloseCB0;
            parent = parent0;
        }
        
        this.showModal = function(params) {
            if (!init) realinit(parent);
            loadImage(params.image_url,
            modalPic,
            function() {
                modalVisible = true;
                showCB();
                _showElm(modalDiv); 
                var width = _getElmWidth(modalPic);
                _setElmWidth(modalInner, width);
                _showElm(modalClose);
                _setElmClickHandler(modalPic,
                    function() {
                        _openURL(params.url);
                    }
                );
                _setElmClickHandler(modalClose,
                    function() {
                        modalVisible = false;
                        _hideElm(modalDiv);
                        selfCloseCB();
                    }
                ); /* end of click handler for close button */
            }
            ); //end of load image handler
        }

        var realinit = function(parent) {
            init = true;
            modalAdFrame = _append(
                //arg1
                _createElm({
                    type: "div",
                    classes: "ivs-hotspot-modal-container"
                }),
                //arg2
                [
                    _append(
                        //arg1
                        modalInner = _createElm({
                            type: "div",
                            classes: "ivs-hotspot-modal-inner"
                        }),
                        //arg2 
                        [
                            modalClose = _createElm({
                                type: "div",
                                classes: "ivs-hotspot-modal-close"
                            }),
                            _append(
                                //arg1
                                modalContent = _createElm({
                                    type: "div",
                                    classes: "ivs-hotspot-modal-content"
                                }),
                                //arg2
                                [
                                    modalPic = _createElm({
                                        type: "img",
                                        classes: "ivs-hotspot-modal-pic"
                                    })
                                ]
                            ) //close append
                        ]
                    ) //close append
                ]
            ); //close append

            modalDiv = _createElm({
                type: 'div',
                id: 'ivs-hotspot-modal',
                styles: {
                    'position': 'absolute',
                    'width': '100%',
                    'height': '86%',
                    'left': '0%',
                    'top': '0%',
                    'zIndex': '2147483647'
                }
            });
            _append(modalDiv, [modalAdFrame]);
            _append(parent, [modalDiv]);
            _hideElm(modalDiv);
        }
    }
    return createObj();
})();



    /********************************************************
    *
    * Ad Network overlay controller
    *
    Unfortunately this is still not supported by the SDK. 
    You can use multiple AdsManagers to make requests, 
    but you need to destroy your first AdsManager before 
    creating your second one.
    Cannot multiple adsManager at the same time!

    ********************************************************/
    /************ no one wants this feature right now
    _adNetwkOverlay = (function() {
    var createObj = function() {
        return new AdNetworkOverlay();
    }
    var AdNetworkOverlay = function() {
        var adtag = null;
        var adsLoader;
        var adsManager = null;
        var adDisplayContainer;
        var init = false;
        var parentDiv;
        var videoElt;
        var notinit = true;
        var adContainer;

        var realinit = function() {
            adsManager = null;
            adContainer = _createElm({
                type: 'div',
                id: 'ivs-for-adnetwork',
            });
            parentDiv.appendChild(adContainer);
            var f = 0.3;
            _setElmStyles(adContainer, {
                    'position' : 'absolute',
                    'pointer-events' : 'none',
                    'bottom' : '20px',
                    'width': '100%',
                    'zIndex': '0',
                    'height' : '30%'
            });
            adDisplayContainer = new google.ima.AdDisplayContainer(
                adContainer, videoElt);
            adsLoader = new google.ima.AdsLoader(adDisplayContainer);
            // Listen and respond to ads loaded and error events.
            adsLoader.addEventListener(
                google.ima.AdsManagerLoadedEvent.Type.ADS_MANAGER_LOADED,
                adsMgrLoadedCB,
                false);
            adsLoader.addEventListener(
                google.ima.AdErrorEvent.Type.AD_ERROR,
                adErrorCB,
                false);
            init = true;
        }
        
        this.stop = function() {
            adContainer.style.pointerEvents = 'none';

            if (adsManager) {
                adsManager.stop();
                adsManager.destroy();
                adsManager = null;
            }
        }
        //called each time a new request
        this.settag = function(endtime0, adtag0) {
            adtag = adtag0;
            this.endtime = endtime0;
        }
        this.start = function() {
            if (!adtag) return;
            if (!init) realinit();
            adContainer.style.pointerEvents = 'auto';
            this.stop(); 
            var adsRequest = new google.ima.AdsRequest();
            adsRequest.nonLinearAdSlotWidth = 0;
            adsRequest.nonLinearAdSlotHeight = 0;
            adsRequest.adTagUrl = adtag;
            adsLoader.requestAds(adsRequest);
            adtag = null;            
        }
        var adsMgrLoadedCB = function(adsManagerLoadedEvent) {

            //
            //autoAlign   boolean 
            //-Set to false if you wish to have fine grained control over the 
            //positioning of all non-linear ads. 
            //-If this value is true, the ad is positioned in the bottom center. 
            //-If this value is false, the ad is positioned in the top left corner. 
            //-The default value is true.

            //useStyledNonLinearAds   boolean 
            //-Render non-linear ads with a close and recall button.
            

            var adsRenderingSettings = new google.ima.AdsRenderingSettings();
            //adsRenderingSettings.restoreCustomPlaybackStateOnAdBreakComplete = true;
            adsRenderingSettings.autoAlign = false;
            adsRenderingSettings.useStyledNonLinearAds = true;
            //https://developers.google.com/interactive-media-ads/docs/sdks/html5/v3/apis
            adsManager = adsManagerLoadedEvent.getAdsManager(
                videoElt, adsRenderingSettings);
            adsManager.addEventListener(
                google.ima.AdEvent.Type.LOADED,
                function(adEvent) {//chance to change size of adsMgr
                    var ad = adEvent.getAd();
                    //console.log('b4 adLoadedCB=' + ad.getWidth() + ' ' + ad.getHeight());
                    adsManager.resize(ad.getWidth()+10 , ad.getHeight()+10 , google.ima.ViewMode.NORMAL);
                });
            if (notinit) {
                // Initialize the container. Must be done via a user action on mobile devices.
                //TODO We might have a problem here for mobile!
                adDisplayContainer.initialize();
                notinit = false;
            }
            try {
                // Initialize the ads manager. Ad rules playlist will start at this time.
                adsManager.init(640, 360, google.ima.ViewMode.NORMAL);
                // Call play to start showing the ad. Single video and overlay ads will
                // start at this time; the call will be ignored for ad rules.
                adsManager.start();
            } catch (adError) {}
        }

        var adErrorCB = function(adEvent) { 
            console.log('adErrorCB ' + adEvent.getError());
        }
        
        //var adLoadedCB = function(adEvent) { 
            //chance to change size of adsMgr
          //   var ad = adEvent.getAd();
            // console.log('b4 adLoadedCB=' + ad.getWidth() + ' ' + ad.getHeight());
             //adsManager.resize(ad.getWidth()+10 , ad.getHeight()+10 , google.ima.ViewMode.NORMAL);
        //}
        this.init = function(parentDiv0, videoElt0) {
            parentDiv = parentDiv0;
            videoElt = videoElt0;
            //only really do the nec init in the first use
        }
    }
    return createObj();
})();
************ no one wants this feature right now */

    /********************************************************
    *
    * Favourites' container. 
    * Singleton to store the user's selection of fav in the session
    *
    ********************************************************/
    _favourites = (function() {
    var createObj = function() {
        return new Favourites();
    }
    var favArray = [];
    var currIdx = -1;
    var firsttime = true;
    var Favourites = function() {
        this.addMaybe = function(obj) { 
            if (firsttime) {
                player.doAddListFavButton();
                firsttime = false;
            }
            //we should add the STAR to the menu bar.
            var found = favArray.some(function (el) {
                return el.id === obj.id;
            });
            if (!found) { favArray.push(obj); }
            //console.log(favArray);
            _sanityFix();
        }
        this.getNum = function() {
            return favArray.length;
        }
        this.getFavArray = function () {
            return favArray;
        }
        this.reset = function() {
            currIdx = 0;
        }
        this.isAtFirst = function() {
            return (currIdx == 0);
        }
        this.isAtLast = function() {
            return (currIdx == favArray.length -1);
        }
        var _sanityFix = function () {
            if (currIdx == -1) currIdx = 0;
            if (currIdx >= favArray.length) currIdx = 0;
        }
        this.getCurrent = function() {
            if (favArray.length == 0) return null;
            _sanityFix();
            return favArray[currIdx];
        }
        this.getCurrIdx = function() {
            _sanityFix();
            return currIdx;
        }
        this.getNext = function() {
            if (favArray.length == 0) return null;
            _sanityFix();
            if (++currIdx == favArray.length)
                ; ///////currIdx = 0;
            return favArray[currIdx];
        }
        this.getPrevious = function() {
            if (favArray.length == 0) return null;
            _sanityFix();
            if (--currIdx == -1) 
                ; //////currIdx = favArray.length -1;
            return favArray[currIdx];
        }
    }
    return createObj();
})();

    /********************************************************
    *
    * InfoCard mgr obj:
    * This is used by both the infoCardModal and the infoCardsDeck
    *   InfoCardsDeck shows the list of ads the user has "favourited"
    *   It looks like infoCardModal but with extra < (prev) 
    *   and > (next) buttons
    * 
    * (Coz visually they are similiar and only 1 can be up at
    * any one time)
    * (those are thin wrappers)
    * This is the one doing all the rendering work:
    * Singleton to populate, show and hide the infoCard modal
    *
    ********************************************************/
    _infoCardMgr = (function() {
    var createObj = function() {
        return new InfoCardMgr();
    }
    var InfoCardMgr = function() {
        var init = false;
        var card;
        var card_infocard;
        var card_graphic;
        var savectrl_infocard;
        var savectrl_graphic;
        var visitctrl_infocard;
        var visitctrl_graphic;
        var prevctrl;
        var nextctrl;
        var likeGraphic;
        var likeInfocard;
        var closectrl;

        var imagesrc_graphic;
        var title;
        var desc; 
        var imagesrc_infocard; 

        this.populate = function(parentDiv0, infoCard, isdeck) {
            var iscard = (infoCard.hasOwnProperty('title') && infoCard.title != "");
            if (!init) 
                realinit(parentDiv0, this, infoCard);
            else {
                if (iscard) {
                    imagesrc_infocard.src = infoCard.image_url;
                    title.innerHTML = infoCard.title;
                    desc.innerHTML = infoCard.description;
                }
                else {
                    imagesrc_graphic.src = infoCard.image_url;
                }
            }
            
            var allFav = _favourites.getFavArray()
            var liked = false;

            allFav.forEach( function(fav) {
                if (fav.id === infoCard.id) liked = true;
            })

            if (iscard) {
                this.save = savectrl_infocard;
                this.visit = visitctrl_infocard;
                this.likeText = likeInfocard;
                _makeVisible(card_infocard);
                _makeInvisible(card_graphic);
            }
            else {
                this.save = savectrl_graphic;
                this.visit = visitctrl_graphic;
                this.likeText = likeGraphic;   
                _makeVisible(card_graphic);
                _makeInvisible(card_infocard);
            }
            if (isdeck) {
                _makeVisible(this.visit);
                _makeInvisible(this.save);
                _makeVisible(this.likeText);
                this.previous = prevctrl;
                this.next = nextctrl;
                _makeVisible(prevctrl);
                _makeVisible(nextctrl);
            }               
            else {
                if (!liked) _makeVisible(this.save);
                _makeVisible(this.visit);
                _makeInvisible(this.likeText);
                this.previous = null;
                this.next = null;
                _makeInvisible(prevctrl);
                _makeInvisible(nextctrl);
            }
            _makeVisible(card);

        }
        var realinit = function(parentDiv0, that, infoCard) {
            card = _createElm({
                type: 'div',
                classes: 'preview-creative-ad invisible-info-card layertop-info-card'  
            });
            var iscard = (infoCard.hasOwnProperty('title') && infoCard.title != "");
            var pic_infocard, pic_graphic, titlestr, descstr;
            if (iscard) {
                pic_infocard = infoCard.image_url;
                pic_graphic = '';
                titlestr = infoCard.title;
                descstr = infoCard.description;
            }
            else {
                pic_graphic = infoCard.image_url;
                pic_infocard = '';
                titlestr = '';
                descstr = '';
            }

            card.innerHTML = 
            "<div class='close-ivideotouch hs-clickable-ptr'><a href='#'><img src='" + relResourcePath + "close.svg'></a></div>" +
            "<div class='previous-ivideotouch hs-clickable-ptr'><a href='#'><img src='" + relResourcePath + "back.svg'></a></div>" +
            "<div class='info-card'>" +
                "<div class='image-ivideotouch'><div class='video-img'><img class='dummy1' src='" + pic_infocard + "'></div></div>" +
                "<div class='content-ivideotouch'>" +
                    "<div class='content-name'>" + titlestr + "</div>" +
                    "<div class='content-ivideotouch-info'>" + descstr + "</div>"  +
                    "<div class='content-ivideotouch-button'>"  + 
                        "<a><div class='ivideotouch-btn favourite hs-clickable-ptr'><img src='" + relResourcePath + "like.png'>Like</div></a>" + 
                        "<a><div class='ivideotouch-btn visit hs-clickable-ptr'><img src='" + relResourcePath + "new-window.png'>Visit</div></a>" +
                        "<p class='like-text like-infocard'><span class='number-of__infocard'>Liked: 1 of 1</span></p>" +
                    "</div>" + 
                "</div>" + 
            "</div>" +   

            "<div class='image-cover'>" +
                "<div class='video-img'>" + 
                    "<a href='#'><img class='dummy1' src='" + pic_graphic + "'></a></div>" + 
                "<div class='content-ivideotouch-button'>"  + 
                    "<p class='like-text like-graphic'><span class='number-of__graphic'>Liked: 1 of 1</span></p>" +
                    "<a><div class='ivideotouch-btn favourite hs-clickable-ptr'><img src='" + relResourcePath + "like.png'>Like</div></a>" + 
                    "<a><div class='ivideotouch-btn visit hs-clickable-ptr'><img src='" + relResourcePath + "new-window.png'>Visit</div></a>" +
                "</div>" + 
            "</div>" +   
            "<div class='next-ivideotouch hs-clickable-ptr'><a href='#'><img src='" + relResourcePath + "next.svg'></a></div>";
            _append(parentDiv0, [card]);
            
            card_infocard = card.getElementsByClassName('info-card')[0];   
            card_graphic = card.getElementsByClassName('image-cover')[0];   

            imagesrc_graphic = card_graphic.getElementsByClassName('dummy1')[0];   

            title = card_infocard.getElementsByClassName('content-name')[0];   
            desc = card_infocard.getElementsByClassName('content-ivideotouch-info')[0];   
            imagesrc_infocard = card_infocard.getElementsByClassName('dummy1')[0];   

            savectrl_infocard = card_infocard.getElementsByClassName('favourite')[0];
            savectrl_graphic = card_graphic.getElementsByClassName('favourite')[0];
            visitctrl_infocard = card_infocard.getElementsByClassName('visit')[0];
            visitctrl_graphic = card_graphic.getElementsByClassName('visit')[0];
            prevctrl = card.getElementsByClassName('previous-ivideotouch')[0];
            nextctrl = card.getElementsByClassName('next-ivideotouch')[0];
            likeInfocard = card_infocard.getElementsByClassName('like-text')[0];
            likeGraphic = card_graphic.getElementsByClassName('like-text')[0];
            that.close = card.getElementsByClassName('close-ivideotouch')[0];
            that.numberOfInfocard = card.getElementsByClassName('number-of__infocard')[0];
            that.numberOfGraphic = card.getElementsByClassName('number-of__graphic')[0];

            init = true;
        }
        this.hide = function() {
            _makeInvisible(card);
        }
    }
    return createObj();
})();


    /********************************************************
    *
    * InfoCard Modal. Somewhat richer in content than the hotspot modal
    *
    ********************************************************/
    _infoCardModal = (function() {
    var createObj = function() {
        return new InfoCardModal();
    }
    var InfoCardModal = function() {
        var parentDiv;
        var showCB = null;
        var selfCloseCB = null;

        //showCB = showCB (i.e. a function in case you want some code to be
        //run when the dialog shows. e.g. Pause the play)
        this.init = function(parentDiv0, showCB0, selfCloseCB0) {
            parentDiv = parentDiv0;
            showCB = showCB0;
            selfCloseCB = selfCloseCB0;
        }
        var realinit = function() { 

        }            
        this.showModal = function(infoCard) {
            //doGAMaybe(type, action, title, starttime, campaign) 
            //here we also have the whole infoCard.
            if (window.gaTrackHotspots)
                window.gaTrackHotspots.logEvent(1,  /* 1 == type view infocard */
                infoCard.advertiser_id, 
                infoCard.campaign_id, 
                infoCard.creative_id, 
                -1);
        
        
            _infoCardMgr.populate(parentDiv, infoCard, false /* no DECK */);
            _infoCardMgr.close.onclick = function() {
                _infoCardMgr.hide();
                if (selfCloseCB)
                    selfCloseCB();
            };
            _infoCardMgr.save.onclick = function() {
                //March 2018 MediaCorp: Change to do nothing.
                //Just send off the event to GA.
                /////_favourites.addMaybe(infoCard);
                /////_makeInvisible(this);
                 if (window.gaTrackHotspots)
                    window.gaTrackHotspots.logEvent(3,  /* 2 == type press LIKE button */
                    infoCard.advertiser_id, 
                    infoCard.campaign_id, 
                    infoCard.creative_id, 
                     -1);
            };
            _infoCardMgr.visit.onclick = function() {
                if (window.gaTrackHotspots)
                    window.gaTrackHotspots.logEvent(2,  /* 2 == type click URL */
                    infoCard.advertiser_id, 
                    infoCard.campaign_id, 
                    infoCard.creative_id, 
                     -1);
        
                //doGAMaybe ok luckily here we can depend on the closure as well.
                _openURL(infoCard.url);
            };
            if (showCB)
                showCB();
        }
        this.closeModal = function() {
            _infoCardMgr.hide();
        }
        this.onresize = function() {
            //in the dev version this was commented out? investigate
          //if (card && textReformatHandles)
            //  reformatProductDesc(textReformatHandles);
       }
    }
    return createObj();
})();

/********************************************************
 *
 * Deck of Info Cards
 *   InfoCardsDeck shows the list of ads the user has "favourited"
 *   It looks like infoCardModal but with extra < (prev) 
 *   and > (next) buttons
 *     show 
 *     showItem (a particular hotspot on top of deck)
 *     hide
 *     clear
 *
 ********************************************************/

_infoCardsDeck = (function() {
    var createObj = function() {
        return new InfoCardsDeck();
    }
    var InfoCardsDeck = function() {
        var parentDiv;
        var selfCloseCB = null;
        var showCB = null;

        this.init = function(parentDiv0, showCB0, selfCloseCB0) {
            showCB = showCB0;
            selfCloseCB = selfCloseCB0;
            parentDiv = parentDiv0;
        }
        var _handleSensitivity = function() {
            if (_favourites.getNum() == 1) { 
                _makeInvisible(_infoCardMgr.previous);
                _makeInvisible(_infoCardMgr.next);
            }
            else {
                if (_favourites.isAtLast())
                    _makeInvisible(_infoCardMgr.next);
                else 
                    _makeVisible(_infoCardMgr.next);
                if (_favourites.isAtFirst())
                    _makeInvisible(_infoCardMgr.previous);
                else 
                    _makeVisible(_infoCardMgr.previous);
            }
        }

        var _showItem = function(infoCard) {
            _infoCardMgr.populate(parentDiv, infoCard, true /* yes DECK mode */);

            // Update number of liked ad text
            var currId = _favourites.getCurrIdx() + 1; 
            if (infoCard.type === 'info_card'){
                _infoCardMgr.numberOfInfocard.innerHTML = 'Liked: ' + currId + ' of ' + _favourites.getNum();
            } else if (infoCard.type === 'image') {
                _infoCardMgr.numberOfGraphic.innerHTML = 'Liked: ' + currId + ' of ' + _favourites.getNum();
            }

            _infoCardMgr.close.onclick = function() {
                _infoCardMgr.hide();
                if (selfCloseCB)
                    selfCloseCB();
            };
            _handleSensitivity();
        }
        this.showModal = function() {
            _favourites.reset();
            var hsW = _favourites.getCurrent();
            if (!hsW) return;
            _showItem(hsW);
            _infoCardMgr.previous.onclick = function() {
                var hsW     = _favourites.getPrevious();
                if (!hsW) return;
                _showItem(hsW);
            };
            _infoCardMgr.next.onclick = function() {
                var hsW     = _favourites.getNext();
                if (!hsW) return;
                _showItem(hsW);

            };
            if (showCB) showCB();
        }
        this.closeModal = function() {
            _infoCardMgr.hide();
        }

        this.onresize = function() {
          //if (currentCard && textReformatHandles) 
            //  reformatProductDesc(textReformatHandles);
        }
        
    }
    return createObj();
})();

} //init modals

/********************************************************
 *
 * Info Strip at bottom of video area
 *
 ********************************************************/
/*******************
function Leaderboard(id, endtime, params, clickHandler, parent) {
    var stripElt = null;
    var textReformatHandles = null;
    stripElt = _createElm({
        type: 'div',
        classes: 'leaderboard-info' 
    });
    _append(parent, [stripElt]);
                    
    this.endtime = endtime;

    textReformatHandles = addProductDesc(stripElt, params);
    stripElt.onclick = function() {
        that.stop();
        clickHandler();
    }   
    
    var close = _createElm({
        type: 'div',
        classes: 'xclose top-right-corner',
    });

    _append(parent, [stripElt]);
    _append(stripElt, [close]);
    _makeInvisible(stripElt);

    var that = this;
    close.onclick = function(evt) {
        evt.stopPropagation()
        that.stop();
    };
            

    this.endtime = endtime;
    this.start = function() {
        _makeVisible(stripElt);
    }
    this.stop = function() {
        if (stripElt) {
            _removeElm(stripElt);
            stripElt = null;
        }
    }
    this.onresize = function() {
        if (stripElt && textReformatHandles)
          reformatProductDesc(textReformatHandles);
    }
}    
*******************/

/********************************************************
 *
 * General data structure
 *
 ********************************************************/
 /****** Seems no longer used
function Mapping() {
    this.keys = new Array();
    this.data = new Object();

    this.put = function (key, value) {
        if (this.data[key] == null) {
            this.keys.push(key);
        }
        this.data[key] = value;
    };

    this.get = function (key) {
        return this.data[key];
    };

    this.remove = function (key) {
        this.keys.remove(key);
        this.data[key] = null;
    };

    this.each = function (fn) {
        if (typeof fn != 'function') {
            return;
        }
        var len = this.keys.length;
        for (var i = 0; i < len; i++) {
            var k = this.keys[i];
            fn(k, this.data[k], i);
        }
    };

    this.entrys = function () {
        var len = this.keys.length;
        var entrys = new Array(len);
        for (var i = 0; i < len; i++) {
            entrys[i] = {
                key: this.keys[i],
                value: this.data[i]
            };
        }
        return entrys;
    };

    this.isEmpty = function () {
        return this.keys.length == 0;
    };

    this.size = function () {
        return this.keys.length;
    };
}
*******/

/********************************************************
 *
 * Scheduler
 *
 ********************************************************/
var _scheduler = (function() {
    var createObj = function() {
        return new Scheduler();
    }
    var Scheduler = function() {
        var player;
        var lastAdTime = -1;
        var futureTime = 0; //we look a bit ahead to preload the nec images
        var imageSet = new Set();
        var jsonResultObj = {};
        var jsonFrameSet = new Set();
        var expiryTimes = new Set();
        var overlaysShowing = new Set();
        var container;

        var preloadHelperFcn = function(url) {
                    if (url && !imageSet.has(url)) {
                        var downloadingImage = new Image();
                        downloadingImage.onload = function(){
                            //$('<img />').attr('src',this.src).appendTo('body').css('display','none');
                            var dummyImg = _createElm({
                                type: 'img',
                                styles: {
                                    'display': 'none'
                                }
                            });
                            dummyImg.setAttribute('src', this.src);
                            document.body.append(dummyImg);
                        };                                
                        downloadingImage.src = url;    
                        imageSet.add(url);
                    }
                }
            
        function preloadMainImage(hsobj) {
            if (hsobj.creativeInfo.image_url)
                preloadHelperFcn(hsobj.creativeInfo.image_url);
            
        }
        function preloadThumbnails(theTime){
            var objects = jsonResultObj[theTime];        
            for(index in objects){
                var cr = objects[index].hsobj.creativeInfo;
                if (cr.thumbnail_url) preloadHelperFcn(cr.thumbnail_url);
            }
        }

        var loadHotspots = function(results) {
            if (!results) return; 
            function sortNumber(a, b) {
                return a - b;
            }
            var frame;
            
            var jsonLinearList = [];
            var jsonFrameArray = [];
            for (index in results) {
                frame = results[index]["frame"]
            
                jsonFrameArray.push(frame);
                jsonResultObj[frame] = results[index]["objects"];
                var objects = jsonResultObj[frame];
                for (index in objects) {
                    var o = objects[index];
                    if (o) {
                        var wrapped = new HSWrapper(frame, o);
                        objects[index] = wrapped;
                        jsonLinearList.push(wrapped);
                    }
                } //for
            } //for     
            jsonFrameArray.sort(sortNumber);
            for (idx in jsonFrameArray) {
                jsonFrameSet.add(jsonFrameArray[idx]);
            }

            preloadThumbnails(1000);
            preloadThumbnails(2000);
            preloadThumbnails(3000);
            preloadThumbnails(4000);
    
        } //function loadHotspots.

        //The following in <- -> are only useful in Hotspot editor context
        //<-------
        this.addAd = function(obj) {
            var tframe = obj.start;
            var wrapped = new HSWrapper(tframe, obj);

            if (!jsonFrameSet.has(tframe))
                jsonFrameSet.add(tframe);
            var arr = jsonResultObj[tframe];
            if (arr) {
                var idx = arr.length;
                arr[idx] = wrapped;
            }
            else {
                jsonResultObj[tframe] = new Array();
                jsonResultObj[tframe][0] = wrapped;
            }
        };

        var deleteObjFromThisFrameMaybe = function(id, tframe) {
            var arr = jsonResultObj[tframe];
            if (arr) {
                if (arr.length == 1 && jsonResultObj[tframe][0].id == id)  {
                    delete jsonResultObj[tframe]; 
                    jsonFrameSet.delete(parseInt(tframe));
                    return true;
                }
                else {
                    for (var arridx in arr){
                        if (arr[arridx].id == id)  {
                            arr.splice(arridx, 1);
                            return true;
                        } //if
                    }//for arridex
                }//else
            }    //if arr
            return false; //not done. couldn't find obj.
        };
        this.deleteAd = function(id) {
            for (var frameStr in jsonResultObj){
                arr = jsonResultObj[frameStr];
                for (var arridx in arr){
                    if (arr[arridx].id == id) {
                        var ret = deleteObjFromThisFrameMaybe(id, frameStr);
                        return ret;
                    }
                }//for
            }//for
        }
        this.lookupAd = function(id) {
            for (var frameStr in jsonResultObj){
               arr = jsonResultObj[frameStr];
                for (var arridx in arr){
                    if (arr[arridx].id == id)
                        return arr[arridx].hsobj;
                }
            }
        }

        this.updateAd = function(hsobj) {
            var that = this;
            that.deleteAd(hsobj.id);
            that.addAd(hsobj);
        }
        //------->

        var addOverlayShown = function(end_time, overlay) {
            expiryTimes.add(end_time);
            overlaysShowing.add(overlay);
        }
        var clearOverlaysMaybe = function(now) {
            if (expiryTimes.has(now)) {
                expiryTimes.delete(now);
                overlaysShowing.forEach(function(overlay) {
                    if (overlay.endtime == now) {
                        //editor complain the hotspot not hanging there long enough.
                        //even though it is. give it half a sec more.
                        setTimeout(function() {
                            overlay.stop(); //with the ad network I am not too sure. Don't let it be closed.
                            overlaysShowing.delete(overlay);
                        
                            if (hsPreviewWrapped && hsPreviewWrapped.id == overlay.id ) {
                            player.doPause();
                            }
                        }, 500);
                    }
                })
            }
        }
        this.pause = function() {
            player.doPause(); 
        }
        this.play = function() {
            player.doPlay();
        }
        this.reinit = function(results) {
            jsonResultObj = {};
            jsonFrameSet = new Set();
            expiryTimes = new Set();
            overlaysShowing = new Set();
            loadHotspots(results);
        }

        this.init = function(container0, player0, results) {
            player = player0
            container = container0;
            loadHotspots(results); 
        }
        this.getMarkedPercents = function(duration) {
            if (duration == 0) return;
            var dotPlace = [];
            for (var i in jsonResultObj) {
                var tmp = (i * 100)/ duration;
                if (tmp < 100) 
                    dotPlace.push(tmp);
            }
            return dotPlace;
        }
        this.clearModals = function() {
            if (_hotspotModal) _hotspotModal.closeModal();
            if (_infoCardModal) _infoCardModal.closeModal();
            if (_infoCardsDeck) _infoCardsDeck.closeModal();
        }
        this.resizeOverlays = function() {
            overlaysShowing.forEach(function(overlay) {
                if (overlay.onresize)
                    overlay.onresize();
            });
        }

        this.clearOverlays = function() {
            overlaysShowing.forEach(function(overlay) {
                overlay.stop(true); /* this is dirty flag: means banner disappear w/o animation */
            });
            expiryTimes.clear();
            overlaysShowing.clear();
        }

      /*
        2 functions : renderOverlaysLong & renderOverlaysDelta

        Normally each time update from player we call the lighter DELTA flavour
        It check which ad should _START_ _NOW_

        The LONG flavour is called after e.g. seek i.e. jumping of the playhead.
        Reason is we need to go thru all ads to draw those who should be visible 
        at this moment (no matter what start time they have)
      */
        this.renderOverlaysLong = function(now) {
            this.clearOverlays();
            //Support Preview Mode in hotspot editor
            if (hsPreviewWrapped && (now >= hsPreviewWrapped.start) && (now < hsPreviewWrapped.end )) {
                    var o = hsPreviewWrapped.createOverlay(container);
                    addOverlayShown(hsPreviewWrapped.end, o);
                    o.start();
            }

            jsonFrameSet.forEach(function(frame) {
                var objects = jsonResultObj[frame];
                for (index in objects) {
                    var hsWrapped = objects[index];
                    if (!hsPreviewWrapped || hsPreviewWrapped.id != hsWrapped.id) {
                        if ((now >= hsWrapped.start) && (now < hsWrapped.end )) {
                            var o = hsWrapped.createOverlay(container);
                            addOverlayShown(hsWrapped.end, o);
                            
                            o.start(true); /* dirty flag true. Banner should come out w/o animation */
                        }
                    }
                }
                lastAdTime = now;
            });
        }

        this.renderOverlaysDelta = function(now) {
            if (now != lastAdTime) {
                //Support Preview Mode in hotspot editor
                if (hsPreviewWrapped && hsPreviewWrapped.start == now) {
                    var o = hsPreviewWrapped.createOverlay(container);
                    addOverlayShown(hsPreviewWrapped.end, o);
                    o.start();
                }
                if (jsonFrameSet.has(now)) {
                    var objects = jsonResultObj[now];
                    for (index in objects) {
                        var hsWrapped = objects[index];
                        if (!hsPreviewWrapped || hsPreviewWrapped.id != hsWrapped.id) {
                            var o = hsWrapped.createOverlay(container);
                            preloadMainImage(hsWrapped.hsobj);
                            addOverlayShown(hsWrapped.end, o);
                            o.start();
                        }
                    }
                }
                clearOverlaysMaybe(now);
            }
            lastAdTime = now;
            if (now + 5000 != futureTime) {
                futureTime = now + 5000;
                if (jsonResultObj[futureTime])
                    preloadThumbnails(futureTime);
            }
        }
    }
    return createObj();
})(); //end of the scheduler object


    var placeRedDots = function(dotPlace, container) {
        //clear the old stuff.
        if ( container && container.hasChildNodes() ) {   
           for(var i= container.childNodes.length-1; i >= 0; i--) {
                if (container.childNodes[i].className.indexOf("vjs-marker ") != -1) {
                    container.removeChild(container.childNodes[i] ); 
                }
            }
        }
        for (var dot in dotPlace) {
                var elDot = _createElm({
                    type: 'span',
                    classes: 'vjs-marker ivs-ad-indicator'
                });
                _append(container, [elDot]);
                /* used CSS Object.keys(setting.markerStyle).forEach(function (key) {
                    elDot.style[key] = setting.markerStyle[key];
                }); */
                elDot.style.marginLeft = elDot.getBoundingClientRect().width / 2 + 'px';
                elDot.style.left = dotPlace[dot] + '%';
        }
    }

    var fixHotspotArea = function(holder) {
        //Renee comment:
        //This is to work where is the extent of the actual video
        //(coz the player aspect ratio and the video aspect ratio could be different,
        //so black bars might be inserted horiz or vert to fill the space.
        //However, the hotspot etc object's locations are as % of the top,left,width,height 
        //of the video real area - i.e. sans black bars.
        var vid = player.getDimensions();
        ivsContentAspectRatio = (vid.videoHeight > 0 ? vid.videoWidth / vid.videoHeight : 1.8);
        
        ivsPlayerAspectRatio = (vid.playerHeight > 0 ? vid.playerWidth / vid.playerHeight : 1.8);
        ivsPlayerAspectRatio = (vid.height > 0 ? vid.width / vid.height : 1.8);

        if (ivsContentAspectRatio > ivsPlayerAspectRatio) {
            var f = ivsPlayerAspectRatio / ivsContentAspectRatio;

            // Add additional class if content size less than container size
            if ( Math.round(100 * f) < 100) {
                holder.className = 'ivs-holder-4by3';
            } else {
                holder.className = '';
            }

            _setElmStyles(holder, {
               'top': Math.round(50 * (1 - f)) + '%',
               'left': '0%',
               'width': '100%',
               'height': Math.round(100 * f) + '%'
            });
        } else {
            var f = ivsContentAspectRatio / ivsPlayerAspectRatio;
            _setElmStyles(holder, {
                'left': Math.round(50 * (1 - f)) + '%',
                'top': '0%',
                'height': '100%',
                'width': Math.round(100 * f) + '%'
            });
        }
    }

    
var player;
var hsPreviewWrapped = null; //Editor preview mode
var isHotspotEditor = false;

ivsHotspotsCore = function(playerProxy0, adUnits0, isHotspotEditor0) {
    //transitional phase: don't assume the incoming info are separated info the
    //'result' and 'metainfo' (for GA) properties first. (the new change with the
    //result and metainfo properties is for hotspot GA
    if (isHotspotEditor0) {
        window.gaTrackHotspots = 0;
    }

    var adUnits; //just leave it as undefined if undefined comes from the ivideotouch.
    if (adUnits0) {
        adUnits = ('results' in adUnits0 ? adUnits0.results : adUnits0);
        if (!isHotspotEditor0 && 'metainfo' in adUnits0) 
        window.gaSetCommonInfo(adUnits0.metainfo);
    }
    
    
    isHotspotEditor = isHotspotEditor0;
     var data_ready = false;
     var player_ready = false;
     var data_results;
     var player_duration = 0;
     var player_container = null;

    var dataready = function(results) {
        data_ready = true;
        data_results = results;
        if (player_ready) {
            console.log('data ready and player also ready');
            doinit();
        }
        else {
            console.log('data ready but player not yet');
        }

    }

    var forceRenderOverlaysLong = false;
    var ivsVideoAspectRatio = 1.8;
    player = playerProxy0;
    var bigHolder = null;
    var functionsObj = {};
    var hseFunctionsObj = {};
    

     //Can only be called:
     //this is when the metadata (duration) information has become available.
     //Also, called whenever the hotspots changes (possible if editor)
     var redrawRedDots = function(previewPos) {
        var pos = _scheduler.getMarkedPercents(player_duration);
        if (previewPos !== 0)
            pos.push(previewPos);
        if (player_container) //if only got native control bar, then no place-red-dots
            placeRedDots(pos, player_container); //must have data first leh?
     }

     var doinit = function() { 
            //overlay to insert the hotspots etc    
        bigHolder = _createElm({
            type: 'div',
            id: 'ivs-real-video-area',
            styles: {
                'position': 'absolute'
            }
        });
        fixHotspotArea(bigHolder); 
        _scheduler.init(bigHolder, player, data_results);
        _initModals();
        if (player_duration) {
            redrawRedDots(0);
        }
        forceRenderOverlaysLong = true;
        player.doAddOverlay(bigHolder);
        if (player.doSubscribeVideoEvts)
            player.doSubscribeVideoEvts(); //videojs
        //For videojs we only wire up those timeupdate etc events d
        //if there are really hotspots to play.
        //For Kaltura (different work flow, this does nothing)
        //player.doFinalSetup();
        //TODO: optimize: if no such entity in the JSON, don't even init the object.
        _hotspotModal.init(bigHolder, function() { player.doPause(); }, function() {player.doPlay(); });
        _infoCardModal.init(bigHolder, function() { player.doPause(); }, function() {player.doPlay(); });
        _infoCardsDeck.init(bigHolder, function() { player.doPause(); }, function() {player.doPlay(); });
        //////////_adNetwkOverlay.init(bigHolder, player.getVideoElement()); 

          //https://stackoverflow.com/questions/11147266/javascript-onresize-event-fires-multiple-times
        /* 
         * When the video is resized, we might need to redraw some of the
         * currently visible leaderboard, infoCard etc (font issue and
         * content truncation issue)
         */
        var timeOut = null;
        _addEvent(window, "resize", function(event) {
            if (timeOut != null)
                clearTimeout(timeOut);
            timeOut = setTimeout(function(){
                timeOut = null;
                fixHotspotArea(bigHolder); 
                if (_scheduler) _scheduler.resizeOverlays();
                if (_hotspotModal) _hotspotModal.onresize();
                if (_infoCardModal) _infoCardModal.onresize();
                if (_infoCardsDeck) _infoCardsDeck.onresize();
                notifyHSEMaybe('areaChanged', player.getDimensions());
                    }, 500);
        });
    }

    /* 
     * 
     * These are functions which the videojs_interface or kaltura_interface js 
     * will call when certain player events happens:
     * 
     */
    functionsObj['playerready'] = function(duration, container) {
        player_ready = true;
        player_duration = duration;
        player_container = container;
        if (data_ready) {
            doinit();
        }            
    }
    var currFrame = 0;
    functionsObj['timeupdate'] = function(frame0) {
        var frame = Math.round(frame0)*1000; 
        currFrame = frame;
        if (forceRenderOverlaysLong) {
            forceRenderOverlaysLong = false;
            _scheduler.renderOverlaysLong(frame);
            return;
        }
        _scheduler.renderOverlaysDelta(frame);
    }

    functionsObj['seeked'] = function(frame0) {
        var frame = Math.round(frame0)*1000;
        _scheduler.clearModals();
        _scheduler.renderOverlaysLong(frame);
    }
    
    functionsObj['resumed'] = function() {
      notifyHSEMaybe('playStateChanged', {state:true});
      _scheduler.clearModals();
    }

    functionsObj['list_fav_button_clicked'] = function() {
        _infoCardsDeck.showModal();
    }

    functionsObj['paused'] = function() {
        notifyHSEMaybe('playStateChanged', {state:false});
    }

    functionsObj['adstart'] = function() {
        _scheduler.clearOverlays();
        _scheduler.clearModals();
    }

    functionsObj['adend'] = function() {
        forceRenderOverlaysLong = true; 
    }

    dataready(adUnits);
    /*****************************************************************
     * Important: 
     * Start of code to fetch the JSON DATA for all existing hotspots
     */
    //var fetchData = function(datareadyFcn) {
        /* 
         * Yudhi: this is the place you will get your JSON data.
         */
         //datareadyFcn(null);
         //Real integration:
         
        /* 
        if (isEditor) {
            //whether currently exist any hotspots for video, still MUST
            //call datareadyFcn(...)
            //to complete the initialization!!
        }
        else {
            //normal web running
            //You should find out from here whether whatever JSON returned has
            //any *actual* hotspots objects in there or not. 
            //If no and since this is running inside HOTSPOT EDITOR, then no need to call datareadyFcn(...)
            //can just stop here and not waste CPU.
         
        }*/
    //}
    //fetchData(dataready);
    //If in editor, possible fetchData gets called again (reloadAds)
    
    
    //var url is url of JSON endpoint
    /*****
    var url = something you derive from the hotspotID or whatever params you are given.

    window.getJSONHandler = function(data0) {
        if (data0["status"]=="success") {
            dataready(data0["data"]["results"]);//this data is from the include (hardcoded)
        }
    };
    var scriptEl = document.createElement('script');
    scriptEl.setAttribute('src', url + '?callback=getJSONHandler');
    document.body.appendChild(scriptEl);
    *****/        
    
    /* 
     * End of code to fetch the JSON DATA for all existing hotspots
     *
     ******************************************************************/
    


    /*
     *
     * HOTSPOT EDITOR RELATED LINES:
     *
     */

    window.hotspotsFcns = functionsObj; //important 
                                        //(this is how kaltura/vjs interface js
                                        //get to the above fcns
    

    /*  HOTSPOT EDITOR RELATED
     * 
     * These are functions which does the actual handling of commands 
     * from hotspot editor. They will be triggered thru the plumbing layer 
     * Kaltura_interface js or videojs_interface js
     * 
     */
     /* meaning of doPreview :
     It will INJECT another object to us: 
     i) could be a changed version of an existing object 
     (i.e. an obj of this id is already in our datastructure)
     ii) could be a new hotspot
     But this is just for previewing and NOT committed into the backend database.

     So doPreview:
     i) seek to the right place and will play until the hotspot's duration is over
     ii) then it will pause
     iii) possible that during this time user also use control bar etc to pause etc
     or click on the hotspot etc. or

     NOTE: If this is editing of an existing object, we will show per the obj is doPreview(obj) 
     The existing obj will be suppressed and not shown.
     Sometimes the users e.g. changes the time or creative of an existing hotspot

    The preview object is present in the renderer until there is an explicit 
    cancelPreview or doPreview(another object)

    If user creates new hotspot / updates hotspot but after pressing preview
    did not commit the change. You should call cancelPreview just before you close that
    Edit GUI for that item.
      */
    hseFunctionsObj['doPreview'] = function(obj) {
        hsPreviewWrapped = new HSWrapper(obj.start, obj);
        var startTimeMS = obj.start;
        if (startTimeMS > 2000)
            startTimeMS = startTimeMS - 2000;
        player.doSeek(Math.round(startTimeMS/1000));
        forceRenderOverlaysLong = true;

        // redraw red dot when preview button clicked
        var pos = startTimeMS * 100 / player_duration;
        redrawRedDots(pos);

        player.doPlay();

        var duration = obj.duration;
        if (startTimeMS !== 0)
            duration = obj.duration + 2000;

        // Redraw red dot when preview finish
        setTimeout( function() {
            redrawRedDots(0);
        }, duration );
    }
    hseFunctionsObj['cancelPreview'] = function(obj) { 
        hsPreviewWrapped = null;
        forceRenderOverlaysLong = true;
    }

    hseFunctionsObj['openAd'] = function(id) {
        var hsobj = _scheduler.lookupAd(id);
        if (!hsobj) return;
        var startTimeMS = hsobj.start;


        if (window.hseInKaltura) {
            player.doSeek(Math.round(startTimeMS/1000));
            notifyHSEMaybe('adOpen', hsobj);
            setTimeout( function() {
                player.doPause();
                _scheduler.clearOverlays();
                ////_scheduler.clearModals();
            }, 500);    
            return;
        }
        player.doPause();
        player.doSeek(Math.round(startTimeMS/1000));
        notifyHSEMaybe('adOpen', hsobj);
        setTimeout( function() {
            _scheduler.clearOverlays();
            ////_scheduler.clearModals();
        }, 500);
    }

    var seedObject = {
        "id": -1,
        "hse_name": '',
        "ad_type": "",
        "hse_gid": -1,
        "creativeInfo": {
            "hse_gname": "",
            //Maybe can get rid of advertiser
            "advertiser": {
                "name": ""
            },
            "url": "",
            "image_url": "",
            "thumbnail_url": "",
            "title": "",
            "description": ""
        },
        "start": 2000,
        "duration": 3000,
        "end": 5000,
        "rect": "10,0,40,40"
    };

    hseFunctionsObj['openNewAd'] = function(type) {
        seedObject.ad_type = type;
        if (type == "banner")
            seedObject.direction = "top2bottom";
        seedObject.start = currFrame;
        seedObject.end = currFrame + 3000;
        notifyHSEMaybe('adOpen', seedObject);
        player.doPause();
        setTimeout( function() {
            
            _scheduler.clearOverlays();
            ////_scheduler.clearModals();
        }, 200);
    }

    hseFunctionsObj['addAd'] = function(obj) {
        hsPreviewWrapped = null;
        _scheduler.clearOverlays();
        _scheduler.clearModals();
        _scheduler.addAd(obj);
        redrawRedDots(0);
        forceRenderOverlaysLong = true;

    }

    hseFunctionsObj['updateAd'] = function(obj) {
        hsPreviewWrapped = null;
        _scheduler.clearOverlays();
        _scheduler.clearModals();
        _scheduler.updateAd(obj);
        redrawRedDots(0);
        forceRenderOverlaysLong = true;
    }

    hseFunctionsObj['deleteAd'] = function(id) {
        hsPreviewWrapped = null;
        _scheduler.clearOverlays();
        _scheduler.clearModals();
        _scheduler.deleteAd(id);
        redrawRedDots(0);
        forceRenderOverlaysLong = true;
    }

    hseFunctionsObj['reloadAds'] = function() {
        var dataready2 = function(results) {
            hsPreviewWrapped = null;
            _scheduler.clearOverlays();
            _scheduler.clearModals();   
            _scheduler.reinit(results);
            redrawRedDots(0);
            forceRenderOverlaysLong = true;
        }
        fetchData(dataready2);
    }

    window.hotspotEditorFcns = hseFunctionsObj; //important 
                                //(this is how kaltura/vjs interface js
                                //get to the above fcns

}

window.hotspotsCore = ivsHotspotsCore; //Important
    //for interface js to get to the main entry point of this file

}(window));