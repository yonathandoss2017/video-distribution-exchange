/*
 * The layer to define the rendererMgr and rendererMgr for editor to interact 
 * with the renderer.
 * This layer differs for Kaltura (no special js file) and ivideostream
 * For ivideostream a bit more stuff here since the video lives in an 
 * iframe
 */
(function(win,doc){

    var videoReadyCB = null;
    var areaChangedCB = null;
    var playStateChangedCB = null;
    var adClickedCB = null;
    var adOpenCB = null;

    window.rendererMgr = new Object();
    var events = new Object();

    rendererMgr.doSeek = function (frame) {
        window.player.seekTo(frame/1000);
    }
    rendererMgr.getPlayhead = function() {
        return Math.round(window.player.getCurrentTime())*1000;
    }
    rendererMgr.getDuration = function() {
        return Math.round(window.player.getDuration())*1000;
    }
    rendererMgr.doPause = function () {
        window.player.pause();
    }
    rendererMgr.doPlay = function () {
        window.player.play();
    }
    rendererMgr.registerAreaChangedCB = function(cb) {
        if (cb)
        events['areaChanged'] = cb;
    }
    rendererMgr.registerPlayStateChangedCB = function(cb) {
        if (cb)
        events['playStateChanged'] = cb;
    }
    rendererMgr.registerAdClickedCB = function(cb) {
        if (cb)
        events['adClicked'] = cb;
    }
    rendererMgr.registerAdOpenCB = function(cb) {
        if (cb)
        events['adOpen'] = cb;
    }
    rendererMgr.registerVideoReadyCB = function(cb) {
        if (cb)
        events['videoReady'] = cb;
    }
    rendererMgr.doPreview = function(obj0) {
        window.player.parent2Plugin({
            handler : 'ivtouch_pluginHandler',
            op : 'doPreview',
            data: {
                obj : obj0
            }
        });
    }
    
    rendererMgr.cancelPreview = function(obj0) {
        window.player.parent2Plugin({
            handler : 'ivtouch_pluginHandler',
            op : 'cancelPreview',
            data: {
            }
        });
    }
    
    rendererMgr.openAd = function(id0) {
        window.player.parent2Plugin({
            handler : 'ivtouch_pluginHandler',
            op : 'openAd',
            data: {
               id : id0
            }
        });
    }

    rendererMgr.openNewAd = function(type0) {
        window.player.parent2Plugin({
            handler : 'ivtouch_pluginHandler',
            op : 'openNewAd',
            data: {
               type : type0
            }
        });
    }
    rendererMgr.deleteAd = function(id0) {
        window.player.parent2Plugin({
            handler : 'ivtouch_pluginHandler',
            op : 'deleteAd',
                data: {
                id : id0
            }
        });
    }

    rendererMgr.addAd = function(obj0) {
        window.player.parent2Plugin({
            handler : 'ivtouch_pluginHandler',
            op : 'addAd',
            data: {
                obj : obj0
            }
        });
    }

    rendererMgr.updateAd = function(obj0) {
        window.player.parent2Plugin({
            handler : 'ivtouch_pluginHandler',
                op : "updateAd",
                data: {
                    obj : obj0
                }
        });
    }   

    rendererMgr.reloadAds = function() {
        window.player.parent2Plugin({
            handler : 'ivtouch_pluginHandler',
            op : "reloadAds",
            data: {
            }
        });
    }   

    window.ivtouch_parentHandler = function(blob) {
        var type = blob.op;
        var data = blob.data;
        if (events[type])
            (events[type])(data);
        }

    var clickedObj;
    function cloneIt(o) {
        return ( $.extend(true, {}, o) );
    }

    
    if (window.rendererMgr) {
        /*
        window.rendererMgr.registerAdClickedCB(function(obj) {
        
        clickedObj = obj;
        console.log("fake editor got ad clicked ");
        console.log(obj);
        return;
        clickedObj = cloneIt(clickedObj);
        clickedObj.hse_name = 'changed changed lah by me';
        clickedObj.rect = "5,70,15,27";
        clickedObj.image_url = "../../ivideotouch/v1/demodata/shades.jpg",
        clickedObj.id = 1234;
        clickedObj.start = 4000;
        clickedObj.duration = 5000;
        clickedObj.end = 9000;
        rendererMgr.doPreview(clickedObj);
        return;

        window.rendererMgr.deleteAd(3);
        var newnewObj = cloneIt(clickedObj);
        newnewObj.id = 2222;
      });*/
      //setTimeout(function() {
        //window.rendererMgr.openNewAd("hotspot");}, 8000);

    }
})(window,document);