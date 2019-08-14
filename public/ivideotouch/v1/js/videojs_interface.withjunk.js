/*
 * This is the videojs (ivideostream) specific parts of the 
 * renderer. Have this layer so that the core.js renderer can
 * be player agnostic.
 *
 */
(function(window, videojs) {

	//When user has favourited an ad, we need to show the list-fav
	//button (add to control bar). But we need to know where the available
	//space is
	var isCaptionsButtonVisible = function() {
		//this will affect WHERE we position our list-favourites 
		//button. That's why we need to know.
		var elt = document.getElementsByClassName('vjs-captions-button')[0];
		if (!elt) return false;
		if ((elt.classList && elt.classList.contains("vjs-hidden"))  ||
			((" " + elt.className + " " ).indexOf( " vjs-hidden " ) > -1))
				return false;
		return true; 
	}							

	/* 
	 * This is the so-called entry point of the ivideotouch videojs plugin
	 * (the last line of this file registered the plugin with videojs)
	 * The invocation is done in main.js of ivideostream
	 */
	ivsHotspots = function(hotspotsetid) {
		var player = this; //<--- this is handle to the videojs player
		var playerProxy = {};

		var setupListFavButton = function() {
			var Button = videojs.getComponent('Button');
			var ListFavButton = videojs.extend(Button, {
				constructor: function() {
    				Button.apply(this, arguments);
		    		this.addClass('list-fav');
		    		if (isCaptionsButtonVisible())
		    			this.addClass('third-from-right');
    				this.controlText("Show ads I favourited");
  				},
	  			handleClick: function() {
					if (fcnsObj['list_fav_button_clicked'])
						fcnsObj['list_fav_button_clicked']();
  				}
			});
			videojs.registerComponent('ListFavButton', ListFavButton);
			player.getChild('controlBar').addChild('ListFavButton', {}, 13);
		}

		/*
		 * playerProxy['xxxxxx']: these are
		 * Functions for the (player type agnostic) core code of renderer 
		 * to call. e.g. player.getPlayerType(), getDimensions
		 *
		 */		
		playerProxy['getPlayerType'] = function() { return 'videojs'; }

		playerProxy['getDimensions'] = function() {
			var w = parseInt(getComputedStyle(player.el()).width, 10) ||
                player.width();
			var h = parseInt(getComputedStyle(player.el()).height, 10) ||
                player.height();
   			var vid = document.getElementsByClassName('vjs-tech')[0];
   			
			//testing and info finding:
			/*
			console.log("<=========================");
			var v1 = getComputedStyle(player.el()).width;
			console.log("getComputedStyle(player.el()).width:");
			console.log(v1);

			var v2 = getComputedStyle(player.el()).height;
			console.log("getComputedStyle(player.el()).height:");
			console.log(v2);

			var v3 = player.width();
			console.log("player.width():");
			console.log(v3);

			var v4 = player.height();
			console.log("player.height():");
			console.log(v4);

			console.log(vid.videoWidth + " " + vid.videoHeight + " " + vid.offsetWidth + " " + vid.offsetHeight);
			console.log("=========================>");
			*/

   			return {
   				slideHeight : 45, /* TODO */
   				videoHeight : vid.videoHeight, 
   				videoWidth : vid.videoWidth, 
   				left : 0, //used by HSE
   				top : 0, //used by HSE
   				width : w, //used by HSE (record only)
   				height : h, //used by HSE (record only)
  				playerHeight : h, 
   				playerWidth : w
   			}
		}
		/***** seems not used:
		playerProxy['querySelector'] = function(selector) {
            var e = document.querySelector(selector);
            return e;
        }
        
		playerProxy['getElementById'] = function(id) {
            var e = document.getElementById(id);
            return e;
        }
		playerProxy['getElementByClassName'] = function(name) {
            var e = document.getElementsByClassName(name)[0];
            return e;
        }
		playerProxy['getCurrentTime'] = function() {
			return player.currentTime();			
		}
		playerProxy['getDuration'] = function() {
			return player.duration();			
		}*******/
		playerProxy['getVideoElement'] = function() {
			var f = document.getElementById('content_video_html5_api'); 
            return f;
		}
		playerProxy['doAddOverlay'] = function(elt) {
			player.el().appendChild(elt);
			return;
		}
		playerProxy['doPlay'] = function() {
			player.play();			
		}

		playerProxy['doPause'] = function() {
			player.pause();			
		}

		playerProxy['doSeek'] = function(tsSeconds) {
			player.currentTime(tsSeconds); //not tested yet
		}
		//When the core.js has gotten BOTH the player-ready call _AND_ data-ready
		//it will call this:
		//So if there is no hotspot data, then we don't do all these
		//things to waste CPU:
		playerProxy['doSubscribeVideoEvts'] = function() {
			//var f = document.getElementById('content_video_html5_api'); 
			
			//if (fcnsObj['playerready'])
			//	fcnsObj['playerready'](player.duration()*1000, elt);
			
			//When area info is available, tell to HSEditor
		 	if (window.ivtouch_fireEvent)
            	window.ivtouch_fireEvent('areaChanged', (playerProxy['getDimensions'])());

			if (fcnsObj['fullscreenchange'])
				player.on('fullscreenchange', fcnsObj['fullscreenchange']);

			if (fcnsObj['timeupdate'])
				player.on('timeupdate', function() {
					//console.log(player.currentTime());
					fcnsObj['timeupdate'](player.currentTime());
					});

			if (fcnsObj['seeked'])
				player.on('seeked', function() {
					fcnsObj['seeked'](player.currentTime());
					});

			if (fcnsObj['resumed']) 
				player.on('play', fcnsObj['resumed']);
   			
			if (fcnsObj['paused'])
				player.on('pause', fcnsObj['paused']);

			if (fcnsObj['adstart'])
				player.on('adstart', fcnsObj['adstart']);

			if (fcnsObj['adend'])
				player.on('adend', fcnsObj['adend']);
		}

		//There is reason I defer it to when it is needed.
		//1st it should not show when nothing favourited by user ..
		//also, it takes some time after video starts for the Closed
		//caption button to show up (IF ANY). So only then
		//I will know where to put our list-fav star button.
		playerProxy['doAddListFavButton'] = function() {
			setupListFavButton();
		}
		
        /*
		 *
		 * IMPORTANT 
		 * calling on core.js (injected before this js) to perform the init.
		 * core js will fetch the JSON asynchronously.
    	 *
		 */		
		var fcnsObj = window.hotspotsCore;
		fcnsObj(playerProxy, hotspotsetid, true ); //hotspotsetid == 999 ? true : false); 

		/*
		 * fcnsObj['xxxxxx']: these are
		 * Functions in core.js for this layer to call to notify events
		 * e.g. seeked, paused, playerready, fullscreenchange, timeupdate, 
		 * adstart, adend
		 *
		 */		
		fcnsObj = window.hotspotsFcns; //other entry points for this layer to call core

		player.ready(function() {
			player.one('loadedmetadata', function() {
				if (fcnsObj['playerready']) {
					var elt = document.getElementsByClassName('vjs-progress-holder')[0];
					fcnsObj['playerready'](player.duration()*1000, elt);
				}
			});
		});

		/****************************************************
		 *
		 * STUFF RELATED TO COMMUNICATIONS WITH HOTSPOT EDITOR,
		 * WHICH IS IN THE PARENT FRAME
		 * Suggestion: get a flag to know if you are running in context
		 * of editor. if not, then no need to set these functions
		 *
		 ****************************************************/
		//Called by renderer to fire events to the HSeditor, if one is running
		window.ivtouch_fireEvent = function(op0, data0) {
			window.plugin2Parent({
                handler: 'ivtouch_parentHandler',
                op: op0,
                data:  data0
            });
		}

		//Called by ivideostream to process requests from the parent window 
		//(editor) to our iframe
		window.ivtouch_pluginHandler = function(blob) {
			var op = blob.op;
			var data = blob.data;
			
			if (op == 'doPreview') {
				window.hotspotEditorFcns[op](data.obj);
			}
			else if (op == 'cancelPreview') { 
				window.hotspotEditorFcns[op]();
			}
			else if (op == 'openAd') {
				window.hotspotEditorFcns[op](data.id);
			}
			else if (op == 'openNewAd') {
				window.hotspotEditorFcns[op](data.type);
			}
			else if (op == 'deleteAd') {
				window.hotspotEditorFcns[op](data.id);
			}
			else if (op == 'addAd') {
				window.hotspotEditorFcns[op](data.obj);
			}
			else if (op == 'updateAd') {
				window.hotspotEditorFcns[op](data.obj);
			}
			else if (op == 'reloadAds') {
				window.hotspotEditorFcns[op]();
			}
			return;
		}
	}


	/*
	 * register ourself as videojs plugin ourselves
	 */
	if (videojs.registerPlugin)   //new videojs version syntax
		videojs.registerPlugin('ivideotouch', ivsHotspots);
	else 
		videojs.plugin('ivideotouch', ivsHotspots);
	
	
}(window, window.videojs));
