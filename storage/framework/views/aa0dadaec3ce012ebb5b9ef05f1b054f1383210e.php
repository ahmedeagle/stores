<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة مقدمى الخدمة</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                    <li>مقدمى الخدمة</li>
                    <li class="active">قائمة مقدمى الخدمة</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة مقدمى الخدمة
            </div>
            <div class="widget-content">
                <div class="col-sm-12">
                    <a href="<?php echo e(route('provider.create')); ?>" class="custom-btn red-bc">
                        <i class="fa fa-plus"></i>
                        إضافة مقدم خدمة
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
                                <th>الأسم بالكامل</th>
                                <th>إسم  المتجر </th>
                                <th> رقم الهاتف</th>
                                 <th> الدولة</th>
                                <th> المدينة </th>
                                 <th> الحالة </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($providers) && $providers ->count() > 0 ): ?>
                                <?php $__currentLoopData = $providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td> <?php echo e($provider->full_name); ?> </td>
                                        <td> <?php echo e($provider-> store_name); ?> </td>
                                        <td> <?php echo e($provider->country_code.$provider->phone); ?> </td>
                                         <td> <?php echo e($provider->country); ?> </td>
                                        <td> <?php echo e($provider->city); ?> </td>
                                         <td> <?php echo e(($provider->status == 0)? 'not activated' : 'activated'); ?> </td>
                                        <td>
                                            <!-- <button class="custom-btn green-bc">
                                                <i class="fa fa-eye"></i>
                                            </button> -->
                                            <a href="<?php echo e(route('provider.edit', $provider->provider_id)); ?>" class="custom-btn blue-bc">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <!-- <button class="custom-btn red-bc">
                                                <i class="fa fa-trash-o"></i>
                                            </button> -->
                                            <a href="<?php echo e(route('provider.products', $provider->provider_id)); ?>" class="custom-btn blue-bc">
                                                <i class="fa fa-cog"></i>
                                            </a>
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