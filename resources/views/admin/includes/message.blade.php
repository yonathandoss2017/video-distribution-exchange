@if (count($errors))
    <div class="alert alert-danger">
        <strong>{{ __('admin/common.oops') }}</strong> {{ __('admin/common.submit_error') }}
    </div>
@elseif (session()->exists('success'))
    <div class="alert alert-success" role="alert">
        <strong>{{ __('admin/common.nice') }}</strong> {!! Session::pull('success') !!}
    </div>
@elseif (session()->exists('warning'))
    <div class="alert alert-warning" role="alert">
        {!! Session::pull('warning') !!}
    </div>
@elseif (session()->exists('error'))
    <div class="alert alert-danger" role="alert">
        <strong>{{ __('admin/common.oops') }}</strong> {!! Session::pull('error') !!}
    </div>
@elseif (session()->exists('well_done'))
    <div class="errors p-a-30">
        <div class="alert alert-success" role="alert">
            <strong>{{ __('admin/common.well_done') }}</strong> {{ __('admin/user.successful_activate') }}
        </div>
    </div>
@elseif(session()->exists('invalid_token'))
    <div class="errors p-a-30">
        <div class="alert alert-danger" role="alert">
            <strong>{{ __('admin/common.oops') }}</strong> {{ __('admin/user.invalid_token') }}
        </div>
    </div>
@endif
