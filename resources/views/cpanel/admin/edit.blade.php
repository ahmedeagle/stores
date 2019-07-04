@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>تعديل بيانات  مدير </h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li> 
                    <li><a href="{{ route('admins.show') }}">المدرين </a></li> 
                    <li class="active">تعديل بيانات مدير </li> 
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
    </div>
    <div class="spacer-25"></div>
    
    <div class="col-md-12">
        <div class="profile-content profile-account-setting">
            <div class="profile-widget-title pattern-bg">
                نموذج تعديل بيانات  المدير 
            </div><!--End profile-widget-title-->
            <div class="profile-widget-content">
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <strong>تم بنجاح !</strong> {{ Session::get('success') }}
                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                @endif

                @if(Session::has('errors'))
                    <div class="alert alert-danger">
                        <strong>خطأ !</strong> {{ Session::get('errors') }}
                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                @endif
                @if($admin != NULL)
                <form id="admin-form" class="form ui" method="post" action="{{ route('update_admin') }}">
                    <input type="hidden" value="{{ $admin-> id }}" name="id" />
                    <div class="form-title">من فضلك إملى الحقول التالية</div>
                    <div class="form-note">[ <span class="require">*</span> ] حقل مطلوب</div>
                    <div class="ui error message"></div>
                    <!-- <div class="form-group"> -->
                    <div class="ui field">
                        <label>
                            الأسم بالكامل :<span class="require">*</span>
                        </label><!--End label-->
                        <div class="ui input">
                            <input class="form-control" type="text" value="{{ (!empty(old('full_name')))? old('full_name') : $admin->full_name }}" name="full_name">
                        </div>
                    </div><!--End form-group-->
                    <div class="ui field">
                        <label>
                           البريد الألكترونى : <span class="require">*</span>
                        </label><!--End label-->
                        <div class="ui input">
                            <input class="form-control" type="email" value="{{ (!empty(old('email')))? old('email') : $admin->email }}" name="email">
                        </div>
                    </div><!--End form-group-->
                    
                   
                    <span class="spacer-20"></span>
                    <hr>
                    <div class="spacer-20"></div>
                    <div class="ui field">
                        <label>
                           كلمة المرور :  <span class="require">*</span>
                        </label><!--End label-->
                        <div class="ui input">
                            <input class="form-control" type="text" value="{{ old('password') }}" name="password">
                        </div>
                    </div><!--End form-group-->
                    <div class="ui field">
                        <label>
                           تأكيد كلمة المرور :
                        </label><!--End label-->
                        <div class="ui input">
                            <input class="form-control" type="text" value="{{ old('password_confirmation') }}" name="password_confirmation">
                        </div>
                    </div><!--End form-group-->


                       <div class="ui field">
                        <label>
                            الحالة  :  <span class="require">*</span>
                        </label>
                        <div class="ui input">
                            <select   name="publish">
                                <option value="">إختار  حاله  : </option>
                               
                                  <option value="1" {{$admin-> publish == 1 ? 'selected' : '' }}>مفعل 
                                  </option>

                                  <option value="0" {{$admin-> publish == 0 ? 'selected' : '' }}> غير مفعل  
                                  </option>
                                   
                            </select>
                        </div>
                    </div><!--End form-widget-->

                    <hr>
                    <div class="spacer-20"></div>
                    <div class="ui form">
                        <button type="submit" class="custom-btn">
                            <i class="fa fa-save"></i>
                           تعديل
                        </button>
                    </div>
                </form><!-- End form-->
                @endif
            </div><!--profile-->                                                            
        </div>
    </div><!--End col-md-9-->
</div><!--End Content-->
@stop
 