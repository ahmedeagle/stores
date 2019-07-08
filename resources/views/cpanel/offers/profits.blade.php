@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>الارباح </h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                     <li class="active">الارباح  </li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة  الارباح          </div>
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
                                <th> صوره العرض </th>
                                <th>إسم   العرض  </th>
                                <th>مدينه العرض </th>
                                 <th>تاريخ بدا العرض</th>
                                <th> تاريخ انتهاء العرض  </th>
                                 <th> المتجر  </th>
                                <th>القيمه المدفوعه </th>
                                <th> الحالة </th>
                                <th>ايام العرض </th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($offers) && $offers ->count() > 0)
                                @foreach($offers AS $offer)
                                    <tr>
                                        <td> {{ $offer -> offer_id}} </td>
                                        <td> <img style="width: 60px;height: 60px;" src="{{ $offer ->offer_photo }}"> </td>
                                        <td> {{ $offer-> offer_title }} </td>
                                        <td> {{ $offer->city_name}} </td>
                                         <td> {{ $offer->start_date }} </td>
                                        <td> {{ $offer->end_date }} </td>
                                        <td>  <a title="عرض " href="{{ route('provider.edit',$offer -> provider_id) }}"> {{ $offer->store_name }} </a> </td>
                                        
                                         <td>

                                           {{ $offer -> paid_amount }}
             
                                        </td>

                                        <td>
                                           
                                          {{$offer -> expire == 1 ?  'عرض منتهي ': 'عرض ساري '}}

                                        </td>

                                        <td>{{$offer -> days}}</td>

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
 