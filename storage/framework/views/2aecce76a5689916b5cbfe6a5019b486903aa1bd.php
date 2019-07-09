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


                      <?php if(Session::has('faild')): ?>
                            <div class="alert alert-danger">
                               <?php echo e(Session::get('faild')); ?>

                            </div>
                        <?php endif; ?>

                          <?php if(Session::has('success')): ?>
                            <div class="alert alert-success">
                               <?php echo e(Session::get('success')); ?>

                            </div>
                        <?php endif; ?>


 <form action="<?php echo e(route('send.notifications')); ?>" method="post" >

            	<div class="ui field">
                        <label>
                             الموضوع  <span class="require">*</span>
                        </label><!--End label-->
                             <input class="form-control" type="text" value="" name="subject">

                             <?php if(!empty($errors)): ?>
						         
						        <?php if($errors -> has('subject')): ?> 
	                             <div class="alert alert-danger">
	                              <?php echo e($errors -> first('subject')); ?>

	                            </div>
	                            <?php endif; ?>
 
						   <?php endif; ?>


                     </div>

<br>

                     <div class="ui field">
                        <label>
                             المحتوي     <span class="require">*</span>
                        </label><!--End label-->
                             <textarea   class="form-control" type="text" value="" name="content"></textarea>

                               <?php if(!empty($errors)): ?>
						         
						        <?php if($errors -> has('content')): ?> 
	                             <div class="alert alert-danger">
	                              <?php echo e($errors -> first('content')); ?>

	                            </div>
	                            <?php endif; ?>
 
						   <?php endif; ?>
                     </div>

<br>
                      <button type="submit" class="btn btn-success">
                        <i class="fa fa-send"></i> ارسال
                    </button>


               
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

                    	  <?php if(!empty($errors)): ?>
						         
						        <?php if($errors -> has('ids')): ?> 
	                             <div class="alert alert-danger">
	                              <?php echo e($errors -> first('ids')); ?>

	                            </div>
	                            <?php endif; ?>
 
						   <?php endif; ?>


                        <thead>
                            <tr>
                            	<td><input type="checkbox" id = "chckHead" /></td>
                            	<th>المسلسل  </th>
                                <th> الاسم </th>
                                <th>العضوية  </th>
                               </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($actors) && $actors ->count() > 0): ?>
                                <?php $__currentLoopData = $actors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $actor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
										<td>
										 <input type="checkbox" value="<?php echo e($actor-> access_token); ?>" name="ids[]" data_id="<?php echo e($actor-> access_token); ?>" class = "chcktbl" />
										</td>
                                        <td> <?php echo e($actor -> id); ?> </td>
                                        <td> <?php echo e($actor -> full_name); ?> </td>
                                        <td> <?php echo e($actor-> type); ?> </td>
                                        
                                        
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

             </form>  
            </div>
        </div><!--End Widget-->
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('customJs'); ?>

<script type="text/javascript">
	 
     $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


   $('#chckHead').click(function(){

	   	if(this.checked == false){
            
            $('.chcktbl:checked').attr('checked',false);

	   	}else{

             $('.chcktbl:not(:checked)').attr('checked',true);

	   	}

   });



</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>