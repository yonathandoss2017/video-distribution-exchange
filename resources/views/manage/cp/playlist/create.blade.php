@extends('partials.layout_cp')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="form">
                    {{ Form::open(['method'=>'POST', 'url'=>route('manage.cp.playlists.store',[ $property_id ]), 'id' => 'playlist_form', 'class'=>'form-horizontal', 'files' => true]) }}
                        <div class="title-header ">
                            <a href="{{ route('manage.cp.playlists.index', [$property_id]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/contents/playlists.back_to_playlists') }}</a>
                            <div class="title">{{ __('manage/cp/contents/playlists.new_playlist') }}</div>
                        </div>
                        @include('manage.cp.playlist._form')
                    {{ Form::close() }}
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
@stop
