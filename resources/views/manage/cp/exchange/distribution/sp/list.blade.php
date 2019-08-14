<div class="ibox-content">
    <div class="playlist-list">
        @if(sizeof($sps) != 0)
        <table class="table">
            <tbody>
                @foreach($sps as $sp)
                <tr>
                    <td class="playlist-title pl">
                        <div>
                            {{ $sp->organization->name }}
                        </div>
                    </td>
                    <td class="playlist-title pt">
                        <div>{{ $sp->name }} <small class="label sp-wp">{{ __('manage/cp/exchange/distribution.sp') }}</small></div>
                    </td>
                    <td>
                        <a href="{{ route('manage.cp.exchange.distribution.sp.select', [$property_id, $id, $sp->id]) }}" class="btn btn-normal btn-m">{{ __('common.select') }}</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            {{ __('manage/cp/exchange/distribution.no_matches_found') }}
        @endif
    </div>
</div>
