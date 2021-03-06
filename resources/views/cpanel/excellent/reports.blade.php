
@extends('cpanel.layout.master')

@section('customCss')
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
 @stop

@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة  طلبات التمييز  </h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                     <li class="active">قائمة    طلبات التمييز  </li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة   طلبات التمييز  
            </div>
            <div class="widget-content">
                <div class="col-sm-12">
                   <div class="row" style="padding: 50px;margin: -50px auto 0 auto; width: 870px">
                        <form method="GET" action="{{route('excellent.reports')}}">
                          <div class="col-md-3" style="width: 150px">
                        <select name="status">
                           <option value=""> كل  الطلبات   </option>
                          
                          <option value="pending">الجديده </option>
                          <option value="approved">الموافق عليها </option>
                          <option value="canceled">الملغاه و المنتهيه  </option>
                          <option value="unpublished">الموقوفه </option>
                           <option value="published">المنشوره </option>
                        </select>
                        </div>
                        <div class="col-md-3" style="width: 200px">
                          <input class="datepicker form-control" type="text" value="{{ $request -> from  }}" name="from" placeholder="تاريخ البدايه كــ  Y-M-D"  >
                        </div>
                        <div class="col-md-3" style="width: 200px">
                          <input class="datepicker form-control" type="text" value="{{ $request->to }}" name="to" placeholder="تاريخ النهايه كــ  Y-M-D" >
                        </div>
                        <div class="col-md-3">
                          <button class="btn btn-success" type="submit"><i class="fa fa-search"> بحث</i></button>
                        </div>
                          </form>
                        </div>



                </div>
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


                @if(Session::has('errors'))
                    <div class="alert alert-danger">
                         {{ Session::get('errors') }}
                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                @endif


                <div class="table-responsive">          
                    <table id="datatable" class="table table-hover">
                        <thead>
                            <tr>
                            	<th>  رقم  الطلب   </th>
                                 <th>إسم   الطلب   </th>
                                <th>مدينه الطلب  </th>
                                 <th>تاريخ بدا  الطلب </th>
                                <th> تاريخ انتهاء  الطلب   </th>
                                 <th> المتجر  </th>
                                 <th> قيمه  الطلب    </th>
                                  <th>  حاله  الطلب   </th>
                             </tr>
                        </thead>
                        <tbody>
                            @if(isset($providerRequests) && $providerRequests ->count() > 0)
                                @foreach($providerRequests AS $providerRequest)
                                    <tr>
                                        <td> {{ $providerRequest -> request_id}} </td>
                                         <td> {{ $providerRequest-> title }} </td>
                                        <td> {{ $providerRequest-> category_name}} </td>
                                         <td> {{ $providerRequest->start_date }} </td>
                                        <td> {{ $providerRequest->end_date }} </td>
                                        <td>  <a title="عرض " href="{{ route('provider.edit',$providerRequest -> provider_id) }}"> {{ $providerRequest->store_name }} </a> </td>
                                        
                                        <td>{{$providerRequest -> paid  == '1' ? $providerRequest ->  paid_amount : '----'}}</td>
                                        <td>

                           @php
                                                     
                               if($providerRequest-> status == '0'){
                                   echo 'جديد ';
                                   }
                             elseif ($providerRequest-> status == '1') {
                                   echo 'موافق عليه ';
                                }elseif ($providerRequest-> status == '3') {
                                  echo 'ملغي او منتهي ';
                                }elseif ($providerRequest -> publish == '0' && $providerRequest -> paid=='1') {
                                 echo 'موقوف ';
                                }elseif ($providerRequest -> publish == '1' && $providerRequest -> paid=='1' ) {
                                  echo 'منشور ';
                                }
                                


                              @endphp
                                              

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


@section('customJs')

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<script type="text/javascript">
	 
     $( function() {
    $( ".datepicker" ).datepicker();
  } );


     $( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });



</script>
@stop