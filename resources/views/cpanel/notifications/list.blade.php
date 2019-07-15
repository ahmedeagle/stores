@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة  العروض </h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                     <li class="active">قائمة  العروض </li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة  العروض 
            </div>
            <div class="widget-content">
               
                <div class="spacer-25"></div><!--End Spacer-->

              
               <div class="alert alert-success" id="alert_success" style="display: none;">
                        
                    </div>

                     <div class="alert alert-danger"  id="alert_danger" style="display: none;">
                     </div>


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
                            	<th>   ألرقم </th>
                                <th>  العنوان </th>
                                <th>النص </th>
                                <th> التارخ </th>
                                 <th>الوقت </th>
                                <th> اسم المرسل اليه </th>
                                 <th>النوع  </th>
                             </tr>
                        </thead>
                        <tbody>
                            @if(isset($notifications) && $notifications ->count() > 0)
                                @foreach($notifications AS $notify)
                                    <tr>
                                        <td> {{ $notify -> id}} </td>
                                        <td> {{ $notify-> title }} </td>
                                        <td> {{ $notify->content}} </td>
                                        <td> {{ $notify->create_date }} </td>
                                        <td> {{ $notify->create_time }} </td>
                                        <td> {{$notify -> actor_name}} </td>
                                        <td> {{$notify -> actor_type}} </td>
                                        
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

 