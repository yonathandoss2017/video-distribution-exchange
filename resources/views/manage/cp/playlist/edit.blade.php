@extends('partials.layout_cp')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="form">
                    {{ Form::model($playlist, ['method'=>'PUT', 'url'=>route('manage.cp.playlists.update',[ $property_id, $playlist->id ]), 'id' => 'playlist_form', 'class'=>'form-horizontal', 'files' => true]) }}
                        <div class="title-header ">
                            <a href="{{ route('manage.cp.playlists.index', [$property_id]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/contents/playlists.back_to_playlists') }}</a>
                            <div class="title">{{ __('manage/cp/contents/playlists.edit_playlist') }}</div>
                        </div>
                        @include('manage.cp.playlist._form')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
