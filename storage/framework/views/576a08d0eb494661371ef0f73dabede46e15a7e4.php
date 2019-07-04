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
                            <div class="counter-content"> 
                                <div class="timer" data-to="<?php echo e(($users != NULL)? $users : 0); ?>" data-speed="2500"><?php echo e(($users != NULL)? $users : 0); ?></div>
                                <span>المستخدمين</span>
                            </div>
                        </div>
                    </div><!--End col-md-4-->
                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-user-o"></i>
                            </div>
                            <div class="counter-content"> 
                                <div class="timer" data-to="<?php echo e(($providers != NULL)? $providers : 0); ?>" data-speed="2500"><?php echo e(($providers != NULL)? $providers : 0); ?></div>
                                <span>مقدمين الخدمات</span>
                            </div>
                        </div>
                    </div><!--End col-md-4-->
                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-user-o"></i>
                            </div>
                            <div class="counter-content"> 
                                <div class="timer" data-to="<?php echo e(($deliveries != NULL)? $deliveries : 0); ?>" data-speed="2500"><?php echo e(($deliveries != NULL)? $deliveries : 0); ?></div>
                                <span>الموصلين</span>
                            </div>
                        </div>
                    </div><!--End col-md-4-->
                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-cutlery"></i>
                            </div>
                            <div class="counter-content"> 
                                <div class="timer" data-to="<?php echo e(($products != NULL)? $products : 0); ?>" data-speed="2500"><?php echo e(($products != NULL)? $products : 0); ?></div>
                                <span>الوجبات</span>
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
                                <i class="fa fa-user-o"></i>
                            </div>
                            <div class="counter-content"> 
                                <div class="timer" data-to="<?php echo e(($return != NULL)? $return : 0); ?>" data-speed="2500"><?php echo e(($return != NULL)? $return : 0); ?></div>
                                <span>المرجعات</span>
                            </div>
                        </div>
                    </div><!--End col-md-4-->
                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-comment-o"></i>
                            </div>
                            <div class="counter-content"> 
                                <div class="timer" data-to="<?php echo e(($comments != NULL)? $comments : 0); ?>" data-speed="2500"><?php echo e(($comments != NULL)? $comments : 0); ?></div>
                                <span>التعليقات</span>
                            </div>
                        </div>
                    </div><!--End col-md-4-->
                </div>
            </div>
        </div>
        <div class="footer-copy-rights">جميع الحقوق محفوظة <a href="https://www.al-yasser.com.sa/en/">مجموعة الياسر</a> ©2017
        </div>
    </div>
    
<?php $__env->stopSection(); ?>
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>