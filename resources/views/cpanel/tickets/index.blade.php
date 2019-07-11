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
					                        <td>{{ $ticket->type_name }}</td>
					                        <td>{{ str_limit($ticket->title, $limit = 30, $end = "....") }}</td>
					                        <td>{{ $ticket->created_at }}</td>
					                        <td><a href="{{ route('ticket.replay',$ticket->id) }}" class="btn btn-success ">رد</a></td>
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

 
 
