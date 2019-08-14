@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/exchange/distribution.manage_playlists') }}
@endpush

@section('content')
    <div class="container" id="app">
        <distribution-playlist
                inline-template
                :property="{{ $property }}"
                :tod="{{ $tod }}"
                :selected-playlists="{{ $selectedPlaylist }}"
                v-cloak
            >
            <div>
                <div class="title-header">
                    <a href="{{ route('manage.cp.exchange.distribution.edit', ['property' => $property, 'distribution' => $tod]) }}"
                       class="btn btn-normal btn-m">{{ __('manage/cp/exchange/distribution.back_to_terms_of_distribution') }}</a>
                    <div class="title">{{ __('manage/cp/exchange/distribution.manage_playlists') }}</div>
                </div>
                <div class="row is-table-row">
                    <div class="col-md-6">
                        <div class="form">
                            <div class="ibox">
                                <div class="ibox-title title-with-button playlist-search">
                                    <div class="input-group wl" style="width: 477px; margin-left: 10px;">
                                        <input type="text" placeholder="{{ __('manage/cp/exchange/distribution.search_playlists') }}" class="input-sm form-control" style="max-width: 100%;" v-model="search" v-on:keyup.enter="searchPlaylist" >
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-sm btn-normal" @click="searchPlaylist"><i class="fa fa-search" aria-hidden="true"></i></button>
                                        </span>
                                    </div>
                                </div>
                                <div class="ibox-content pt-0">
                                    <div class="video-list spwp">
                                        <table class="table tod-playlist">
                                            <tbody>
                                            <tr v-for="playlist in playlists">
                                                <td class="image-small">
                                                    <div class="video-img video-img-small">
                                                        <a :href="'/manage/' + property.id + '/cp/videos?playlist=' + playlist.id">
                                                            <img :src="'/serve/image/' + property.id + '/playlist/' + playlist.id + '/'+playlist.updated_at+'?width=300'">
                                                        </a>
                                                    </div>
                                                </td>
                                                <td class="playlist-title">
                                                    <a :href="'/manage/' + property.id + '/cp/videos?playlist=' + playlist.id">@{{ playlist.name }}</a> <br>
                                                    <small><b>@{{ playlist.entries_count }}</b> {{ __('manage/cp/exchange/distribution.videos') }}</small>
                                                </td>
                                                <td class="playlist-actions">
                                                    <button type="button" class="btn btn-normal" @click="select(''+playlist.id+'')">{{ __('manage/cp/exchange/distribution.add') }}</button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <div class="row">
                                            <div class="col-sm-5">
                                                <div class="dataTables_info">@{{ from }} {{ __('manage/cp/exchange/distribution.to') }} @{{ to }} {{ __('manage/cp/exchange/distribution.of') }} @{{ total }} {{ __('manage/cp/exchange/distribution.playlists') }}</div>
                                            </div>
                                            <div class="col-sm-7">
                                                <div class="dataTables_paginate paging_simple_numbers">
                                                    <ul class="pagination" v-if="data.links">
                                                        <!-- previous button -->
                                                        <li class="page-item" v-if="data.links.prev">
                                                            <a class="page-link" aria-label="Previous" @click="fetchData(data.links.prev)">
                                                                <span aria-hidden="true">&laquo;</span>
                                                                <span class="sr-only">{{ __('manage/cp/exchange/distribution.previous') }}</span>
                                                            </a>
                                                        </li>
                                                        <li class="page-item disabled" v-else>
                                                            <a class="page-link" aria-label="Previous">
                                                                <span aria-hidden="true">&laquo;</span>
                                                                <span class="sr-only">{{ __('manage/cp/exchange/distribution.previous') }}</span>
                                                            </a>
                                                        </li>

                                                        <li class="page-item" v-for="page in pagesNumber" :class="{'active': page == data.meta.current_page}">
                                                            <a @click="goToPage(page)" class="page-link">@{{ page }}</a>
                                                        </li>

                                                        <!-- next button -->
                                                        <li class="page-item" v-if="data.links.next">
                                                            <a class="page-link" aria-label="Next" @click="fetchData(data.links.next)">
                                                                <span aria-hidden="true">&raquo;</span>
                                                                <span class="sr-only">{{ __('manage/cp/exchange/distribution.next') }}</span>
                                                            </a>
                                                        </li>
                                                        <li class="page-item disabled" v-else>
                                                            <a class="page-link" aria-label="Next">
                                                                <span aria-hidden="true">&raquo;</span>
                                                                <span class="sr-only">{{ __('manage/cp/exchange/distribution.next') }}</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form">
                            <form method="get" class="form-horizontal">
                                <div class="ibox">
                                    <div class="ibox-title title-with-button">
                                        <div class="title-button-header"><h5>{{ __('manage/cp/exchange/distribution.selected_playlists') }}</h5></div>
                                    </div>
                                    <div class="ibox-content pt-0">
                                        <div class="video-list spwp">
                                            <div class="scrolled-v">
                                                <table class="table tod-playlist">
                                                    <tbody>
                                                    <tr v-if="selectedPlaylistData.length === 0">
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <tr v-else v-for="playlist in selectedPlaylistData">
                                                        <td class="image-small">
                                                            <div class="video-img video-img-small">
                                                                <a :href="'/manage/' + property.id + '/cp/videos?playlist=' + playlist.id">
                                                                    <img :src="'/serve/image/' + property.id + '/playlist/' + playlist.id + '/'+playlist.updated_at+'?width=300'">
                                                                </a>
                                                            </div>
                                                        </td>
                                                        <td class="playlist-title">
                                                            <a :href="'/manage/' + property.id + '/cp/videos?playlist=' + playlist.id">@{{ playlist.name }}</a> <br>
                                                            <small><b>@{{ playlist.entries_count }}</b> {{ __('manage/cp/exchange/distribution.videos') }}</small>
                                                        </td>
                                                        <td class="playlist-actions">
                                                            <button type="button" class="btn btn-normal btn-m delete" @click="removeSelected(''+playlist.id+'')"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-5">
                                                    <div class="dataTables_info">@{{ selectedPlaylistData.length }} {{ __('manage/cp/exchange/distribution.playlists_selected') }}</div>
                                                </div>
                                                <div class="col-sm-7">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="form-save">
                    <button type="button" class="btn btn-primary" @click="confirm">{{ __('manage/cp/exchange/distribution.confirm') }}</button>
                </div>
            </div>
        </distribution-playlist>
    </div>
@stop

@push('js')
    <script src="{{ asset('js/distribution-playlist.js') }}"></script>
@endpush
