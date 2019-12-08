<button type="button" class="custom-btn" data-toggle="modal" data-target="#deleteModal_{{ $id }}"
		title="حذف" style="background-color: #d9534f !important;">
	حذف
</button>

<!-- Start Delete Modal -->
<div class="modal text-left" id="deleteModal_{{ $id }}" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">تأكيد الحذف</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<h5> العنصر : </h5>
				<p>{{ $message }}</p>
				<hr>
				<h5>هل تريد بالفعل حذف العنصر ؟</h5>
			</div>
			<div class="modal-footer">

				<form id="form_{{$id}}" action="{{ $routePath }}" method="POST">

					@csrf
					@method('DELETE')

					<button type="button" class="btn btn-default"
							data-dismiss="modal">إلغاء
					</button>
					<button type="submit" class="btn btn-danger">حذف</button>

				</form>

			</div>
		</div>
	</div>
</div>
<!-- End Delete Modal -->

