<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة   الارباح     </h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                     <li class="active">قائمة  الارباح  </li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة   الارباح  
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
                            	<th>  رقم العرض  </th>
                                 <th>إسم   العرض  </th>
                                <th>القسم </th>
                                 <th>تاريخ  الطلب</th>
                                 <th>تاريخ  انتهاء الطلب </th>
                                  <th> المتجر  </th>
                                   <th>الحاله   </th>
                                   <th>القيمه المدفوعه   </th>
                                  <th>ايام العرض </th>
                             </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($providerRequests) && $providerRequests ->count() > 0): ?>
                                <?php $__currentLoopData = $providerRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $providerRequest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td> <?php echo e($providerRequest -> request_id); ?> </td>
                                         <td> <?php echo e($providerRequest-> title); ?> </td>
                                        <td> <?php echo e($providerRequest->category_name); ?> </td>

                                         <td> <?php echo e($providerRequest  -> start_date); ?> </td>

                                         <td> <?php echo e($providerRequest  -> end_date); ?> </td>

 
                                        <td>  <a title="عرض " href="<?php echo e(route('provider.edit',$providerRequest -> provider_id)); ?>"> <?php echo e($providerRequest->store_name); ?> </a> </td>

                                        <td> 
                                             <?php echo e($providerRequest -> status == 3 ?  'عرض منتهي ': 'عرض ساري '); ?>


                                         </td>
                                       <td><?php echo e($providerRequest -> paid_amount); ?></td>
                                       <td><?php echo e($providerRequest -> days); ?></td>
                                         
 
                                     </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>

                             <tfoot>
                            <tr>
                              <td>الاجمالي </td>
                              <td> <?php echo e($total); ?>ريال </td>
                            </tr>
                          </tfoot>

                        </tbody>
                    </table>
                </div>
            </div>
        </div><!--End Widget-->
    </div>
</div>
<?php $__env->stopSection(); ?>

 
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>