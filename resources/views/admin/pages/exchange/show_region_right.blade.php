@extends('admin.layout')
@push('title') {{ __('admin/sidebar.exchange') }} | {{ __('app.title') }} @endpush
@section('content')
    <div class="row justify-content-center">

        <header class="title-header col-md-9">
            <h3 class="title">
                @if($regionRight->tod->name == 'Whitelist for own SP')
                    {{ __('manage/sp/exchange/tod.whitelist_sp') }}
                @else
                    {{ $regionRight->tod->name }}
                @endif
            </h3>
        </header>

        <div class="col-md-9">
            <p class="brdcrmb" style="float: left;"><a class="brdcrmb-item">{{ __('admin/sidebar.exchange') }}</a> <span class="brdcrmb-item">/</span><a class="brdcrmb-item">{{ __('manage/sp/exchange/tod.page_title') }}</a> <span class="brdcrmb-item">/</span><strong class="brdcrmb-item">{{ __('manage/sp/exchange/tod.regions_distribution') }}</strong></p>
            <a href="{{ route('admin.exchange.show', $regionRight->tod->id) }}" class="btn btn-normal btn-m">{{ __('manage/sp/exchange/tod.back_to_tod') }}</a>
        </div><!-- .col-* -->

        <div class="col-md-9">
            <form method="get" class="form-horizontal">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ __('manage/sp/exchange/tod.regional_rights') }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.allowed_regions') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            @if ($regionRight->allowed_regions_in_lang)
                                {{ str_replace(',', ', ', $regionRight->allowed_regions_in_lang) }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/sp/common.except') }}</label>
                        <div class="col-md-9 control-label t-a-l">{{ $regionRight->excepted_regions_in_lang ?: '-' }}</div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.availability_period') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            @if($regionRight->started_at && $regionRight->ended_at)
                                {{ optional($regionRight->started_at)->toFormattedDateString() }} - {{ optional($regionRight->ended_at)->toFormattedDateString() }}
                            @else
                                @if($regionRight->tod->sp_property_id == \App\Models\Property::ID_FOR_ADMIN)
                                    {{ __('manage/sp/exchange/tod.always_available') }}
                                @else
                                    -
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div> <!-- /.ibox -->

            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ __('manage/sp/exchange/tod.supported_distribution') }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.platforms') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            @if (str_contains($regionRight->platforms, ','))
                                @php
                                    $array_platforms = explode(',', $regionRight->platforms);
                                @endphp
                                @foreach ($array_platforms as $key => $pl)
                                    {{ count($array_platforms)-1 == $key ? __('term.platforms.'.$pl) : str_finish(__('term.platforms.'.$pl), ', ') }}
                                @endforeach
                            @elseif($regionRight->platforms)
                                {{ __('term.platforms.'.$regionRight->platforms) }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.exclusivity') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            @if (str_contains($regionRight->exclusivity, ','))
                                @php
                                    $array_exclusivity = explode(',', $regionRight->exclusivity);
                                @endphp
                                @foreach ($array_exclusivity as $key => $pl)
                                    {{ count($array_exclusivity)-1 == $key ? __('term.exclusive_property.'.$pl) : str_finish(__('term.exclusive_property.'.$pl), ', ') }}
                                @endforeach
                            @elseif($regionRight->exclusivity)
                                {{ __('term.exclusive_property.'.$regionRight->exclusivity) }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.supported_models') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            @if (str_contains($regionRight->supported_models, ','))
                                @php
                                    $array_supported_models = explode(',', $regionRight->supported_models);
                                @endphp
                                @foreach ($array_supported_models as $key => $pl)
                                    {{ count($array_supported_models)-1 == $key ? __('term.supported_models.'.$pl) : str_finish(__('term.supported_models.'.$pl), ', ') }}
                                @endforeach
                            @elseif($regionRight->supported_models)
                                {{ __('term.supported_models.'.$regionRight->supported_models) }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div> <!-- /.ibox -->

            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ __('manage/sp/exchange/tod.business_terms_to_the_cp') }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.revenue_share') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            <div class="row">
                                <div class="col-md-5">
                                    @if (!empty($regionRight->revenue_share_ex))
                                        {{ $regionRight->revenue_share_ex }}%
                                    @else
                                        -
                                    @endif
                                    <small class="m-l">{{ __('manage/sp/exchange/tod.exclusive') }}</small>
                                </div>
                                <div class="col-md-6">
                                    @if (!empty($regionRight->revenue_share_nonex))
                                        {{ $regionRight->revenue_share_nonex }}%
                                    @else
                                        -
                                    @endif
                                    <small class="m-l">{{ __('manage/sp/exchange/tod.non_exclusive') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.license_fee') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            <div class="row">
                                <div class="col-md-5">
                                    @if (!empty($regionRight->license_fee_ex))
                                        {{ $regionRight->license_fee_ex }} USD
                                    @else
                                        - USD
                                    @endif
                                    <small class="m-l">{{ __('manage/sp/exchange/tod.exclusive') }}</small>
                                </div>
                                <div class="col-md-6">
                                    @if (!empty($regionRight->license_fee_nonex))
                                        {{ $regionRight->license_fee_nonex }} USD
                                    @else
                                        - USD
                                    @endif
                                    <small class="m-l">{{ __('manage/sp/exchange/tod.non_exclusive') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.minimum_guarantee') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            <div class="row">
                                <div class="col-md-5">
                                    @if($regionRight->minimum_guarantee_ex)
                                        {{ $regionRight->minimum_guarantee_ex }} USD
                                    @else
                                        - USD
                                    @endif
                                    <small class="m-l">{{ __('manage/sp/exchange/tod.exclusive') }}</small>
                                </div>
                                <div class="col-md-6">
                                    @if($regionRight->minimum_guarantee_nonex)
                                        {{ $regionRight->minimum_guarantee_nonex }} USD
                                    @else
                                        - USD
                                    @endif
                                    <small class="m-l">{{ __('manage/sp/exchange/tod.non_exclusive') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- /.ibox -->

            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ __('manage/sp/exchange/tod.custom_terms') }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.extra_terms') }}</label>
                        <div class="col-md-9">
                            <p class="form-control-static">
                                {!! $regionRight->extra_terms ? nl2br($regionRight->extra_terms) : '-' !!}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div><!-- .row -->
@stop
