<?php $__env->startSection('customCss'); ?>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
 <?php $__env->stopSection(); ?>

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
                <div class="col-sm-12">
                   <div class="row" style="padding: 50px;margin: -50px auto 0 auto; width: 870px">
                        <form method="GET" action="<?php echo e(route('offers.reports')); ?>">
                          <div class="col-md-3" style="width: 150px">
                        <select name="status">
                           <option value=""> كل العروض  </option>
                          
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
                <div class="table-responsive">          
                    <table id="datatable" class="table table-hover">
                        <thead>
                            <tr>
                            	<th>  رقم العرض  </th>
                                <th> صوره العرض </th>
                                <th>إسم   العرض  </th>
                                <th>مدينه العرض </th>
                                 <th>تاريخ بدا العرض</th>
                                <th> تاريخ انتهاء العرض  </th>
                                 <th> المتجر  </th>
                                 <th> قيمه العرض   </th>
                                  <th>  حاله العرض  </th>
                             </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($offers) && $offers ->count() > 0): ?>
                                <?php $__currentLoopData = $offers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td> <?php echo e($offer -> offer_id); ?> </td>
                                        <td> <img style="width: 60px;height: 60px;" src="<?php echo e($offer ->offer_photo); ?>"> </td>
                                        <td> <?php echo e($offer-> offer_title); ?> </td>
                                        <td> <?php echo e($offer->city_name); ?> </td>
                                         <td> <?php echo e($offer->start_date); ?> </td>
                                        <td> <?php echo e($offer->end_date); ?> </td>
                                        <td>  <a title="عرض " href="<?php echo e(route('provider.edit',$offer -> provider_id)); ?>"> <?php echo e($offer->store_name); ?> </a> </td>
                                        
                                        <td><?php echo e($offer -> paid  == '1' ? $offer ->  paid_amount : '----'); ?></td>
                                        <td>

                           <?php 
                                                     
                               if($offer-> status == '0' &&  $offer-> expire == '0'){
                                   echo 'جديد ';
                                   }
                             elseif ($offer-> status == '1') {
                                   echo 'موافق عليه ';
                                }elseif ($offer-> expire == '1') {
                                  echo 'ملغي او منتهي ';
                                }elseif ($offer -> publish == '0' && $offer -> status=='2') {
                                 echo 'موقوف ';
                                }elseif ($offer -> publish == '1') {
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