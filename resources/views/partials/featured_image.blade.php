<div class="form-group row img-direct-upload{{ $errors->has('imagefile') ? ' has-danger' : '' }}" style="">
    <label class="col-md-3 control-label">{{ __('manage/sp/common.featured_image') }}</label>
    <div class="col-md-9">
        <label class="custom-file">
            <input type="file" name="imagefile" id="imagefile" class="custom-file-input" value="{{ $imagePath }}" />
            <span class="custom-file-control"></span>
            @include('partials.errors', ['err_type' => 'field','field' => 'imagefile'])
        </label>
        <div class="description">
            <small>
                @if (Request::is('*/playlists/*'))
                    * {{ __('manage/sp/common.supported_format') }}: .JPEG, .PNG<br/>
                    * {{ __('manage/sp/common.recommended_size') }}: 706 x 400 {{ __('manage/sp/common.px') }}
                @elseif(Request::is('*/videos/*'))
                    * {{ __('manage/sp/common.video_image_upload_tips') }}<br/>
                @else
                    * {{ __('manage/sp/common.supported_format') }}: .JPEG, .PNG<br/>
                    * {{ __('manage/sp/common.recommended_size') }}: 400 x 400 {{ __('manage/sp/common.px') }}
                @endif
            </small>
        </div>
        <div class="creative-image">
            <img id="preview_image" src="{{ $imageUrl }}" alt="">
        </div>
    </div>
</div>
<script type="text/javascript" src="/js/featuredimage.js"></script>
<script>
    bindImageFileChange();
</script>
