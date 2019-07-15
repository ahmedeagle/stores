<?php $__env->startSection('content'); ?>
   <div class="content">
        <div class="col-sm-12">
            <div class="widget">
                <div class="widget-content">
                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-user-o"></i>
                            </div>
                            <a style="color: #fff;" href="<?php echo e(route('user.show')); ?>?status=active">
                            <div class="counter-content"> 
                                <div class="timer" data-to="<?php echo e(($activeusers != NULL)? $activeusers : 0); ?>" data-speed="2500"><?php echo e(($activeusers != NULL)? $activeusers : 0); ?></div>
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
                             <a style="color: #fff;" href="<?php echo e(route('user.show')); ?>?status=inactive">
                            <div class="counter-content"> 
                                 
                                <div class="timer" data-to="<?php echo e(($inactiveusers != NULL)? $inactiveusers : 0); ?>" data-speed="2500"><?php echo e(($inactiveusers != NULL)? $inactiveusers : 0); ?></div>
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
                             <a style="color: #fff;" href="<?php echo e(route('provider.show')); ?>?status=active">
                            <div class="counter-content"> 
                                <div class="timer" data-to="<?php echo e(($activeproviders != NULL)? $activeproviders : 0); ?>" data-speed="2500"><?php echo e(($activeproviders != NULL)? $activeproviders : 0); ?></div>
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
                             <a style="color: #fff;" href="<?php echo e(route('provider.show')); ?>?status=inactive">
                            <div class="counter-content"> 
                                <div class="timer" data-to="<?php echo e(($inactiveproviders != NULL)? $inactiveproviders : 0); ?>" data-speed="2500"><?php echo e(($inactiveproviders != NULL)? $inactiveproviders : 0); ?></div>
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
                            <a style="color: #fff;" href="<?php echo e(route('deliveries.show')); ?>?status=active">
                            <div class="counter-content"> 
                                <div class="timer" data-to="<?php echo e(($activedeliveries != NULL)? $activedeliveries : 0); ?>" data-speed="2500"><?php echo e(($activedeliveries != NULL)? $activedeliveries : 0); ?></div>
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
                        <a style="color: #fff;" href="<?php echo e(route('deliveries.show')); ?>?status=inactive">
                            <div class="counter-content"> 
                                <div class="timer" data-to="<?php echo e(($inactivedeliveries != NULL)? $inactivedeliveries : 0); ?>" data-speed="2500"><?php echo e(($inactivedeliveries != NULL)? $inactivedeliveries : 0); ?></div>
                                <span>الموصلين الغير مفعلين </span>
                            </div>
                        </a>    
                        </div>

                    </div><!--End col-md-4-->
                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-product-hunt"></i>
                            </div>
                            <div class="counter-content"> 
                                <div class="timer" data-to="<?php echo e(($products != NULL)? $products : 0); ?>" data-speed="2500"><?php echo e(($products != NULL)? $products : 0); ?></div>
                                <span>المنتجات </span>
                            </div>
                        </div>
                    </div><!--End col-md-4-->
                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-user-o"></i>
                            </div>
                            <div class="counter-content"> 
                                <div class="timer" data-to="<?php echo e(($sale != NULL)? $sale : 0); ?>" data-speed="2500"><?php echo e(($sale != NULL)? $sale : 0); ?></div>
                                <span>المدخلات</span>
                            </div>
                        </div>
                    </div><!--End col-md-4-->
               

                     <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-comment-o"></i>
                            </div>
                     <a style="color: #fff;" href="<?php echo e(route('comments.show')); ?>?status=active"> 
                            <div class="counter-content"> 
                                <div class="timer" data-to="<?php echo e(($comments != NULL)? $comments : 0); ?>" data-speed="2500"><?php echo e(($comments != NULL)? $comments : 0); ?></div>
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
                            <a style="color: #fff;" href="<?php echo e(route('excellent.status',0)); ?>"> 
                            <div class="counter-content"> 
                                <div class="timer" data-to="<?php echo e(($excellentReq != NULL)? $excellentReq : 0); ?>" data-speed="2500"><?php echo e(($excellentReq != NULL)? $excellentReq : 0); ?></div>
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
                            <a style="color: #fff;" href="<?php echo e(route('offers.status',0)); ?>">
                                <div class="counter-content"> 
                                    <div class="timer" data-to="<?php echo e(($offers != NULL)? $offers : 0); ?>" data-speed="2500"><?php echo e(($offers != NULL)? $offers : 0); ?></div>
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
                               <a style="color: #fff;" href="<?php echo e(route('offers.status',0)); ?>">
                                <div class="counter-content"> 
                                    <div class="timer" data-to="<?php echo e(($offers != NULL)? $offers : 0); ?>" data-speed="2500"><?php echo e(($offers != NULL)? $offers : 0); ?></div>
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
    
<?php $__env->stopSection(); ?>
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>