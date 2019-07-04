@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة  المدرين </h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
            <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li> 
                     <li class="active">قائمة  المدرين </li>
                </ul>
             
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
                قائمة  المدرين  
            </div>
            <div class="widget-content">
                <div class="col-sm-12">
                    <a href="{{ route('create_admin') }}" class="custom-btn red-bc">
                        <i class="fa fa-plus"></i>
                        إضافة مدير  جديد
                    </a>
                </div>
                <div class="spacer-25"></div><!--End Spacer-->
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <strong>تم بنجاح !</strong> {{ Session::get('success') }}
                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                @endif
                <div class="table-responsive">  
                    @if($admins->count())        
                    <table id="datatable" class="table table-hover">
                        <thead>
                            <tr>
                                 <th> الاسم  </th>
                                <th> البريد الألكترونى</th>
                                 <th> الحالة </th>
                                <th>  </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admins AS $admin)
                            <tr>
                                
                                <td> {{ $admin->full_name }} </td>
                                <td> {{ $admin->email }} </td>
                                 <td> {{ ($admin->publish  == 1)? 'نشط ' : 'غير نشط ' }}</td>
                                <td>
                                    <!-- <button class="custom-btn green-bc">
                                        <i class="fa fa-eye"></i>
                                    </button> -->
                                    <a href="{{route('admin.edit',$admin -> id)}}" class="custom-btn blue-bc" title="تعديل  المدير ">
                                        <i class="fa fa-pencil"></i>
                                    </a>

                                      <a href="{{route('admin.delete',$admin -> id)}}" class="custom-btn blue-bc" title="حذف ">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
 

                                    <!--
                                    <a href="#" class="custom-btn blue-bc">
                                        <i class="fa fa-cog"></i>
                                    </a> -->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div><!--End Widget-->
    </div>
</div>
@stop