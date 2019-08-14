@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/exchange/distribution.internal_whitelisting') }}
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
        <div class="form">
            <div class="title-header">
                <div class="title">{{ __('manage/cp/exchange/distribution.internal_whitelisting') }}</div>
            </div>
            <form method="get" class="form-horizontal">
                <div class="ibox">
                    <div class="ibox-title title-with-button">
                        <div class="title-button-header"><h5>{{ __('manage/cp/exchange/distribution.terms_summary') }}</h5></div>
                        <a href="{{ route('manage.cp.exchange.distribution.summary.edit', [$property_id, $id]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/exchange/distribution.edit_summary') }}</a>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.internal_remarks') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $distribution->internal_remarks ?: '-' }}</div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('common.created_by') }}</label>
                            <div class="col-md-9 control-label t-a-l">
                                @if($distribution->creator)
                                    {{ $distribution->userCreator->name }} on {{ $distribution->created_at->format('M j, Y') }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('common.updated_by') }}</label>
                            <div class="col-md-9 control-label t-a-l">
                                @if($distribution->updater)
                                    {{ $distribution->userUpdater->name }} on {{ $distribution->updated_at->format('M j, Y') }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="ibox">
                    <div class="ibox-title title-with-button">
                        <h5 class="title-button-header">{{ __('manage/cp/exchange/distribution.service_providers') }}</h5>
                        <a href="{{ route('manage.cp.exchange.distribution.sp.index', [$property_id, $id]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/exchange/distribution.select_sp') }}</a>
                    </div>
                    @if($distribution->serviceProviders)
                    <div class="ibox-content pt-0">
                        <div class="playlist-list">
                            <table class="table table-hover">
                                <tbody>
                                @foreach($distribution->serviceProviders as $serviceProvider)
                                <tr>
                                    <td class="image-small">
                                        <div class="video-img video-img-small">
                                            @if($serviceProvider->logo_path)
                                                <a href="#"><img src="{{ \App\Services\Serve\PropertyImageService::getImageUrl($serviceProvider, 'logo', null, 90) }}"></a>
                                            @else
                                                <a href="#"><img src="{{ url('images/property-logo-default.png') }}"></a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="playlist-title pl"><a href="#">{{ $serviceProvider->name }}</a></td>
                                    <td class="playlist-status">{{ $serviceProvider->uuid }}</td>
                                    <td class="playlist-actions">
                                        <a href="javascript:deleteSp({{ $serviceProvider->id }});" class="btn btn-normal btn-m delete"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('manage/cp/exchange/distribution.distribution_rights') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="playlist-list">
                        <table class="table table-hover">
                            <tbody>
                            @foreach($distribution->regionRights as $regionRight)
                                <tr>
                                    <td class="playlist-title pt">
                                        <a href="#">
                                            @foreach ($regionRight->allowed_regions as $region)
                                                {{ __('region.'.$region) }} {{ $loop->last ? '' : ',' }}
                                            @endforeach
                                        </a>
                                        @if($regionRight->excepted_regions)
                                            <br>
                                            <small>{{ __('common.except') }}:
                                                @foreach ($regionRight->excepted_regions as $region)
                                                    {{ __('region.'.$region) }} {{ $loop->last ? '' : ',' }}
                                                @endforeach
                                            </small>
                                        @endif
                                    </td>
                                    <td class="playlist-status">
                                        {{ __('manage/cp/exchange/distribution.always_available') }}
                                    </td>
                                    <td class="playlist-actions pl">
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
                
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('manage/cp/exchange/distribution.content_provider_playlists') }}</h5>
                    </div>
                    <div class="ibox-content">
                        {{ __('manage/cp/exchange/distribution.include_all_playlists_from_this_cp') }}
                    </div>
                </div>
            </form>
        </div>
        </div>
    </div>
    {{ Form::open(['url' => route('manage.cp.exchange.distribution.sp.delete', ['property_id'=> $property_id, 'id'=> $distribution->id]), 'method' => 'DELETE', 'name' => "delete_sp_form"]) }}
    {{ Form::hidden('sp') }}
    {{ Form::close() }}
</div>
@stop
@push('js')
    <script>
        function deleteSp(sp)
        {
            var form = document.forms['delete_sp_form'];
            form.sp.value = sp;
            form.submit();
        }
    </script>
@endpush