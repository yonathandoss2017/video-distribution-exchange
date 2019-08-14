<div class="modal fade" id="modalDiscontinue" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog s-width" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/sp/exchange/tod.discontinue_tod') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    {{ __('manage/sp/exchange/tod.confirm_discontinue_tod') }}
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" data-delete-form-id="0" onclick="deleteAction(this, 1);">{{ __('manage/sp/exchange/tod.discontinue') }} &amp; {{ __('manage/sp/common.delete') }}</a>
                <button type="button" class="btn btn-secondary" data-delete-form-id="0" onclick="deleteAction(this, 0);">{{ __('manage/sp/exchange/tod.discontinue_only') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog s-width" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">{{ __('manage/sp/exchange/tod.delete_tod') }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div>
                    {{ __('manage/sp/exchange/tod.confirm_delete_tod') }}
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-delete-form-id="0" onclick="deleteAction(this, 2);">{{ __('manage/sp/common.delete') }}</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalDecline" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog s-width" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/sp/exchange/tod.decline_tod') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>{{ __('manage/sp/exchange/tod.confirm_decline_tod') }}</div>
            </div> 
            <div class="modal-footer">
                <a href="javascript:void(0)" data-delete-form-id="0" onclick="deleteAction(this, 2);">{{ __('manage/sp/common.delete') }}</a>
                <button type="button" class="btn btn-secondary" data-delete-form-id="0" onclick="deleteAction(this, 3);">{{ __('manage/sp/exchange/tod.decline') }}</button>
            </div>
        </div>
    </div>
</div>


@push('js')
    <script>
        function setDeleteFormId(el) {
            var todId = el.getAttribute('data-delete-form-id');
            $('div#modalDiscontinue').find('a[data-delete-form-id]').attr('data-delete-form-id', todId)
            $('div#modalDiscontinue').find('button[data-delete-form-id]').attr('data-delete-form-id', todId)
            $('div#modalDelete').find('button[data-delete-form-id]').attr('data-delete-form-id', todId)
            $('div#modalDecline').find('a[data-delete-form-id]').attr('data-delete-form-id', todId)
            $('div#modalDecline').find('button[data-delete-form-id]').attr('data-delete-form-id', todId)
        }

        function deleteAction(el, action_type) {
            var todId = el.getAttribute('data-delete-form-id');
            var theForm = $('#form_delete_'+ todId);

            $('#form_delete_'+ todId +' input[name=delete_mode]').val(action_type);
            theForm.submit();
        }
    </script>
@endpush