<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة  المدرين </h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
            <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li> 
                     <li class="active">قائمة  المدرين </li>
                </ul>
             
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
                قائمة  المدرين  
            </div>
            <div class="widget-content">
                <div class="col-sm-12">
                    <a href="<?php echo e(route('create_admin')); ?>" class="custom-btn red-bc">
                        <i class="fa fa-plus"></i>
                        إضافة مدير  جديد
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
                    <?php if($admins->count()): ?>        
                    <table id="datatable" class="table table-hover">
                        <thead>
                            <tr>
                                 <th> الاسم  </th>
                                <th> البريد الألكترونى</th>
                                 <th> الحالة </th>
                                <th>  </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                
                                <td> <?php echo e($admin->full_name); ?> </td>
                                <td> <?php echo e($admin->email); ?> </td>
                                 <td> <?php echo e(($admin->publish  == 1)? 'نشط ' : 'غير نشط '); ?></td>
                                <td>
                                    <!-- <button class="custom-btn green-bc">
                                        <i class="fa fa-eye"></i>
                                    </button> -->
                                    <a href="<?php echo e(route('admin.edit',$admin -> id)); ?>" class="custom-btn blue-bc" title="تعديل  المدير ">
                                        <i class="fa fa-pencil"></i>
                                    </a>

                                      <a href="<?php echo e(route('admin.delete',$admin -> id)); ?>" class="custom-btn blue-bc" title="حذف ">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
 

                                    <!--
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