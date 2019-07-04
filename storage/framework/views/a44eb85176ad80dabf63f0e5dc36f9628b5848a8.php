<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة الموصلين</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                    <li>الموصلين</li>
                    <li class="active">قائمة الموصلين</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة الموصلين
            </div>
            <div class="widget-content">
                <div class="col-sm-12">
                    <a href="<?php echo e(route('deliveries.create')); ?>" class="custom-btn red-bc">
                        <i class="fa fa-plus"></i>
                        إضافة موصل جديد
                    </a>
                </div>
                <div class="spacer-25"></div><!--End Spacer-->
                <?php if(Session::has('success')): ?>
                    <div class="alert alert-success">
                        <strong>تم بنجاح !</strong> <?php echo e(Session::get('success')); ?>

                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                <?php endif; ?>
                <div class="table-responsive">          
                    <table id="datatable" class="table table-hover">
                        <thead>
                            <tr>
                                <th> الأسم بالكامل </th>
                                <th> الإسم التجارى </th>
                                <th> رقم الجوال </th>
                                <th> البريد الإلكترونى </th>
                                <th> الدولة </th>
                                <th> المدينة </th>
                                <th> العنوان </th>
                                <th> تفعيل رقم الهاتف </th>
                                <th> تفعيل الادارة </th>
                                <th>  </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($deliveries->count()): ?>
                                <?php $__currentLoopData = $deliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $delivery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td> <?php echo e($delivery->full_name); ?> </td>
                                        <td> <?php echo e($delivery->brand_name); ?> </td>
                                        <td> <?php echo e($delivery->country_code.$delivery->phone); ?> </td>
                                        <td> <?php echo e($delivery->email); ?> </td>
                                        <td> <?php echo e($delivery->country); ?> </td>
                                        <td> <?php echo e($delivery->city); ?> </td>
                                        <td> <?php echo e($delivery->address); ?> </td>
                                        <td> <?php echo e(($delivery->status == 0 || $delivery->status == 3)? 'غير مفعل' : 'مفعل'); ?> </td>
                                        <td> <?php echo e(($delivery->status == 0 || $delivery->status == 2)? 'غير مفعل' : 'مفعل'); ?> </td>
                                        <td>
                                            <a href="<?php echo e(route('deliveries.edit', $delivery->delivery_id)); ?>" class="custom-btn blue-bc">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <!--
                                             0 => not sms activated
                                             2 => activate sms But not activated from manger
                                             1 => activated from the manger
                                            -->
                                            <?php if($delivery->status == 0 || $delivery->status == 2): ?>
                                            <form action="<?php echo e(route('deliveries.activate', $delivery->delivery_id)); ?>" method="post" name="activate-delivery" id="activate-delivery">
                                                <button type="submit" name="activate-delivery-btn" class="btn btn-success">تفعيل</button>
                                            </form>
                                            <?php endif; ?>
                                            <!-- <button class="custom-btn red-bc">
                                                <i class="fa fa-trash-o"></i>
                                            </button> -->
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!--End Widget-->
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>