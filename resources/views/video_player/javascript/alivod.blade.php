<link rel="stylesheet" href="https://g.alicdn.com/de/prismplayer/2.8.1/skins/default/aliplayer-min.css" />
<link rel="stylesheet" href="/css/player.css" />
<script charset="utf-8" type="text/javascript" src="/js/aliplayer-min.js"></script>
<script>
    var lang = "{{ App::getLocale() }}" == 'zh' ? 'zh-cn' : 'en-us';
  var player = new Aliplayer({
    id: "{{ $container }}",
    height: "100%",
    width: "100%",
    preload: false,
    qualitySort:"desc",
    language: lang,
    encryptType: 1,
    autoplay: {{ $autoplay }},
    skinLayout: [
        {
            "name": "bigPlayButton",
            "align": "cc"
        },
        {
            "name": "H5Loading",
            "align": "cc"
        },
        {
            "name": "errorDisplay",
            "align": "tlabs",
            "x": 0,
            "y": 0
        },
        {
            "name": "infoDisplay"
        },
        {
            "name": "tooltip",
            "align": "blabs",
            "x": 0,
            "y": 56
        },
        {
            "name": "thumbnail"
        },
        {
            "name": "controlBar",
            "align": "blabs",
            "x": 0,
            "y": 0,
            "children": [
                {
                    "name": "progress",
                    "align": "blabs",
                    "x": 0,
                    "y": 44
                },
                {
                    "name": "playButton",
                    "align": "tl",
                    "x": 15,
                    "y": 12
                },
                {
                    "name": "timeDisplay",
                    "align": "tl",
                    "x": 10,
                    "y": 7
                },
                {
                    "name": "fullScreenButton",
                    "align": "tr",
                    "x": 10,
                    "y": 12
                },
                {
                    "name": "setting",
                    "align": "tr",
                    "x": 10,
                    "y": 12
                },
                {
                "name": "volume",
                "align": "tr",
                "x": 10,
                "y": 10
            }
            ]
        }
    ],
    vid :"{{ $videoId }}",
    playauth :"{{ $playauth }}"
  });
</script>
