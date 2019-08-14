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
                @if($platforms && $platforms->id > 0)
                    {{ Form::open(['method'=>'PUT', 'url'=>route('manage.cp.oauths.update.alioss', [$property_id, $platforms->id]), 'class'=>'form-horizontal']) }}
                @else
				    {{ Form::open(['method'=>'POST', 'url'=>route('manage.cp.oauths.store.alioss', [$property_id]), 'class'=>'form-horizontal']) }}
                @endif
					<div class="ibox">
						<div class="ibox-title">
							<h5>{{ __('manage/cp/sources/oauth_settings.connect_to_oss') }}</h5>
						</div>
						<div class="ibox-content">
							<div class="form-group row">
								<label class="col-md-3 control-label">{{ __('manage/cp/sources/oauth_settings.oss_api_key') }}*<i class="fa fa-info-circle tp" data-toggle="tooltip" title="" data-original-title="<img src='/images/ali-oss-accesskey.png'/>"></i></label>
								<div class="col-md-9 {{ $errors->has('api_key') ? " has-danger" : "" }}">
									<input class="form-control{{ $errors->has('api_key') ? " form-control-danger" : "" }}" required="true" name="api_key" type="text" value="{{ old('api_key') ?? (empty($platforms) ? '' : $platforms->api_key) }}">
									@include('partials.errors', ['err_type' => 'field','field' => 'api_key'])
								</div>
							</div>
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/cp/sources/oauth_settings.oss_api_secret') }}*<i class="fa fa-info-circle tp" data-toggle="tooltip" title="" data-original-title="<img src='/images/ali-oss-keysecret.png'/>"></i></label>
                                <div class="col-md-9 {{ $errors->has('api_secret') ? " has-danger" : "" }}">
                                    <input class="form-control{{ $errors->has('api_secret') ? " form-control-danger" : "" }}" required="true" name="api_secret" type="text" value="{{ old('api_secret') ?? (empty($platforms) ? '' : $platforms->api_secret) }}">
                                    @include('partials.errors', ['err_type' => 'field','field' => 'api_secret'])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 control-label" id="endpoint-tooltip">{{ __('manage/cp/sources/oauth_settings.oss_outer_endpoint') }}*<i class="fa fa-info-circle tp" data-toggle="tooltip" title="" data-original-title="<img src='/images/ali-oss-outer-endpoint.png'/>"></i></label>
                                <div class="col-md-9 {{ $errors->has('oss_outer_endpoint') ? " has-danger" : "" }}">
                                    <input class="form-control{{ $errors->has('oss_outer_endpoint') ? " form-control-danger" : "" }}" required="true" name="oss_outer_endpoint" type="text" value="{{ old('oss_outer_endpoint') ?? (empty($platforms) ? '' : $platforms->oss_outer_endpoint) }}">
                                    @include('partials.errors', ['err_type' => 'field','field' => 'oss_outer_endpoint'])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 control-label" id="endpoint-tooltip">{{ __('manage/cp/sources/oauth_settings.oss_internal_endpoint') }}*<i class="fa fa-info-circle tp" data-toggle="tooltip" title="" data-original-title="<img src='/images/ali-oss-internal-endpoint.png'/>"></i></label>
                                <div class="col-md-9 {{ $errors->has('oss_internal_endpoint') ? " has-danger" : "" }}">
                                    <input class="form-control{{ $errors->has('oss_internal_endpoint') ? " form-control-danger" : "" }}" required="true" name="oss_internal_endpoint" type="text" value="{{ old('oss_internal_endpoint') ?? (empty($platforms) ? '' : $platforms->oss_internal_endpoint) }}">
                                    @include('partials.errors', ['err_type' => 'field','field' => 'oss_internal_endpoint'])
                                </div>
                            </div>
						</div>
					</div>
					<div class="form-save">
				 		<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
				 	</div>
			 	{{ Form::close() }}
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
  </script>
@endpush
