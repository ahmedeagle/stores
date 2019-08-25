@extends('cpanel.layout.master')
@section('content')
   <div class="content">
        <div class="col-sm-12">
            <div class="widget">
                <div class="widget-content">
                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-user-o"></i>
                            </div>
                            <a style="color: #fff;" href="{{route('user.show')}}?status=active">
                            <div class="counter-content"> 
                                <div class="timer" data-to="{{ ($activeusers != NULL)? $activeusers : 0 }}" data-speed="2500">{{ ($activeusers != NULL)? $activeusers : 0 }}</div>
                                <span>المستخدمين المفعلين </span>
                            
                            </div>
                            </a>
                        </div>
                    </div><!--End col-md-4-->

                     <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-user-o"></i>
                            </div>
                             <a style="color: #fff;" href="{{route('user.show')}}?status=inactive">
                            <div class="counter-content"> 
                                 
                                <div class="timer" data-to="{{ ($inactiveusers != NULL)? $inactiveusers : 0 }}" data-speed="2500">{{ ($inactiveusers != NULL)? $inactiveusers : 0 }}</div>
                                <span>مستخدمين  غير  مفعلين  </span>

                            </div>
                        </a>

                        </div>
                    </div><!--End col-md-4-->


                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-home"></i>
                            </div>
                             <a style="color: #fff;" href="{{route('provider.show')}}?status=active">
                            <div class="counter-content"> 
                                <div class="timer" data-to="{{ ($activeproviders != NULL)? $activeproviders : 0 }}" data-speed="2500">{{ ($activeproviders != NULL)? $activeproviders : 0 }}</div>
                                <span>المتاجر المفعله </span>
                            </div>
                        </a>
                        </div>
                    </div><!--End col-md-4-->

                     <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-home"></i>
                            </div>
                             <a style="color: #fff;" href="{{route('provider.show')}}?status=inactive">
                            <div class="counter-content"> 
                                <div class="timer" data-to="{{ ($inactiveproviders != NULL)? $inactiveproviders : 0 }}" data-speed="2500">{{ ($inactiveproviders != NULL)? $inactiveproviders : 0 }}</div>
                                <span>المتاجر  الغير مفعله  </span>
                            </div>
                        </a>
                        </div>
                    </div><!--End col-md-4-->


                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-truck"></i>
                            </div>
                            <a style="color: #fff;" href="{{route('deliveries.show')}}?status=active">
                            <div class="counter-content"> 
                                <div class="timer" data-to="{{ ($activedeliveries != NULL)? $activedeliveries : 0 }}" data-speed="2500">{{ ($activedeliveries != NULL)? $activedeliveries : 0 }}</div>
                                <span>الموصلين المغعلين </span>
                            </div>
                        </a>
                        </div>
                    </div><!--End col-md-4-->


                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-truck"></i>
                            </div>
                        <a style="color: #fff;" href="{{route('deliveries.show')}}?status=inactive">
                            <div class="counter-content"> 
                                <div class="timer" data-to="{{ ($inactivedeliveries != NULL)? $inactivedeliveries : 0 }}" data-speed="2500">{{ ($inactivedeliveries != NULL)? $inactivedeliveries : 0 }}</div>
                                <span>الموصلين الغير مفعلين </span>
                            </div>
                        </a>    
                        </div>

                    </div><!--End col-md-4-->
                    <div class="col-md-4">
                        <div class="counter">
                           <a href="{{route('provider.products.list')}}">
                            <div class="counter-icon">
                                <i class="fa fa-product-hunt"></i>
                            </div>
                            <div class="counter-content"> 
                                <div class="timer" data-to="{{ ($products != NULL)? $products : 0 }}" data-speed="2500">{{ ($products != NULL)? $products : 0 }}</div>
                                <span>المنتجات </span>
                            </div>
                            </a> 

                        </div>
                    </div><!--End col-md-4-->
                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-user-o"></i>
                            </div>
                            <div class="counter-content"> 
                                <div class="timer" data-to="{{ ($sale != NULL)? $sale : 0 }}" data-speed="2500">{{ ($sale != NULL)? $sale : 0 }}</div>
                                <span>المدخلات</span>
                            </div>
                        </div>
                    </div><!--End col-md-4-->
               

                     <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-comment-o"></i>
                            </div>
                     <a style="color: #fff;" href="{{route('comments.show')}}?status=active"> 
                            <div class="counter-content"> 
                                <div class="timer" data-to="{{ ($comments != NULL)? $comments : 0 }}" data-speed="2500">{{ ($comments != NULL)? $comments : 0 }}</div>
                                <span>تعليقات جديده </span>
                            </div>
                        </a>
                        </div>
                    </div><!--End col-md-4-->

                     <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-star"></i>
                            </div>
                                                        <!-- new status -->
                            <a style="color: #fff;" href="{{route('excellent.status',0)}}"> 
                            <div class="counter-content"> 
                                <div class="timer" data-to="{{ ($excellentReq != NULL)? $excellentReq : 0 }}" data-speed="2500">{{ ($excellentReq != NULL)? $excellentReq : 0 }}</div>
                                <span> طلبات تمييز جديده  </span>
                            </div>
                            </a> 
                        </div>
                    </div><!--End col-md-4-->

                 <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-gift"></i>
                            </div>
                            <a style="color: #fff;" href="{{route('offers.status',0)}}">
                                <div class="counter-content"> 
                                    <div class="timer" data-to="{{ ($offers != NULL)? $offers : 0 }}" data-speed="2500">{{ ($offers != NULL)? $offers : 0 }}</div>
                                    <span> عروض جديده    </span>
                                </div>
                           </a> 
                        </div>
                    </div><!--End col-md-4-->

                 <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-ticket"></i>
                            </div>
                               <a style="color: #fff;" href="{{route('offers.status',0)}}">
                                <div class="counter-content"> 
                                    <div class="timer" data-to="{{ ($offers != NULL)? $offers : 0 }}" data-speed="2500">{{ ($offers != NULL)? $offers : 0 }}</div>
                                    <span> تذاكر  مفتوحه  </span>
                                </div>
                           </a> 
                        </div>
                    </div><!--End col-md-4-->


                </div>
            </div>
        </div>
        <div class="footer-copy-rights">جميع الحقوق محفوظة <a target="_blank" href="https://wisyst.com"> wisyst </a> ©2019
        </div>
    </div>
    
@stop