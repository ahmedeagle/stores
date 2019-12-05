@extends('cpanel.layout.master')
@section('content')
	<div class="content">
		<div class="col-sm-12" id="container">
			<section class="page-heading">
				<div class="col-sm-6">
					<h2>تعديل الصفحة</h2>
				</div><!--End col-md-6-->
				<div class="col-sm-6">
					<ul class="breadcrumb">
						<li><a href="{{ route('home') }}">الرئيسية</a></li>
						<li><a href="{{ route('pages.index') }}">الصفحات</a></li>
						<li class="active">تعديل الصفحة</li>
					</ul>
				</div><!--End col-md-6-->
			</section><!--End page-heading-->
			<div class="spacer-25"></div>
			<div class="col-md-12">
				<div class="widget">
					<div class="widget-title">
						نموذج تعديل الصفحة
					</div>
					<div class="widget-content">
						@if($page != NULL)
							<form class="ui form" id="create-category" method="post"
								  action="{{ route('pages.update') }}" enctype="multipart/form-data">
								<input type="hidden" name="id" value="{{ $page->id }}"/>
								<div class="form-title">من فضلك إملئ الحقول التالية</div>
								<div class="form-note">[ * ] حقل مطلوب</div>
								<div class="ui error message"></div>
								@if(!empty($errors->first()))
									<div class="alert alert-danger">
										<strong>خطأ!</strong> {{ $errors->first() }}
									</div>
								@endif
								@if(Session::has('success'))
									<div class="alert alert-success">
										<strong>تم بنجاح !</strong> {{ Session::get('success') }}
									</div>
								@endif
								<div class="widget-title">
									بيانات الصفحة
								</div>
								<div class="two fields">

									<div class="ui field">
										<label>عنوان الصفحة باللغة العربية<span class="require">*</span></label>
										<div class="ui input">
											<input name="ar_title" id="ar_title" type="text"
												   placeholder="الصفحة باللغة العربية" value="{{ $page->ar_title }}"/>
										</div>
									</div>

									<div class="ui field">
										<label>عنوان الصفحة باللغة الإنجليزية<span class="require">*</span></label>
										<div class="ui input">
											<input name="en_title" id="en_title" type="text"
												   placeholder="الصفحة باللغة الأنجليزية"
												   value="{{ $page->en_title }}"/>
										</div>
									</div>

								</div>

								<div>
									<div class="ui field">
										<label>محتوى الصفحة باللغة العربية<span class="require">*</span></label>
										<div class="ui input">

										<textarea id="ar_content" name="ar_content" class="form-control" required>
											{{ $page->ar_content }}
										</textarea>

										</div>
									</div>

									<div class="ui field">
										<label>محتوى الصفحة باللغة الإنجليزية<span class="require">*</span></label>
										<div class="ui input">

										<textarea id="en_content" name="en_content" class="form-control" required>
											{{ $page->en_content }}
										</textarea>

										</div>
									</div>

									<div class="ui field">
										<label>الحالة : <span class="require">*</span></label>
										<div class="ui input">
											<select name="active">
												<option value="">--- اختر الحالة ---</option>
												<option value="1" {{ $page->active == 1 ? 'selected' : '' }}>مفعلة
												</option>
												<option value="0" {{ $page->active == 0 ? 'selected' : '' }}> غير
													مفعلة
												</option>
											</select>
										</div>
									</div>

								</div>
								<div class="spacer-25"></div><!--End Spacer-->

								<div class="ui right algined inline field">
									<button type="submit" class="custom-btn">
										<i class="fa fa-plus"></i>
										تحديث
									</button>
								</div>
							</form>
						@endif
					</div><!-- end widget-content -->
				</div><!-- end widget -->
			</div>
		</div><!-- end container -->
	</div>

@stop
@section('customJs')

@stop
