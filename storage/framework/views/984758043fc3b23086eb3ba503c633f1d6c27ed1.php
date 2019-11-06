<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>المنتجات </h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                    <li><a href="<?php echo e(route('provider.show')); ?>">مقدمى الخدمة</a> </li>
                    <li class="active">المنتجات </li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
              المنتجات 
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
                                 <th>صوره المنتج  </th>
                                 <th>صوره  المتجر   </th>
                                <th>المتجر  </th>
                                <th>الأسم المنتج </th>
                                <th> السعر </th>
                                 <th> الوصف </th>
                                 <th>الحالة </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($products) && $products -> count() > 0): ?>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td> <img src="<?php echo e($product->product_image); ?>"></td>
                                        <td> <img style="width: 60px; height: 60px;" src="<?php echo e($product->profile_pic); ?>"></td>
                                         <td> <?php echo e($product-> store_name); ?> </td>
                                         <td> <?php echo e($product->title); ?> </td>
                                         <td> <?php echo e($product->price); ?> </td>
                                         <td> <?php echo e($product->description); ?> </td>
                                         <td> 
                                            
                                            <a href="" id="status_btn" product_id="<?php echo e($product -> product_id); ?>" status="<?php echo e($product->publish); ?>" class="custom-btn blue-bc">

                                                 <?php echo e(($product->publish == 1)? 'الغاء التفعيل  ' : 'تفعيل '); ?>

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

<?php $__env->startSection('customJs'); ?>

  <script type="text/javascript">
    

    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


     $(document).on('click','#status_btn',function(e){

          e.preventDefault();

         var status       =  $(this).attr('status');
         var product_id   =  $(this).attr('product_id');
 

         $.ajax({
     
              type :'post',

              url  :"<?php echo e(route('product.status')); ?>",

              data :{
     
                   '_token'            :   $('input[name="_token"]').val(),
                   'product_id'        :   product_id,
                   'currentstatus'     :   status, 
      
                },

              success:function(data)
              {                                          

                    var status = data.status;                          

                    if(status == 1 ){

                         $('#status_btn').text('الغاء تفعيل ')  ;

                    }else{
                     

                        $('#status_btn').text(' تفعيل  ')  ;

                    }

                    $('#status_btn').attr('status',status);

              }
            });


 
    });

  </script>

<?php $__env->stopSection(); ?>    
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>