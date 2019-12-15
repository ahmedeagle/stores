<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة  الوظائف  </h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                     <li class="active">قائمة  الوظائف  </li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة   الوظائف  
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
                            	<th>  رقم  الوظيفه   </th>
                                <th> صوره  المتجر  </th>
                                <th>إسم   الوظيفه   </th>
                                <th>الوصف </th>
                                 <th>تاريخ الاعلان  </th>
                                 <th> المتجر  </th>
                                 <th>عدد المتقدمين للوظيفه </th>
                                 <th>الحاله </th>
                                 <th> </th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($jobs) && $jobs ->count() > 0): ?>
                                <?php $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td> <?php echo e($job -> job_id); ?> </td>
                                        <td> <img style="width: 60px;height: 60px;" src="<?php echo e($job -> store_image); ?>"> </td>
                                        <td> <?php echo e($job-> job_title); ?> </td>
                                         <td> <?php echo e($job-> created_at); ?> </td>
                                        <td> <?php echo e(str_limit($job-> job_desc,30)); ?> </td>
                                         <td>  <a title="عرض " href="<?php echo e(route('provider.edit',$job -> provider_id)); ?>"> <?php echo e($job->store_name); ?> </a> </td>

                                         <td><?php echo e($job -> applicants); ?></td>
                                        
                                         <td>
                                            <?php echo e($job -> publish == 1 ? 'مفعل ' : 'غير مفعل '); ?>

                                          
                                        </td>
                                        <td>
                                              
                                             <?php if($job -> publish == 0): ?>
                                              <a title="نشر  الوظيفه  "   data_provider="<?php echo e($job -> provider_id); ?>" data_id="<?php echo e($job -> job_id); ?>" data_status="1" href="" class="publishingJob custom-btn blue-bc">
                                                <i class="fa fa-bullhorn" aria-hidden="true"></i>
                                                </a>
                                              <?php elseif($job -> publish == 1 ): ?>  

                                                 <a title="ايقاف الوظيفه  " href=""     data_provider="<?php echo e($job -> provider_id); ?>" data_id="<?php echo e($job -> job_id); ?>" data_status="0" class="unpublishingJob custom-btn blue-bc">
                                                <i class="fa fa-pause" aria-hidden="true"></i>
                                                 </a>

                                             <?php else: ?>
 
                                                -----
 
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

 


<?php $__env->startSection('customJs'); ?>

<script type="text/javascript">
	 
     $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


 



      $(document).on('click',".publishingJob,.unpublishingJob",function(e){

     	$('#alert_success').empty().hide();
        $('#alert_danger').empty().hide();

          e.preventDefault();

         var status          =  $(this).attr('data_status');
         var job_id          =  $(this).attr('data_id');
         var provider_id     =  $(this).attr('data_provider');
 

         $.ajax({
     
              type :'post',

              url  :"<?php echo e(route('jobs.publishing')); ?>",

              data :{
     
                   '_token'            :   $('input[name="_token"]').val(),
                   'job_id'            :   job_id,
                   'status'            :   status, 
                   'provider_id'       :   provider_id,
       
                },

              success:function(data)
              {                                          

                    var status = data.status;                          
 
                  if(data.error){



                   $('#alert_danger').show().empty().append(data.error);

                  }
  

                if(data.success){
                      
                      $('#alert_success').show().empty().append(data.success);
                }
 
			                setTimeout(location.reload.bind(location), 1700);
                   
	             }
 
               
            });
 
    });



</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>