@extends('cpanel.layout.master')
@section('content')
	<div class="content">
		<div class="col-sm-12" id="container">
			<section class="page-heading">
				<div class="col-sm-6">
					<h2>إضافة صفحة</h2>
				</div><!--End col-md-6-->
				<div class="col-sm-6">
					<ul class="breadcrumb">
						<li><a href="{{ route('home') }}">الرئيسية</a></li>
						<li><a href="{{ route('pages.index') }}">الصفحات</a></li>
						<li class="active">إضافة صفحة</li>
					</ul>
				</div><!--End col-md-6-->
			</section><!--End page-heading-->
			<div class="spacer-25"></div>
			<div class="col-md-12">
				<div class="widget">
					<div class="widget-title">
						نموذج إضافة صفحة
					</div>
					<div class="widget-content">

						<form class="ui form" id="create-category" method="post"
							  action="{{ route('pages.store') }}" enctype="multipart/form-data">

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
											   placeholder="الصفحة باللغة العربية" value="{{ old('ar_title') }}"/>
									</div>
								</div>

								<div class="ui field">
									<label>عنوان الصفحة باللغة الإنجليزية<span class="require">*</span></label>
									<div class="ui input">
										<input name="en_title" id="en_title" type="text"
											   placeholder="الصفحة باللغة الأنجليزية"
											   value="{{  old('en_title') }}"/>
									</div>
								</div>

							</div>

							<div>
								<div class="ui field">
									<label>محتوى الصفحة باللغة العربية<span class="require">*</span></label>
									<div class="ui input">

										<textarea id="ar_content" name="ar_content" class="form-control" required>
											{{  old('ar_content') }}
										</textarea>

									</div>
								</div>

								<div class="ui field">
									<label>محتوى الصفحة باللغة الإنجليزية<span class="require">*</span></label>
									<div class="ui input">

										<textarea id="en_content" name="en_content" class="form-control" required>
											{{  old('en_content') }}
										</textarea>

									</div>
								</div>

								<div class="ui field">
									<label>الحالة : <span class="require">*</span></label>
									<div class="ui input">
										<select name="active">
											<option value="" {{ old('active') == '' ? 'selected' : '' }}>--- اختر الحالة
												---
											</option>
											<option value="1" {{ old('active') == 1 ? 'selected' : '' }}>مفعلة
											</option>
											<option value="0" {{ old('active') == 0 ? 'selected' : '' }}> غير
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
									إضافة
								</button>
							</div>
						</form>

					</div><!-- end widget-content -->
				</div><!-- end widget -->
			</div>
		</div><!-- end container -->
	</div>

@stop
@section('customJs')

@stop
