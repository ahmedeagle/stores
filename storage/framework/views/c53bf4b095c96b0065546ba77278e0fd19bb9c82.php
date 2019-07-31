<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2><?php echo e($title); ?></h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                     <li class="active"> <?php echo e($title); ?>  </li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة   <?php echo e($title); ?>  
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


                <?php if(Session::has('errors')): ?>
                    <div class="alert alert-danger ">
                          <?php echo e(Session::get('errors')); ?>

                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                <?php endif; ?>
                <div class="table-responsive">          
                    <table id="datatable" class="table table-hover">
                        <thead>
                            <tr>
                           <th>مسلسل</th>  
                            <th> الاسم </th>  
                             <th> الصورة  </th>  
			                     <th>نوع التذكرة</th>
			                     <th>محتوى التذكرة</th>
			                     <th>تاريخ الانشاء</th>
			                     <th>العمليات</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($tickets) && $tickets ->count() > 0): ?>
                                <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                         
                                  <td><?php echo e($key + 1); ?></td>
                                  <td><?php echo e($ticket  -> name); ?></td>
                                  <td><img  style="width:90px; height:90px"  src="<?php echo e($imag_path.$ticket  -> profile_pic); ?>"> </td>
					                        <td><?php echo e($ticket->type_name); ?></td>
					                        <td><?php echo e(str_limit($ticket->title, $limit = 30, $end = "....")); ?></td>
					                        <td><?php echo e($ticket->created_at); ?></td>

					                        <td>
                                             
                                             <?php if($ticket -> solved == 0): ?>
                                                <a href="<?php echo e(route('ticket.replay',$ticket->id)); ?>" class="btn btn-success ">رد</a>

                                             <a title="غلق التذكره " href="<?php echo e(route('ticket.close',$ticket->id)); ?>?action=close" class="btn btn-danger "> غلق   

                                             </a>
                                             <?php else: ?>

                                              <a title="غلق التذكره " href="<?php echo e(route('ticket.close',$ticket->id)); ?>?action=open" class="btn btn-success "> فتح التذكره     

                                             </a>


                                             <?php endif; ?>


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