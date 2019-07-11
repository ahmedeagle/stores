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

               @if(!empty($errors))
                 @if($errors -> has('content'))
                  <div class="alert alert-danger"> {{ $errors -> first('content') }}</div>
                  @endif
              @endif
     
               @if(Session::has('success'))
                   <div class="alert alert-success"> {{ Session::get('success') }}</div>
               @endif

                <div class="row">
      <div class="col-md-12">
         <div class="">
            <div class="row timeline-right p-t-35">
               <div class="col-12 col-sm-10 col-xl-11 p-l-5 p-b-35">
                  <div class="card">
                     <div class="card-block post-timelines">
                       
                        <div class="chat-header f-w-600">{{ $username }}</div>
                        <div class="social-time text-muted">{{ $ticket->created_at }}</div>
                        <br><br>

                     </div>
                     <div class="card-block">
                        <div class="timeline-details">
                           <div class="chat-header">({{ $ticket->type_name }})</div>
                           <br>
                           <p class="text-muted">{{ $ticket->title }}</p>
                        </div>
                     </div>
                     <hr>
                     <div class="card-block b-b-theme b-t-theme social-msg">
                        <a> <i class="icofont icofont-comment text-muted"></i> <span class="b-r-muted">الردود {{ count($ticket_replys) }}</span></a>
                     </div>
                     <hr>
                     <br>
                     <div class="card-block user-box">
                        <div class="p-b-30"><span class="f-right">  جميع الردود</span></div>
                        <br>
                     @foreach ($ticket_replys as $reply)
                        <div class="media m-b-20">
                           <div class="media-body b-b-muted social-client-description" style="padding-right: 20px;">
                              <div class="chat-header">
                                  {{ ($reply->FromUser == "0") ?'ادارة الموقع' : $username }}
                                  <span class="text-muted" style="padding-right: 5px;">{{ $reply->created_at }}</span>
                              </div>
                              <br>
                              <p class="text-muted">{{ $reply->reply }}</p>
                           </div>
                        </div>
                        <hr>
                     @endforeach

                     <br>
                        <div class="media">
                           <div class="media-body" style="padding-right: 20px;">
                              <form action="{{route('post.replay') }}" method="POST" >
                                  <div class="">
                                    <input type="text" class="form-control" name="content" placeholder="اضافة رد"/>

                                     <input type="hidden" name="ticket_id" value="{{ $ticket->id }}" />
                                     <br>
                                    <div class="text-right m-t-20"> <button style="width: 63px" type="submit" class="btn btn-md btn-success">رد</button>  <a href="{{ route('tickets' , $type) }}" class="btn btn-md btn-danger">رجوع</a></div>
                                 </div>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
               
            </div>
        </div><!--End Widget-->
    </div>
</div>
@stop

 
 