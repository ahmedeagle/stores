
@extends('cpanel.layout.master')

@section('customCss')
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
 @stop

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
                <div class="col-sm-12">
                   <div class="row" style="padding: 50px;margin: -50px auto 0 auto; width: 870px">
                        <form method="GET" action="{{route('offers.reports')}}">
                          <div class="col-md-3" style="width: 150px">
                        <select name="status">
                           <option value=""> كل العروض  </option>
                          
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
                                 <th> قيمه العرض   </th>
                                  <th>  حاله العرض  </th>
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
                                        
                                        <td>{{$offer -> paid  == '1' ? $offer ->  paid_amount : '----'}}</td>
                                        <td>

                           @php
                                                     
                               if($offer-> status == '0' &&  $offer-> expire == '0'){
                                   echo 'جديد ';
                                   }
                             elseif ($offer-> status == '1') {
                                   echo 'موافق عليه ';
                                }elseif ($offer-> expire == '1') {
                                  echo 'ملغي او منتهي ';
                                }elseif ($offer -> publish == '0' && $offer -> status=='2') {
                                 echo 'موقوف ';
                                }elseif ($offer -> publish == '1') {
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