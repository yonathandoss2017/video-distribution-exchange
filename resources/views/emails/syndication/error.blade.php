<body>
    <style type="text/css">
        span {
            float: left;
            clear: left;
        }
    </style>
    <h2>Syndication Error</h2>
    <h3>Website : {{ $website }}</h3>
    <div>
        <span>Error Message : {{ $errMessage }}</span><br>
        <span>Platform : {{ $platformType }}</span><br>
        <span>Platform ID : {{ $platformId }}</span><br>
        <span>Entry ID : {{ $entry->id }}</span><br>
        <span>Syndication ID : {{ $feed->id }}</span><br>
        <span>Syndication : <a href="{{ $syndicationUrl }}">{{ $syndicationUrl }}</a></span>
    </div>
</body>
