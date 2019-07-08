@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة  الوظائف  </h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                     <li class="active">قائمة  الوظائف  </li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة   الوظائف  
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
                            	<th>  رقم  الوظيفه   </th>
                                <th> صوره  المتجر  </th>
                                <th>إسم   الوظيفه   </th>
                                <th>الوصف </th>
                                 <th>تاريخ الاعلان  </th>
                                 <th> المتجر  </th>
                                 <th>عدد المتقدمين للوظيفه </th>
                                 <th>الحاله </th>
                                 <th> </th>

                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($jobs) && $jobs ->count() > 0)
                                @foreach($jobs AS $job)
                                    <tr>
                                        <td> {{ $job -> job_id}} </td>
                                        <td> <img style="width: 60px;height: 60px;" src="{{ $job -> store_image }}"> </td>
                                        <td> {{ $job-> job_title }} </td>
                                         <td> {{ $job-> created_at}} </td>
                                        <td> {{ str_limit($job-> job_desc,30)}} </td>
                                         <td>  <a title="عرض " href="{{ route('provider.edit',$job -> provider_id) }}"> {{ $job->store_name }} </a> </td>

                                         <td>{{$job -> applicants}}</td>
                                        
                                         <td>
                                            {{$job -> publish == 1 ? 'مفعل ' : 'غير مفعل '}}
                                          
                                        </td>
                                        <td>
                                              
                                             @if($job -> publish == 0)
                                              <a title="نشر  الوظيفه  "   data_provider="{{$job -> provider_id}}" data_id="{{$job -> job_id}}" data_status="1" href="" class="publishingJob custom-btn blue-bc">
                                                <i class="fa fa-bullhorn" aria-hidden="true"></i>
                                                </a>
                                              @elseif($job -> publish == 1 )  

                                                 <a title="ايقاف الوظيفه  " href=""     data_provider="{{$job -> provider_id}}" data_id="{{$job -> job_id}}" data_status="0" class="unpublishingJob custom-btn blue-bc">
                                                <i class="fa fa-pause" aria-hidden="true"></i>
                                                 </a>

                                             @else
 
                                                -----
 
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


 



      $(document).on('click',".publishingJob,.unpublishingJob",function(e){

     	$('#alert_success').empty().hide();
        $('#alert_danger').empty().hide();

          e.preventDefault();

         var status          =  $(this).attr('data_status');
         var job_id          =  $(this).attr('data_id');
         var provider_id     =  $(this).attr('data_provider');
 

         $.ajax({
     
              type :'post',

              url  :"{{route('jobs.publishing')}}",

              data :{
     
                   '_token'            :   $('input[name="_token"]').val(),
                   'job_id'            :   job_id,
                   'status'            :   status, 
                   'provider_id'       :   provider_id,
       
                },

              success:function(data)
              {                                          

                    var status = data.status;                          
 
                  if(data.error){



                   $('#alert_danger').show().empty().append(data.error);

                  }
  

                if(data.success){
                      
                      $('#alert_success').show().empty().append(data.success);
                }
 
			                setTimeout(location.reload.bind(location), 1700);
                   
	             }
 
               
            });
 
    });



</script>
@stop
