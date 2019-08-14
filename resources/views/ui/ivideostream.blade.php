<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
</head>
<body>
  <style>
    .flex {
      display: flex;
    }
    .playerContainer {
      height: 600px;
      width: calc(50% - 10px);
      margin: 0 5px;
    }
  </style>

  <!-- <script type="text/javascript" src="{{ config('ivideostream.javascript_file_url') }}"></script> -->
  <script type="text/javascript" src="/ivideostream/ivstreamplay_v1.js"></script>
  <div class="flex">
    <div class="playerContainer" id="ivideostreamplayer-mp4"></div>
    <div class="playerContainer" id="ivideostreamplayer-hls"></div>
  </div>

  <script type="text/javascript">
      var videoIdMp4 = 1;
      var videoSrcMp4 = "https://vjs.zencdn.net/v/oceans.mp4?sd";
      var multiSourceMp4 = [[
          "https://vjs.zencdn.net/v/oceans.mp4?sd",//src
          "video/mp4",
          "SD",//label
          "480",//res
        ],[
          "https://vjs.zencdn.net/v/oceans.mp4?hd",
          "video/mp4",
          "HD",
          "1080"
        ],[
          "https://vjs.zencdn.net/v/oceans.mp4?4k",
          "video/mp4",
          "4k",
          "2160"
        ]]
      var videoIdHLS = 2;
      var videoSrcHLS = "https://mnmedias.api.telequebec.tv/m3u8/29880.m3u8"
      // var videoSrc ="https://localuse.oss-cn-shanghai.aliyuncs.com/1/1/795/stonetest.m3u8"

      playIvStream(videoIdMp4, videoSrcMp4, "ivideostreamplayer-mp4", multiSourceMp4);
      playIvStream(videoIdHLS, videoSrcHLS, "ivideostreamplayer-hls", null);

      function playIvStream(videoId, videoSrc, container, multiSource) {

          window.ivstreamplay({
              container: container,
              settings: {
                  videoId: videoId,
                  videoSrc: videoSrc,
                  // videoType: videoType, //非必要参数
                  vastUrl: "",
                  autoPlay: false,
                  skipOffset: 5,
                  multiResolution: multiSource ? true : false,
                  multiSource: multiSource,
              },
              onReady: function(player) {
                  window.player = player;
                  /******* Get Methode *****/
                  //player.getDuration();
                  //player.getCurrentTime();

                  /******* Set Methode *****/
                  // player.play();
                  // player.pause();
                  // player.volume() // from 0 to 1
                  // player.seekTo(num) //  num has to be in seconds

                  /******* Events *****/
                  player.on("playerStart", function() {
                      console.log("playerStart", this);
                  });
                  player.on("playerPlay", function() {
                      console.log("playerPlay", this);
                  });
                  player.on("playerPause", function() {
                      console.log("playerPause", this);
                  });
                  player.on("playerEnd", function() {
                      console.log("playerEnd", this);
                  });
              }
          });
      }
  </script>

</body>
</html>
