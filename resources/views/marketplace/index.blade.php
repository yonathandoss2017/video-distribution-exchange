<!DOCTYPE html>
<html lang="{{ Session::get('locale') }}">
	<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/images/app/favicon.ico">
    <title>{{ __('app.title') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.params = <?php echo json_encode(['csrfToken' => csrf_token()]); ?>
    </script>
    <script src="http://qzonestyle.gtimg.cn/qzone/app/qzlike/qzopensl.js#jsdate=20111201" charset="utf-8"></script>
    <script src="https://tjs.sjs.sinajs.cn/open/api/js/wb.js" type="text/javascript" charset="utf-8"></script>
  </head>
	<body>
  	<div id="app"></div>
    <script src="{!! asset('js/marketplace/marketplace.js') !!}"></script>
	</body>
</html>