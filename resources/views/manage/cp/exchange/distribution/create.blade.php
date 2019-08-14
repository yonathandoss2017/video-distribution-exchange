@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/exchange/distribution.terms_of_distribution') }}
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="form">
                <div class="title-header">
                    <div class="title">{{ __('manage/cp/exchange/distribution.terms_of_distribution') }}</div>
                </div>
                <form method="get" class="form-horizontal">
                    <div class="ibox">
                        <div class="ibox-title title-with-button">
                            <div class="title-button-header"><h5>{{ __('manage/cp/exchange/distribution.terms_summary') }}</h5></div>
                            <a href="{{ route('manage.cp.exchange.distribution.summary.create', $property_id) }}" class="btn btn-normal btn-m">{{ __('manage/cp/exchange/distribution.edit_summary') }}</a>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('common.name') }}</label>
                                <div class="col-md-9 control-label t-a-l">-</div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('common.status') }}</label>
                                <div class="col-md-9 tod-status"><span class="label label-grey">{{ __('manage/cp/exchange/distribution.draft') }}</span></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.internal_remarks') }}</label>
                                <div class="col-md-9 control-label t-a-l">-</div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.contract') }}</label>
                                <div class="col-md-9 control-label t-a-l">-</div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('common.created_by') }}</label>
                                <div class="col-md-9 control-label t-a-l">-</div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('common.updated_by') }}</label>
                                <div class="col-md-9 control-label t-a-l">-</div>
                            </div>
                        </div>
                    </div>

                    <div class="ibox">
                        <div class="ibox-title title-with-button">
                            <div class="title-button-header"><h5>{{ __('manage/cp/exchange/distribution.service_provider') }}</h5></div>
                            <a href="#" class="btn btn-normal btn-m disabled" style="background-color: #e1e1e1;">{{ __('manage/cp/exchange/distribution.select_sp') }}</a>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.service_provider') }}</label>
                                <div class="col-md-9 control-label t-a-l">-</div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.sp_account_uuid') }}</label>
                                <div class="col-md-9 control-label t-a-l">-</div>
                            </div>
                        </div>
                    </div>

                    <div class="ibox">
                        <div class="ibox-title title-with-button">
                            <div class="title-button-header"><h5>{{ __('manage/cp/exchange/distribution.distribution_rights') }}</h5></div>
                            <a href="#" class="btn btn-normal btn-m disabled" style="background-color: #e1e1e1;">{{ __('common.edit') }}</a>
                        </div>
                        <div class="ibox-content">
                            {{ __('manage/cp/exchange/distribution.no_region_added') }}
                        </div>
                    </div>

                    <div class="ibox">
                        <div class="ibox-title title-with-button">
                            <div class="title-button-header"><h5>{{ __('manage/cp/exchange/distribution.content_provider_playlists') }}</h5></div>
                            <a href="#" class="btn btn-normal btn-m disabled" style="background-color: #e1e1e1;">{{ __('manage/cp/exchange/distribution.manage_playlists') }}</a>
                        </div>
                        <div class="ibox-content">
                            <span>{{ __('manage/cp/exchange/distribution.no_playlist_selected') }}</span><br><br>
                            <small>{{ __('manage/cp/exchange/distribution.most_recent_terms_of_distribution') }}</small>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
