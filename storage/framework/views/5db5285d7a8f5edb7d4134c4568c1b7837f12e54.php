<?php $__env->startSection('customCss'); ?>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
 <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة  طلبات التمييز  </h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                     <li class="active">قائمة    طلبات التمييز  </li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة   طلبات التمييز  
            </div>
            <div class="widget-content">
                <div class="col-sm-12">
                   <div class="row" style="padding: 50px;margin: -50px auto 0 auto; width: 870px">
                        <form method="GET" action="<?php echo e(route('excellent.reports')); ?>">
                          <div class="col-md-3" style="width: 150px">
                        <select name="status">
                           <option value=""> كل  الطلبات   </option>
                          
                          <option value="pending">الجديده </option>
                          <option value="approved">الموافق عليها </option>
                          <option value="canceled">الملغاه و المنتهيه  </option>
                          <option value="unpublished">الموقوفه </option>
                           <option value="published">المنشوره </option>
                        </select>
                        </div>
                        <div class="col-md-3" style="width: 200px">
                          <input class="datepicker form-control" type="text" value="<?php echo e($request -> from); ?>" name="from" placeholder="تاريخ البدايه كــ  Y-M-D"  >
                        </div>
                        <div class="col-md-3" style="width: 200px">
                          <input class="datepicker form-control" type="text" value="<?php echo e($request->to); ?>" name="to" placeholder="تاريخ النهايه كــ  Y-M-D" >
                        </div>
                        <div class="col-md-3">
                          <button class="btn btn-success" type="submit"><i class="fa fa-search"> بحث</i></button>
                        </div>
                          </form>
                        </div>



                </div>
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
                    <div class="alert alert-danger">
                         <?php echo e(Session::get('errors')); ?>

                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                <?php endif; ?>


                <div class="table-responsive">          
                    <table id="datatable" class="table table-hover">
                        <thead>
                            <tr>
                            	<th>  رقم  الطلب   </th>
                                 <th>إسم   الطلب   </th>
                                <th>مدينه الطلب  </th>
                                 <th>تاريخ بدا  الطلب </th>
                                <th> تاريخ انتهاء  الطلب   </th>
                                 <th> المتجر  </th>
                                 <th> قيمه  الطلب    </th>
                                  <th>  حاله  الطلب   </th>
                             </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($providerRequests) && $providerRequests ->count() > 0): ?>
                                <?php $__currentLoopData = $providerRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $providerRequest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td> <?php echo e($providerRequest -> request_id); ?> </td>
                                         <td> <?php echo e($providerRequest-> title); ?> </td>
                                        <td> <?php echo e($providerRequest-> category_name); ?> </td>
                                         <td> <?php echo e($providerRequest->start_date); ?> </td>
                                        <td> <?php echo e($providerRequest->end_date); ?> </td>
                                        <td>  <a title="عرض " href="<?php echo e(route('provider.edit',$providerRequest -> provider_id)); ?>"> <?php echo e($providerRequest->store_name); ?> </a> </td>
                                        
                                        <td><?php echo e($providerRequest -> paid  == '1' ? $providerRequest ->  paid_amount : '----'); ?></td>
                                        <td>

                           <?php 
                                                     
                               if($providerRequest-> status == '0'){
                                   echo 'جديد ';
                                   }
                             elseif ($providerRequest-> status == '1') {
                                   echo 'موافق عليه ';
                                }elseif ($providerRequest-> status == '3') {
                                  echo 'ملغي او منتهي ';
                                }elseif ($providerRequest -> publish == '0' && $providerRequest -> paid=='1') {
                                 echo 'موقوف ';
                                }elseif ($providerRequest -> publish == '1' && $providerRequest -> paid=='1' ) {
                                  echo 'منشور ';
                                }
                                


                               ?>
                                              

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

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<script type="text/javascript">
	 
     $( function() {
    $( ".datepicker" ).datepicker();
  } );


     $( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });



</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>