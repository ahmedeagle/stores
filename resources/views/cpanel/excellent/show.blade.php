@extends('cpanel.layout.master')
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
                     <li class="active">قائمة  طلبات التميز </li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة   الطلبات  
            </div>
            <div class="widget-content">
                <div class="col-sm-12">
                    <a href="{{ route('excellent.status', 0) }}" class="custom-btn red-bc">
                          الحالية 
                    </a>

                    <a href="{{ route('excellent.status',1) }}" class="custom-btn red-bc">
                        المكتملة 
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
                                 <th>إسم   العرض  </th>
                                <th>القسم </th>
                                 <th>تاريخ  الطلب</th>
                                 <th>تاريخ  انتهاء الطلب </th>
                                  <th> المتجر  </th>
                                   <th>الحاله   </th>
                                  <th>   </th>
                             </tr>
                        </thead>
                        <tbody>
                            @if(isset($providerRequests) && $providerRequests ->count() > 0)
                                @foreach($providerRequests AS $providerRequest)
                                    <tr>
                                        <td> {{ $providerRequest -> request_id}} </td>
                                         <td> {{ $providerRequest-> title }} </td>
                                        <td> {{ $providerRequest->category_name}} </td>

                                         <td> {{ !empty($providerRequest->order_date) ? $providerRequest->order_date : '----' }} </td>

                                          <td> {{ !empty($providerRequest->expire_date) ? $providerRequest->expire_date : '----' }} </td>

 
                                        <td>  <a title="عرض " href="{{ route('provider.edit',$providerRequest -> provider_id) }}"> {{ $providerRequest->store_name }} </a> </td>

                                        <td> 
                                              @if($providerRequest -> status == '0' && $providerRequest -> status != '3')

                                                جديد 

                                             @elseif($providerRequest -> status == '1' && $providerRequest -> status != '3')

                                             موافق  علية 

                                             @elseif($providerRequest -> status == '2' && $providerRequest -> status != '3')

                                             مدفوع 

                                             @elseif($providerRequest -> publish  == '1' && $providerRequest -> status != '3')

                                             تم النشر 

                                             @else
                                              ----
                                             @endif

                                         </td>

                                        <td>

              @if($type == 0 or $type == '0' )

               @if(! $providerRequest -> status == '1'  && $providerRequest -> status != '3' && $providerRequest -> status != '2')
                  <a title="موافقه "  id="acceptRequest" data_provider="{{$providerRequest -> provider_id}}" data_id="{{$providerRequest -> request_id}}" data_status="1" href="" class="requestAcceptance custom-btn blue-bc ">
                      <i class="fa fa-check" aria-hidden="true"></i>

                  </a>
               @endif   
                 
                 @if( $providerRequest -> status == '1' && $providerRequest -> status != '3' && $providerRequest -> status != '2')       
                  <a title="رفض "  id="refuseRequest" data_provider="{{$providerRequest -> provider_id}}"  data_id="{{$providerRequest -> request_id}}"  data_status="0" href="" class="requestAcceptance custom-btn blue-bc ">
                      <i class="fa fa-close" aria-hidden="true"></i>
                  </a>
                @endif
                  
              @endif

              @if($type == 1 or $type == '1')

                @if(! $providerRequest -> publish == 1 && $providerRequest -> status !='3')
                
                    <a title="نشر العرض "   data_provider="{{$providerRequest -> provider_id}}" data_id="{{$providerRequest -> request_id}}" data_status="1" href="" class="publishingRequest custom-btn blue-bc">
                                                <i class="fa fa-bullhorn" aria-hidden="true"></i>
                    </a>

               @endif

             @endif  

 
                 @if( $providerRequest -> publish == 1 && $providerRequest -> status !='3') 
                    <a title="وقف الاعلان " href=""     data_provider="{{$providerRequest -> provider_id}}" data_id="{{$providerRequest -> request_id}}" data_status="0" class="unpublishingRequest custom-btn blue-bc">
                                                <i class="fa fa-pause" aria-hidden="true"></i>
                    </a>

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



     $(document).on('click','.requestAcceptance',function(e){

     	$('#alert_success').empty().hide();
        $('#alert_danger').empty().hide();

          e.preventDefault();

         var status         =  $(this).attr('data_status');
         var request_id     =  $(this).attr('data_id');
         var provider_id    =  $(this).attr('data_provider');
 

         $.ajax({
     
              type :'post',

              url  :"{{route('excellent.acceptnace')}}",

              data :{
     
                   '_token'            :   $('input[name="_token"]').val(),
                   'request_id'        :   request_id,
                   'status'            :   status, 
                   'provider_id'       :   provider_id,
       
                },

              success:function(data)
              {                                          

                    var status = data.status;                          

                    if(status == 1 ){

                         $('#acceptRequest').hide();

                    }

                    else if(status == 0)
                    {

                         $('#refuseRequest').hide();
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



      $(document).on('click',".publishingRequest,.unpublishingRequest",function(e){

     	$('#alert_success').empty().hide();
        $('#alert_danger').empty().hide();

          e.preventDefault();

         var status          =  $(this).attr('data_status');
         var request_id      =  $(this).attr('data_id');
         var provider_id     =  $(this).attr('data_provider');
 

         $.ajax({
     
              type :'post',

              url  :"{{route('excellent.publishing')}}",

              data :{
     
                   '_token'            :   $('input[name="_token"]').val(),
                   'request_id'        :   request_id,
                   'status'            :   status, 
                   'provider_id'       :   provider_id,
       
                },

              success:function(data)
              {                                          

                    var status = data.status;                          

                    if(status == 1){

                         $('.publishingRequest').hide();
                         $('.unpublishingRequest').show();

                    }

                    else if(status == 0)
                    {
                         
                         $('.publishingRequest').show();
                         $('.unpublishingRequest').hide();
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