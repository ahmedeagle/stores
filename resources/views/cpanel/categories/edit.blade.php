@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12" id="container">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>تعديل التصنيف</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li><a href="{{ route('category.show') }}">التصنفيات</a></li>
                    <li class="active">تعديل التصنيف</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div>
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-title">
                   نموذج  تعديل التصنيف
                </div>
                <div class="widget-content">
                    @if($category != NULL)
                    <form class="ui form" id="create-category" method="post" action="{{ route('category.update') }}" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="{{ $category->cat_id }}" />
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
                            المعلومات الشخصية
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                               <label>التصنيف باللغة الأنجليزية<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="en_name" id="en_name" type="text" placeholder="التصنيف باللغة الأنجليزية" value="{{ $category->cat_en_name }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>التصنيف باللغة  العربية<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="ar_name" id="ar_name" type="text" placeholder="التصنيف باللغة العربية" value="{{ $category->cat_ar_name }}" />
                                </div>
                            </div>
                        </div>
                        <div class=" ui field">
                            <label class="custom-file">
                              <input type="file" name="category_img" value="" id="category_img" class="custom-file-input">
                              <span class="custom-file-control"></span>
                            </label>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="col-sm-12">
                            <a class="img-popup-link" href="{{ env('APP_URL').'/public/categoriesImages/'.$category->cat_img }}">
                                <img src="{{ env('APP_URL').'/public/categoriesImages/'.$category->cat_img }}" class="table-img">
                            </a>    
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