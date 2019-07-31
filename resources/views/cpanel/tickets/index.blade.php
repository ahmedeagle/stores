@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>{{$title}}</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                     <li class="active"> {{$title}}  </li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة   {{$title}}  
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


                @if(Session::has('errors'))
                    <div class="alert alert-danger ">
                          {{ Session::get('errors') }}
                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                @endif
                <div class="table-responsive">          
                    <table id="datatable" class="table table-hover">
                        <thead>
                            <tr>
                           <th>مسلسل</th>  
                            <th> الاسم </th>  
                             <th> الصورة  </th>  
			                     <th>نوع التذكرة</th>
			                     <th>محتوى التذكرة</th>
			                     <th>تاريخ الانشاء</th>
			                     <th>العمليات</th>

                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($tickets) && $tickets ->count() > 0)
                                @foreach($tickets AS $key => $ticket)
                                    <tr>
                                         
                                  <td>{{ $key + 1 }}</td>
                                  <td>{{ $ticket  -> name }}</td>
                                  <td><img  style="width:90px; height:90px"  src="{{$imag_path.$ticket  -> profile_pic }}"> </td>
					                        <td>{{ $ticket->type_name }}</td>
					                        <td>{{ str_limit($ticket->title, $limit = 30, $end = "....") }}</td>
					                        <td>{{ $ticket->created_at }}</td>

					                        <td>
                                             
                                             @if($ticket -> solved == 0)
                                                <a href="{{ route('ticket.replay',$ticket->id) }}" class="btn btn-success ">رد</a>

                                             <a title="غلق التذكره " href="{{ route('ticket.close',$ticket->id) }}?action=close" class="btn btn-danger "> غلق   

                                             </a>
                                             @else

                                              <a title="غلق التذكره " href="{{ route('ticket.close',$ticket->id) }}?action=open" class="btn btn-success "> فتح التذكره     

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

 
 
