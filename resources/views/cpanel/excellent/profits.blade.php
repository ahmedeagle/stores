@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة   الارباح     </h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                     <li class="active">قائمة  الارباح  </li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة   الارباح  
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
                            	<th>  رقم العرض  </th>
                                 <th>إسم   العرض  </th>
                                <th>القسم </th>
                                 <th>تاريخ  الطلب</th>
                                 <th>تاريخ  انتهاء الطلب </th>
                                  <th> المتجر  </th>
                                   <th>الحاله   </th>
                                   <th>القيمه المدفوعه   </th>
                                  <th>ايام العرض </th>
                             </tr>
                        </thead>
                        <tbody>
                            @if(isset($providerRequests) && $providerRequests ->count() > 0)
                                @foreach($providerRequests AS $providerRequest)
                                    <tr>
                                        <td> {{ $providerRequest -> request_id}} </td>
                                         <td> {{ $providerRequest-> title }} </td>
                                        <td> {{ $providerRequest->category_name}} </td>

                                         <td> {{ $providerRequest  -> start_date   }} </td>

                                         <td> {{ $providerRequest  -> end_date   }} </td>

 
                                        <td>  <a title="عرض " href="{{ route('provider.edit',$providerRequest -> provider_id) }}"> {{ $providerRequest->store_name }} </a> </td>

                                        <td> 
                                             {{$providerRequest -> status == 3 ?  'عرض منتهي ': 'عرض ساري '}}

                                         </td>
                                       <td>{{$providerRequest -> paid_amount}}</td>
                                       <td>{{$providerRequest -> days}}</td>
                                         
 
                                     </tr>
                                @endforeach
                            @endif

                             <tfoot>
                            <tr>
                              <td>الاجمالي </td>
                              <td> {{$total}}ريال </td>
                            </tr>
                          </tfoot>

                        </tbody>
                    </table>
                </div>
            </div>
        </div><!--End Widget-->
    </div>
</div>
@stop

 