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
                <div class="col-sm-12">
                    <a href="{{ route('offers.status', 0) }}" class="custom-btn red-bc">
                         العروض الجديده 
                    </a>

                    <a href="{{ route('offers.status',1) }}" class="custom-btn red-bc">
                         العروض  الموافق عليها 
                    </a>

                     <a href="{{ route('offers.status',2) }}" class="custom-btn red-bc">
                     	العروض المدفوعه 
                     </a>


                     <a href="{{ route('offers.status',3) }}" class="custom-btn red-bc">
                         العروض المنتهيه  والملغاه 
                    </a>
 

                     <a href="{{ route('offers.status',4) }}" class="custom-btn red-bc">
                           العروض المنشوره 
                    </a>
 



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
                                <th></th>
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
                                      
            	@if($type == 0 )

            	 @if(! $offer -> status == '1' && $offer -> expire == '0')
            	    <a title="موافقه "  id="acceptOffer" data_provider="{{$offer -> provider_id}}" data_id="{{$offer -> offer_id}}" data_status="1" href="" class="offerAcceptance custom-btn blue-bc ">
	                    <i class="fa fa-check" aria-hidden="true"></i>

	                </a>
	             @endif   
                 
                 @if(! $offer -> expire == '1')       
	                <a title="رفض "  id="refuseOffer" data_provider="{{$offer -> provider_id}}"  data_id="{{$offer -> offer_id}}"  data_status="0" href="" class="offerAcceptance custom-btn blue-bc ">
	                    <i class="fa fa-close" aria-hidden="true"></i>
	                </a>
	              @endif
	                
              @endif

              @if($type == 2)

                @if(! $offer -> publish == 1  && $offer -> expire == '0' )
                
                    <a title="نشر العرض "   data_provider="{{$offer -> provider_id}}" data_id="{{$offer -> offer_id}}" data_status="1" href="" class="publishingOffer custom-btn blue-bc">
                                                <i class="fa fa-bullhorn" aria-hidden="true"></i>
                    </a>

               @endif

             @endif  


                @if($type == 4)

                
                 @if( $offer -> publish == 1 && $offer -> expire == '0')  
                    <a title="وقف الاعلان " href="" id="publishOffer"   data_provider="{{$offer -> provider_id}}" data_id="{{$offer -> offer_id}}" data_status="0" class="unpublishingOffer custom-btn blue-bc">
                                                <i class="fa fa-pause" aria-hidden="true"></i>
                    </a>

                 @endif   

               @endif

 
                                          
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

<script type="text/javascript">
	 
     $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });



     $(document).on('click','.offerAcceptance',function(e){

     	$('#alert_success').empty().hide();
        $('#alert_danger').empty().hide();

          e.preventDefault();

         var status       =  $(this).attr('data_status');
         var offer_id     =  $(this).attr('data_id');
         var provider_id  =  $(this).attr('data_provider');
 

         $.ajax({
     
              type :'post',

              url  :"{{route('offers.acceptnace')}}",

              data :{
     
                   '_token'            :   $('input[name="_token"]').val(),
                   'offer_id'          :   offer_id,
                   'status'            :   status, 
                   'provider_id'       :   provider_id,
       
                },

              success:function(data)
              {                                          

                    var status = data.status;                          

                    if(status == 1){

                         $('#acceptOffer').hide();

                    }

                    else if(status == 0)
                    {

                         $('#refuseOffer').hide();
                    }

                  if(data.error){



                   $('#alert_danger').show().empty().append(data.error);

                  }
  

                if(data.success){
                      
                      $('#alert_success').show().empty().append(data.success);
                }

 
                setTimeout(location.reload.bind(location), 2000);

                        

              }

                   

               
            });


 
    });



      $(document).on('click',".publishingOffer,.unpublishingOffer",function(e){

     	$('#alert_success').empty().hide();
        $('#alert_danger').empty().hide();

          e.preventDefault();

         var status       =  $(this).attr('data_status');
         var offer_id     =  $(this).attr('data_id');
         var provider_id  =  $(this).attr('data_provider');
 

         $.ajax({
     
              type :'post',

              url  :"{{route('offers.publishing')}}",

              data :{
     
                   '_token'            :   $('input[name="_token"]').val(),
                   'offer_id'          :   offer_id,
                   'status'            :   status, 
                   'provider_id'       :   provider_id,
       
                },

              success:function(data)
              {                                          

                    var status = data.status;                          

                    if(status == 1){

                         $('.publishingOffer').hide();
                         $('.unpublishingOffer').show();

                    }

                    else if(status == 0)
                    {
                         
                         $('.publishingOffer').show();
                         $('.unpublishingOffer').hide();
                    }

                  if(data.error){



                   $('#alert_danger').show().empty().append(data.error);

                  }
  

                if(data.success){
                      
                      $('#alert_success').show().empty().append(data.success);
                }

 
			                setTimeout(location.reload.bind(location), 2000);
                        

	             }

	                   

               
            });


 
    });



</script>
@stop