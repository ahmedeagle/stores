@extends('cpanel.layout.master')
@section('content')
	<div class="content">
		<div class="col-sm-12">
			<section class="page-heading">
				<div class="col-sm-6">
					<h2>قائمة الصفحات</h2>
				</div><!--End col-md-6-->
				<div class="col-sm-6">
					<ul class="breadcrumb">
						<li><a href="{{ route('home') }}">الرئيسية</a></li>
						<li>الصفحات</li>
						<li class="active">قائمة الصفحات</li>
					</ul>
				</div><!--End col-md-6-->
			</section><!--End page-heading-->
			<div class="spacer-25"></div><!--End Spacer-->
			<div class="widget">
				<div class="widget-title">
					قائمة الصفحات
				</div>
				<div class="widget-content">
					{{--<div class="col-sm-12">
						<a href="{{ route('pages.create') }}" class="custom-btn red-bc">
							<i class="fa fa-plus"></i>
							إضافة صفحة جديدة
						</a>
					</div>--}}
					<div class="spacer-25"></div><!--End Spacer-->
					@if(Session::has('success'))
						<div class="alert alert-success">
							<strong>تم بنجاح !</strong> {{ Session::get('success') }}
						</div>
						<div class="spacer-25"></div><!--End Spacer-->
					@endif
					<div class="table-responsive">
						<table id="datatable" class="table table-hover">
							<thead>
							<tr>
								<th> اسم الصفحة بالعربية</th>
								<th> اسم الصفحة بالإنجليزية</th>
								<th> حالة العرض</th>
								<th>العمليات</th>
							</tr>
							</thead>
							<tbody>
							@if($pages->count())
								@foreach($pages AS $page)
									<tr>
										<td> {{ $page->ar_title }} </td>
										<td> {{ $page->en_title }} </td>
										<td> {{ $page->active == 1 ? 'مفعلة' : 'غير مفعلة' }} </td>
										<td>
											<a href="{{ route('pages.edit', $page->id)}}"
											   class="custom-btn blue-bc">
												<i class="fa fa-pencil"></i>
											</a>
										</td>
									</tr>
								@endforeach
							@endif
							</tbody>
						</table>
					</div>
				</div>
			</div><!--End Widget-->
		</div>
	</div>
@stop
