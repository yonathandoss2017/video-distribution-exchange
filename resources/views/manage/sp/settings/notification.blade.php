@extends('partials.layout_sp')

@push('title')
    {{ __('manage/sp/setting/notification.page_title') }} | {{ __('app.title') }}
@endpush

@section('content')
    <div id="app" class="container">
        <div class="row">
            <div class="col-md-12">
            	<div class="title-header ">
                    <div class="title">{{ __('manage/sp/setting/notification.page_title') }}</div>
                </div>

                <div class="ibox">
                	<div class="ibox-content">
                		<div class="playlist-list">
                			<table class="table table-hover">
                				<thead>
                					<tr>
                						<th class="playlist-title ptl">{{ __('manage/sp/common.name') }}</th>
                						<th class="email-width">{{ __('manage/sp/common.email') }}</th>
                						<th>{{ __('manage/sp/common.role') }}</th>
                						<th>{{ __('manage/sp/setting/notification.email_notifications') }}</th>
            						</tr>
        						</thead>
        						<tbody>
        							@foreach ($users as $user)
	        							<tr>
	        								<td class="playlist-title">
	        									<a href="#">{{ $user->name }}</a>
	        								</td>
	        								<td>{{ $user->email }}</td>
	        								<td>{{ __('admin/user.'.$user->roles->first()->name) }}</td>
	        								<td class="playlist-actions">
                                                <radio-button
                                                    value="{{ $user->isAcceptNotificationForProperty($property_id) }}"
                                                    url-update="{{ route('manage.sp.settings.update_notifications', [$property_id, $user->id]) }}"></radio-button>
	        								</td>
										</tr>
									@endforeach
								</tbody>
    						</table>
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info">{{ __('manage/sp/setting/notification.showing_from_to_users', ['from' => $users->firstItem(), 'to' => $users->lastItem(), 'total' => $users->total()]) }}</div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers">
                                        {{ $users->links() }}
                                    </div>
                                </div>
                            </div>
						</div>
					</div>
				</div> <!-- /.ibox -->
            </div>
        </div>
    </div>
@stop

@push('js')
    <script src="{{ asset('js/app.js') }}"></script>
@endpush