<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>تفاصيل الطلب</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                    <li><a href="<?php echo e(route('orders.filter')); ?>">الطلبات</a></li>
                    <li class="active">تفاصيل الطلب</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
              تفاصيل الطلب
            </div>
            <div class="widget-content">
                <!-- <div class="col-sm-12">
                    <a href="" class="custom-btn red-bc">
                        <i class="fa fa-plus"></i>
                        Add new order
                    </a>
                </div> -->
                <div class="spacer-25"></div><!--End Spacer-->
                <?php if(Session::has('success')): ?>
                    <div class="alert alert-success">
                        <strong>تم بنجاح  !</strong> <?php echo e(Session::get('success')); ?>

                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                <?php endif; ?>
                <div class="table-responsive">  
                    <?php if($header != NULL): ?>        
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                 <th>كود الطلب</th>
                                <th>نوع الطلب</th>
                                <th>طريقة التوصيل</th>
                                <th>المستخدم</th>
                                <th>رقم هاتف المستخدم</th>
                                <th>البريد الألكترونى</th>
                                <th>عنوان المستخدم</th>
                                <th>مقدم الخدمة</th>
                                <th>الموصل</th>
                                <th>الأجمالى</th>
                                <th>الكمية</th>
                                <th>التاريخ</th>
                                <th>الحالة </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo e($header->order_code); ?></td>
                                <td><?php echo e(($header->in_future == 1)? 'Future order' : 'Current order'); ?></td>
                                <td><?php echo e($header->method_en_name); ?></td>
                                <td><?php echo e($header->user); ?></td>
                                <td><?php echo e($header->user_phone); ?></td>
                                <td><?php echo e($header->user_email); ?></td>
                                <td><?php echo e($header->address); ?></td>
                                <td><?php echo e($header->provider); ?></td>
                                <td><?php echo e($header->delivery); ?></td>
                                <td><?php echo e($header->total_value); ?></td>
                                <td><?php echo e($header->total_qty); ?></td>
                                <td><?php echo e(date('Y-m-d', strtotime($header->created_at))); ?></td>
                                <td><?php echo e($header->sts); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
                <span class="spacer-25"></span>
                <div class="table-responsive">  
                    <?php if(isset($details) && $details->count() > 0): ?>        
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>الصورة</th>
                                <th>الوجبة</th>
                                <th>الكمية</th>
                                <th>السعر</th>
                                <th>الأجمالى</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total = 0; ?>
                            <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $total += ($row->product_price * $row->qty); ?>
                            <tr>
                                <td class="width-90">
                                    <a class="img-popup-link" href="<?php echo e($row -> main_image); ?>">
                                        <img src="<?php echo e($row -> main_image); ?>" class="table-img">
                                    </a>
                                </td>
                                <td><?php echo e($row-> title); ?></td>
                                <td><?php echo e($row->qty); ?></td>
                                <td><?php echo e($row->product_price); ?></td>
                                <td><?php echo e(($row->product_price * $row->qty)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td colspan="4">الأجمالى</td>
                                <td><?php echo e($total); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div><!--End Widget-->
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>