<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة المستخدمين</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
            <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li> 
                    <li>المستخدمين</li>
                    <li class="active">قائمة المستخدمين</li>
                </ul>
             
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
                قائمة المستخدمين 
            </div>
            <div class="widget-content">
                <div class="col-sm-12">
                    <a href="<?php echo e(route('user.create')); ?>" class="custom-btn red-bc">
                        <i class="fa fa-plus"></i>
                        إضافة مستخدم جديد
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
                    <?php if($users->count()): ?>        
                    <table id="datatable" class="table table-hover">
                        <thead>
                            <tr>
                                <th> صورة المستخدم</th>
                                <th> الأسم بالكامل</th>
                                <th> البريد الألكترونى</th>
                                <th> رقم الهاتف</th>
                                <th> الرصيد </th>
                                <th> المدينة </th>
                                <th> الحالة </th>
                                <th>  </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <a class="img-popup-link" href="<?php echo e($user->profile_pic); ?>">
                                        <img src="<?php echo e($user->profile_pic); ?>" class="table-img">
                                    </a>
                                </td>
                                <td> <?php echo e($user->full_name); ?> </td>
                                <td> <?php echo e($user->email); ?> </td>
                                <td> <?php echo e($user->country_code.$user->phone); ?> </td>
                                <td> <?php echo e($user->points); ?> </td>
                                <td> <?php echo e($user->city_en_name); ?> </td>
                                <td> <?php echo e(($user->status == 1)? 'activated' : 'not activated'); ?></td>
                                <td>
                                    <!-- <button class="custom-btn green-bc">
                                        <i class="fa fa-eye"></i>
                                    </button> -->
                                    <a href="<?php echo e(route('user.edit', $user->user_id)); ?>" class="custom-btn blue-bc" title="تعديل مستخدم">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <!-- <button class="custom-btn red-bc">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                    <a href="#" class="custom-btn blue-bc">
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