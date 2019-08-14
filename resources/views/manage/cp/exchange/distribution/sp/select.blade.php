@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/exchange/distribution.select_service_provider') }}
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="form">
                    <div class="title-header">
                        <a href="{{ route('manage.cp.exchange.distribution.edit', [$property_id, $id]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/exchange/distribution.back_to_terms_of_distribution') }}</a>
                        <div class="title">{{ __('manage/cp/exchange/distribution.select_service_provider') }}</div>
                    </div>
                    <form method="get" class="form-horizontal">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{ __('manage/cp/exchange/distribution.search_property') }} </h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group row ">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.property_type') }}</label>
                                    <div class="col-md-9">
                                        <label class="custom-control custom-radio">
                                            <input name="meta_type" id="own-sp" value="1" type="radio" class="custom-control-input" checked="">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('manage/cp/exchange/distribution.own_properties') }}</span>
                                        </label>
                                        <label class="custom-control custom-radio">
                                            <input name="meta_type" id="other-sp" value="2" type="radio" class="custom-control-input">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('manage/cp/exchange/distribution.search_other_properties') }}</span>
                                        </label>
                                    </div>
                                </div>

                                <div id="search-other-sp" class="form-group row" style="display: none;">
                                    <label class="col-md-3 control-label"></label>
                                    <div class="col-md-9">
                                        <div class="input-group" style="max-width: 390px;">
                                            <input type="text" id="search-uuid" placeholder="{{ __('manage/cp/exchange/distribution.sp_account_uuid') }}" class="input-sm form-control">
                                            <span class="input-group-btn">
									            <button type="button" id="btn-search" class="btn btn-sm btn-normal"><i class="fa fa-search" aria-hidden="true"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ibox" id="search-own-result">
                            @include('manage.cp.exchange.distribution.sp.list')
                        </div>
                        <div class="ibox hidden" id="search-other-result"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
@push('js')
    <script>
        $(document).ready(function() {
            $('input[type="radio"]').click(function() {
                if($(this).attr('id') == 'other-sp') {
                    $('#search-own-result').addClass('hidden');
                    $('#search-other-result').removeClass('hidden');
                    $('#search-other-sp').show();
                }
                else {
                    $('#search-own-result').removeClass('hidden');
                    $('#search-other-result').addClass('hidden');
                    $('#search-other-sp').hide();
                }
            });

            $('#btn-search').click(function(){
                if ($('#search-uuid').val() == '') return;
                query_sp();
            });
        });

        function query_sp(){
            $.ajax({
                type: 'GET',
                url: '{{ route('manage.cp.exchange.distribution.sp.search', [$property_id, $id]) }}',
                data: {
                    keywords : $('#search-uuid').val()
                },
                dataType: 'html',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data){
                    $('#search-other-result').html(data);
                },
                error: function(xhr, type){
                    console.error(type);
                }
            });
        }
    </script>
@endpush