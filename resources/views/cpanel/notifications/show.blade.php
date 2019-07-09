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


                      @if(Session::has('faild'))
                            <div class="alert alert-danger">
                               {{ Session::get('faild') }}
                            </div>
                        @endif

                          @if(Session::has('success'))
                            <div class="alert alert-success">
                               {{ Session::get('success') }}
                            </div>
                        @endif


 <form action="{{ route('send.notifications') }}" method="post" >

            	<div class="ui field">
                        <label>
                             الموضوع  <span class="require">*</span>
                        </label><!--End label-->
                             <input class="form-control" type="text" value="" name="subject">

                             @if(!empty($errors))
						         
						        @if($errors -> has('subject')) 
	                             <div class="alert alert-danger">
	                              {{$errors -> first('subject')}}
	                            </div>
	                            @endif
 
						   @endif


                     </div>

<br>

                     <div class="ui field">
                        <label>
                             المحتوي     <span class="require">*</span>
                        </label><!--End label-->
                             <textarea   class="form-control" type="text" value="" name="content"></textarea>

                               @if(!empty($errors))
						         
						        @if($errors -> has('content')) 
	                             <div class="alert alert-danger">
	                              {{$errors -> first('content')}}
	                            </div>
	                            @endif
 
						   @endif
                     </div>

<br>
                      <button type="submit" class="btn btn-success">
                        <i class="fa fa-send"></i> ارسال
                    </button>


               
                <div class="spacer-25"></div><!--End Spacer-->

              
               <div class="alert alert-success" id="alert_success" style="display: none;">
                        
                    </div>

                     <div class="alert alert-danger"  id="alert_danger" style="display: none;">
                     </div>

 
                <div class="table-responsive">          
                    <table id="datatable" class="table table-hover">

                    	  @if(!empty($errors))
						         
						        @if($errors -> has('ids')) 
	                             <div class="alert alert-danger">
	                              {{$errors -> first('ids')}}
	                            </div>
	                            @endif
 
						   @endif


                        <thead>
                            <tr>
                            	<td><input type="checkbox" id = "chckHead" /></td>
                            	<th>المسلسل  </th>
                                <th> الاسم </th>
                                <th>العضوية  </th>
                               </tr>
                        </thead>
                        <tbody>
                            @if(isset($actors) && $actors ->count() > 0)
                                @foreach($actors AS $actor)
                                    <tr>
										<td>
										 <input type="checkbox" value="{{$actor-> access_token}}" name="ids[]" data_id="{{$actor-> access_token}}" class = "chcktbl" />
										</td>
                                        <td> {{ $actor -> id}} </td>
                                        <td> {{ $actor -> full_name}} </td>
                                        <td> {{ $actor-> type }} </td>
                                        
                                        
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

             </form>  
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


   $('#chckHead').click(function(){

	   	if(this.checked == false){
            
            $('.chcktbl:checked').attr('checked',false);

	   	}else{

             $('.chcktbl:not(:checked)').attr('checked',true);

	   	}

   });



</script>
@stop