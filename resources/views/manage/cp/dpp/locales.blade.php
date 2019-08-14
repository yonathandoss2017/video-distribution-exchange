<div class="playlist-list">
    <table class="table table-hover">
        <thead>
        <tr>
            <th class="number">#</th>
            <th class="playlist-title ptl">{{ __('manage/cp/ivideoadd/ivideoadd.scene_locale') }}</th>
            <th>{{ __('manage/cp/ivideoadd/ivideoadd.total_scenes') }}</th>
            <th>{{ __('manage/cp/ivideoadd/ivideoadd.total_dpp_duration') }}</th>
        </tr>
        </thead>
        <tbody>
            @foreach($locales as $index => $locale)
                <tr>
                    <td class="number">{{ $index + 1 }}</td>
                    <td class="playlist-title ptl">{{ $locale['locale'] }}
                    </td>
                    <td>{{ $locale['scenes_num'] }} {{ __('manage/cp/ivideoadd/ivideoadd.scenes') }}</td>
                    <td class="right"><span class="amount">{{ $locale['dpp_sum'] }}</span> <small>{{ __('manage/cp/ivideoadd/ivideoadd.seconds') }}</small></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="col-sm-5">
            <div class="dataTables_info">
                @if ($locales->count()>0)
                    {{
                        __(
                            'manage/cp/ivideoadd/ivideoadd.showing_from_to_locales',
                            [
                                'from'=>$locales->firstItem(),
                                'to'=>$locales->lastItem(),
                                'total'=>$locales->total()
                            ]
                        )
                    }}
                @else
                    {{ __('manage/cp/ivideoadd/ivideoadd.no_locales') }}
                @endif
            </div>
        </div>

        <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers">
                {{ $locales->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>
