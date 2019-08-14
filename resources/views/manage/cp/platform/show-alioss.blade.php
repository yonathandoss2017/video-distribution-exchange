@extends('partials.layout_cp')

@push('title')
{{ __('app.title') }} | {{ __('manage/cp/sources/oauth_settings.oss_oauth') }}
@endpush

@section('content')

<div class="container">
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
			<div class="form">
				<div class="title-header ">
					<a href="{{ route('manage.cp.oauths.index', [$property_id]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/sources/oauth_settings.back_to_oauths') }}</a>
					<div class="title">{{ __('manage/cp/sources/oauth_settings.oss_oauth') }}</div>
				</div>
					<div class="ibox">
						<div class="ibox-title">
							<h5>{{ __('manage/cp/sources/oauth_settings.connect_to_oss') }}</h5>
						</div>
						<div class="ibox-content">
							<div class="form-group row">
								<label class="col-md-3 control-label">{{ __('manage/cp/sources/oauth_settings.oss_api_key') }}<i class="fa fa-info-circle tp" data-toggle="tooltip" title="" data-original-title="<img src='/images/ali-oss-accesskey.png'/>"></i></label>
								<div class="col-md-9 control-label">
									{{ $platformOss->api_key }}
								</div>
							</div>
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/cp/sources/oauth_settings.oss_api_secret') }}<i class="fa fa-info-circle tp" data-toggle="tooltip" title="" data-original-title="<img src='/images/ali-oss-keysecret.png'/>"></i></label>
								<div class="col-md-9 control-label">
									{{ $platformOss->api_secret }}
								</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 control-label" id="endpoint-tooltip">{{ __('manage/cp/sources/oauth_settings.oss_outer_endpoint') }}<i class="fa fa-info-circle tp" data-toggle="tooltip" title="" data-original-title="<img src='/images/ali-oss-outer-endpoint.png'/>"></i></label>
                                <div class="col-md-9 control-label">
                                    {{ $platformOss->oss_outer_endpoint }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 control-label" id="endpoint-tooltip">{{ __('manage/cp/sources/oauth_settings.oss_internal_endpoint') }}<i class="fa fa-info-circle tp" data-toggle="tooltip" title="" data-original-title="<img src='/images/ali-oss-internal-endpoint.png'/>"></i></label>
								<div class="col-md-9 control-label">
									{{ $platformOss->oss_internal_endpoint }}
								</div>
							</div>
							<div class="form-group row">
								<label class="col-md-3 control-label" id="endpoint-tooltip">{{ __('manage/cp/sources/oauth_settings.notification_url') }}<i class="fa fa-info-circle tp" data-toggle="tooltip" title="" data-original-title="<img src='/images/ali-oss-notification-url.png'/>"></i></label>
								<div class="col-md-9 control-label" style="word-break: break-all;">
									{{ str_replace('https', 'http', env('APP_URL')) }}/api/ali/mns/events?api_key={{ $platformOss->contentProviderProperty->api_key }}
								</div>
							</div>
						</div>
					</div>
					<div class="form-save">
						<button type="submit" class="btn btn-primary"><a href="#" data-form-id="{{ $property_id }}_{{ $platformOss->id }}" onclick="deleteOss(this);">{{ Form::open(['url' => route('manage.cp.oauths.delete.alioss', ['property_id'=> $property_id, 'id'=> $platformOss->id ]), 'method' => 'DELETE', 'id' => $property_id . "_" . $platformOss->id]) }}{{ __('common.delete') }}{{ Form::close() }}</a></button>
				 	</div>
			</div>
		</div>
		<div class="col-md-2"></div>
	</div>

</div>

@stop

@push('js')
  <script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip({
        animated: 'fade',
        placement: 'right',
        html: true,
        boundary:'viewport',
        template:'<div class="tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner with-img"></div></div>'
      })
    })

	function deleteOss(element) {
        var formId = element.getAttribute('data-form-id');
        $('#' + formId).submit();
    }
  </script>
@endpush
