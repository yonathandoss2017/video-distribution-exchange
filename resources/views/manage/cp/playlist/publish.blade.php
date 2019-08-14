@extends('partials.layout_cp')

@push('script-head')
    <link href="/css/datapicker/datepicker3.css" rel="stylesheet">
    <script src="/vendor/jquery/jquery.js"></script>
    <script src="/vendor/jquery/jquery-ui.js"></script>
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="form">
                    {{ Form::model($playlist, ['method'=>'PUT', 'url'=>route('manage.cp.playlists.update-publish',[ $property_id, $playlist->id ]), 'id' => 'playlist_form', 'class'=>'form-horizontal', 'files' => true]) }}
                        <div class="title-header ">
                            <a href="{{ route('manage.cp.playlists.index', [$property_id]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/contents/playlists.back_to_playlists') }}</a>
                            <div class="title">{{ __('common.publish') }}</div>
                        </div>
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{ __('manage/cp/contents/playlists.playlist_information') }}</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group row mb-0 {{ $errors->has('radio_publish') ? " has-danger" : "" }}">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/contents/playlists.marketplace') }}*</label>
                                    <div class="col-md-9">
                                        <label class="custom-control custom-radio">
                                            <input type="radio" name="radio_publish" value="on" class="custom-control-input" {{ (old('radio_publish') == 'on' || $playlist->publish) ? 'checked' : '' }} {{ $playlist->status == \App\Models\Playlist::STATUS_READY ? '' : 'disabled' }}>
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('manage/cp/contents/playlists.publish_to_marketplace') }}</span>
                                        </label>
                                        <label class="custom-control custom-radio">
                                            <input type="radio" name="radio_publish" value="off" class="custom-control-input" {{ (old('radio_publish') == 'off' || (!@$playlist->publish)) ? 'checked' : '' }}>
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('manage/cp/contents/playlists.don\'t_Publish') }}</span>
                                        </label>
                                        @include('partials.errors', ['err_type' => 'field','field' => 'radio_publish'])
                                    </div>
                                </div>

                                <div id="marketplace-publish" style="margin-top: 20px; {{ old('radio_publish') == 'on' || (!old('radio_publish') && !empty($playlist) && $playlist->publish) ? '' : 'display: none;' }}">
                                    <div class="form-group row {{ $errors->has('marketplace_terms') ? " has-danger" : "" }}">
                                        <label class="col-md-3 control-label">{{ __('manage/cp/contents/playlists.marketplace_terms') }}*</label>
                                        <div class="col-md-9">
                                            <select name="marketplace_terms" class='form-control' required id="marketplace-terms">
                                                <option value="">{{ __('manage/cp/contents/playlists.select_a_marketplace_terms') }}</option>
                                                @foreach($terms as $id => $name)<option value="{{ $id }}" {{ $playlist->tom_id == $id ? 'selected' : '' }}>{{ $name }}</option>@endforeach
                                            </select>
                                            @include('partials.errors', ['err_type' => 'field','field' => 'marketplace_terms'])
                                        </div>
                                    </div>

                                    <div class="form-group row {{ $errors->has('publish_start_date') ? " has-danger" : "" }}">
                                        <label class="col-md-3 control-label">{{ __('manage/cp/contents/playlists.start_date') }}*</label>
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col-md-5 release-date-calendar">
                                                    <div class="input-group date">
                                                        {{-- this field used for validation --}}
                                                        <input type="hidden" name="now_date" value="{{ date('Y-m-d') }}">

                                                        @php
                                                        $publish_start_date_value = (!empty($playlist) && $playlist->publish_start_date) ? date('m/d/Y', strtotime($playlist->publish_start_date)) : null;
                                                        @endphp
                                                        {!! Form::text('publish_start_date', $publish_start_date_value, ['id' => 'period-start-date', 'class' => 'form-control']) !!}
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-7">
                                                    <label class="custom-control custom-checkbox">
                                                        {!! Form::checkbox('available_now',
                                                            1,
                                                            old('available_now', is_null($publish_start_date_value) ? true : false),
                                                            ['id' => 'available-now', 'class' => 'custom-control-input']) !!}
                                                        <span class="custom-control-indicator"></span>
                                                        <span class="custom-control-description">{{ __('manage/cp/contents/playlists.available_now') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <label class="col-md-3 control-label"></label>
                                        <div class="col-md-4">@include('partials.errors', ['err_type' => 'field','field' => 'publish_start_date'])</div>
                                    </div>

                                    <div class="form-group row {{ $errors->has('publish_end_date') ? " has-danger" : "" }}">
                                        <label class="col-md-3 control-label">{{ __('manage/cp/contents/playlists.end_date') }}*</label>
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col-md-5 release-date-calendar">
                                                    <div class="input-group date">
                                                        @php
                                                        $publish_end_date_value = (!empty($playlist) && $playlist->publish_end_date) ? date('m/d/Y', strtotime($playlist->publish_end_date)) : null;
                                                        @endphp
                                                        {!! Form::text('publish_end_date', $publish_end_date_value, ['id' => 'period-end-date', 'class' => 'form-control']) !!}
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-7">
                                                    <label class="custom-control custom-checkbox">
                                                        {!! Form::checkbox('until_forever',
                                                            1,
                                                            old('until_forever', is_null($publish_end_date_value) ? true : false),
                                                            ['id' => 'until-forever', 'class' => 'custom-control-input']) !!}
                                                        <span class="custom-control-indicator"></span>
                                                        <span class="custom-control-description">{{ __('manage/cp/contents/playlists.until_forever') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <label class="col-md-3 control-label"></label>
                                        <div class="col-md-4">@include('partials.errors', ['err_type' => 'field','field' => 'publish_end_date'])</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input name="is_submit" type="hidden" value="">
                        <div class="form-save">
                            <button type="button" class="btn btn-primary" onclick="saveForm();">{{ __('common.save') }}</button>
                            <button type="button" class="btn btn-primary" onclick="submitForm()" id="save-submit-btn" style="{{ 0 == $playlist->publish || 'off' == old('radio_publish') ? 'display: none;' : '' }}">{{ __('common.save_and_submit') }}</button>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script src="/js/datapicker/bootstrap-datepicker.js"></script>
    <script>
        function saveForm() {
            $("input[name='is_submit']").val('');

            var radio_publish_val = $('input[name=radio_publish]:checked').val();
            if ('on' == radio_publish_val) {
                if (!$('#marketplace-terms').val()) {
                    alert("{{ __('manage/cp/contents/playlists.select_a_marketplace_terms') }}");
                    return false;
                }
            } else {
                $('#marketplace-terms').remove();
            }

            $( "#playlist_form" ).submit();
        }

        function submitForm() {
            $("input[name='is_submit']").val(true)
            
            if (!$('#marketplace-terms').val()) {
                alert("{{ __('manage/cp/contents/playlists.select_a_marketplace_terms') }}");
                return false;
            }

            $( "#playlist_form" ).submit();
        }
        
        $(document).ready(function() {
            $('.input-group.date input[type=text]').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true
            });

            $('input[type=radio][name=radio_publish]').change(function() {
                if($(this).val() == 'on') {
                    $('#marketplace-publish').show();
                    $('#save-submit-btn').show();
                    $('input[name=publish_start_date]').attr('required', 1)
                    $('input[name=publish_end_date]').attr('required', 1)
                    $('select[name=marketplace_terms]').attr('required', true)
                }
                else {
                    $('#marketplace-publish').hide();
                    $('#save-submit-btn').hide();
                    $('input[name=publish_start_date]').removeAttr('required')
                    $('input[name=publish_end_date]').removeAttr('required')
                    $('select[name=marketplace_terms]').removeAttr('required')
                }
            });
        });

        $(function(){
            $('[data-toggle="tooltip"]').tooltip()

            $('#available-now').on('change', function () {
                if($(this).is(':checked')){
                    $('#period-start-date').attr('disabled', true);
                } else {
                    $('#period-start-date').attr('disabled', false);
                }
            });
            $('#available-now').trigger('change')

            $('#until-forever').on('change', function() {
                if($(this).is(':checked')){
                    $('#period-end-date').attr('disabled', true);
                } else {
                    $('#period-end-date').attr('disabled', false);
                }
            });
            $('#until-forever').trigger('change')
        });
    </script>
@endpush