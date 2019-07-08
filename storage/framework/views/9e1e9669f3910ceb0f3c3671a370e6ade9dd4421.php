<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>الطلبات</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                    <li class="active"> الطلبات</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
                قائمة الطلبات
            </div>
            <div class="widget-content">
                <!-- <div class="col-sm-12">
                    <a href="" class="custom-btn red-bc">
                        <i class="fa fa-plus"></i>
                        Add new order
                    </a>
                </div> -->
                <div class="spacer-25"></div><!--End Spacer-->
                <?php if(Session::has('error')): ?>
                    <div class="alert alert-danger">
                        <strong>خطأ !</strong> <?php echo e(Session::get('error')); ?>

                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                <?php endif; ?>
                <div class="table-responsive">  
                    <?php if($headers->count()): ?>        
                    <table id="datatable" class="table table-hover">
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
                            <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                <td>
                                    <a href="<?php echo e(route('orders.details', $header->order_id)); ?>" class="custom-btn green-bc">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <!-- <a href="" class="custom-btn blue-bc">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <button class="custom-btn red-bc">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                    <a href="" class="custom-btn blue-bc">
                                        <i class="fa fa-cog"></i>
                                    </a> -->
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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