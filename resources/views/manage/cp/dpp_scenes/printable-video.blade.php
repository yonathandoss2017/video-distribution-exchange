@extends('partials.pdf.layout_pdf')
@section('main')
  <div class="row  summary-box">
    <div class="col-12 summary-data">
      <div class="half-size">
        <div class="summary-box__wrap">
          <div class="summary-box__title">
            {{ __('manage/cp/ivideoadd/ivideoadd_scene.single_summary') }}
          </div>
          <div class="summary-box__content  summary__top">
            <table class="summary-box__table">
              <tr>
                <td>{{ __('manage/cp/ivideoadd/ivideoadd_scene.summary_total_scene') }}</td>
                <td class="number">{{ $scenes['total'] }}</td>
                <td class="unit">{{ __('manage/cp/ivideoadd/ivideoadd_scene.unit_scene') }}</td>
              </tr>
              <tr>
                <td>{{ __('manage/cp/ivideoadd/ivideoadd_scene.total_dpp_duration') }}</td>
                <td class="number">{{ $scenes['total_dpp'] }}</td>
                <td class="unit">{{ __('manage/cp/ivideoadd/ivideoadd_scene.unit_scene') }}</td>
              </tr>
            </table>
          </div>
        </div><!-- .summary-box__content -->

        <div class="summary-box__wrap">
          <div class="summary-box__title">
          {{ __('manage/cp/ivideoadd/ivideoadd_scene.valuation_analysis') }}
          </div>
          <div class="summary-box__content">
            <table class="summary-box__table">
              <tr>
                <td>{{ __('manage/cp/ivideoadd/ivideoadd_scene.summary_total_valuation') }}</td>
                <td class="number">N/A</td>
                <td class="unit">&nbsp;</td>
              </tr>
            </table>
          </div>
        </div><!-- .summary-box__content -->

      </div><!-- .col -->

      <div class="half-size">

        <div class="summary-box__wrap">
          <div class="summary-box__title">
            <table class="table-title">
              <thead>
                <tr>
                  <th>{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_type') }}</th>
                  <th>{{ __('manage/cp/ivideoadd/ivideoadd_scene.total_scenes') }}</th>
                  <th>{{ __('manage/cp/ivideoadd/ivideoadd_scene.total_duration') }} <small>({{ __('manage/cp/ivideoadd/ivideoadd_scene.seconds') }})</small></th>
                </tr>
              </thead>
            </table>
          </div>
          <div class="summary-box__content">
            <table class="table">
              @foreach($scenes['types'] as $type=>$value)
                <tr>
                  <td>{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_type_'.$type) }}</td>
                  <td class="number">{{ $value[0] }}</td>
                  <td class="number">{{ $value[1] }}</td>
                </tr>
              @endforeach
            </table>
          </div>
        </div><!-- .summary-box__content -->

      </div><!-- .half-size -->
    </div><!-- .col -->
  </div><!-- .summary-box -->

  <div class="row  table-content">
    <div class="col-12">
      <h4 class="table-content__title">{{ __('manage/cp/ivideoadd/ivideoadd_scene.summary_scene_locales_analyzed') }}</h4>

      <table class="table  table-content__datas">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_locale') }}</th>
            <th>{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_type') }}</th>
            <th>{{ __('manage/cp/ivideoadd/ivideoadd_scene.total_dpp_duration') }} <small>({{ __('manage/cp/ivideoadd/ivideoadd_scene.seconds') }})</small></th>
          </tr>
        </thead>
        <tbody>
          @foreach($res as $index => $locale)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $locale['locale'] }}</td>
              <td>{{ ucfirst($locale['type']) }}</td>
              <td class="number">{{ $locale['dpp_sum'] }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div><!-- .col -->
  </div><!-- .row -->


  <div class="row  table-content">
    <div class="col-12">
      <h4 class="table-content__title">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_locales_analyzed_results') }}</h4>

      <table class="table  table-content__videos">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene') }}</th>
            <th>{{ __('manage/cp/ivideoadd/ivideoadd_scene.name') }} / {{ __('manage/cp/ivideoadd/ivideoadd_scene.locale') }}</th>
            <th>{{ __('manage/cp/ivideoadd/ivideoadd_scene.type') }}</th>
            <th>{{ __('manage/cp/ivideoadd/ivideoadd_scene.duration') }} <small>({{ __('manage/cp/ivideoadd/ivideoadd_scene.seconds') }})</small></th>
          </tr>
        </thead>
        <tbody>
          @foreach( $scenes['scenes'] as $index => $scene)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td><img src="{{ \App\Services\Serve\ScenesImageService::getImageUrl($scene) }}" alt="" class="video__image" /></td>
              <td><span class="video__name">{{ $scene['name'] }}</span><br />
                <small class="video__locale">{{ isset($scene['locale']) ? $scene['locale'] : 'N/A' }}</small>
              </td>
              <td>{{ isset($scene['type']) ? ucfirst($scene['type']) : 'N/A' }}</td>
              <td class="number">{{ gmdate("H:i:s", $scene['dpp_duration']) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div><!-- .col -->
  </div><!-- .row -->
@stop
