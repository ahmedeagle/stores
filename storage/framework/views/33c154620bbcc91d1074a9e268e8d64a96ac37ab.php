<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة  العروض </h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                     <li class="active">قائمة  العروض </li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة  العروض 
            </div>
            <div class="widget-content">
               
                <div class="spacer-25"></div><!--End Spacer-->

              
               <div class="alert alert-success" id="alert_success" style="display: none;">
                        
                    </div>

                     <div class="alert alert-danger"  id="alert_danger" style="display: none;">
                     </div>


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
                            	<th>   ألرقم </th>
                                <th>  العنوان </th>
                                <th>النص </th>
                                <th> التارخ </th>
                                 <th>الوقت </th>
                                <th> اسم المرسل اليه </th>
                                 <th>النوع  </th>
                             </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($notifications) && $notifications ->count() > 0): ?>
                                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notify): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td> <?php echo e($notify -> id); ?> </td>
                                        <td> <?php echo e($notify-> title); ?> </td>
                                        <td> <?php echo e($notify->content); ?> </td>
                                        <td> <?php echo e($notify->create_date); ?> </td>
                                        <td> <?php echo e($notify->create_time); ?> </td>
                                        <td> <?php echo e($notify -> actor_name); ?> </td>
                                        <td> <?php echo e($notify -> actor_type); ?> </td>
                                        
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