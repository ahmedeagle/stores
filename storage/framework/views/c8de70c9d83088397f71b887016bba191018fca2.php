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
                    <a href="<?php echo e(route('offers.status', 0)); ?>" class="custom-btn red-bc">
                         العروض الجديده 
                    </a>

                    <a href="<?php echo e(route('offers.status',1)); ?>" class="custom-btn red-bc">
                         العروض  الموافق عليها 
                    </a>

                     <a href="<?php echo e(route('offers.status',2)); ?>" class="custom-btn red-bc">
                     	العروض المدفوعه 
                     </a>


                     <a href="<?php echo e(route('offers.status',3)); ?>" class="custom-btn red-bc">
                         العروض المنتهيه  والملغاه 
                    </a>
 

                     <a href="<?php echo e(route('offers.status',4)); ?>" class="custom-btn red-bc">
                           العروض المنشوره 
                    </a>
 



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
                                <th></th>
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
                                        
                                         <td>
                                      
            	<?php if($type == 0 ): ?>

            	 <?php if(! $offer -> status == '1'): ?>
            	    <a title="موافقه "  id="acceptOffer" data_provider="<?php echo e($offer -> provider_id); ?>" data_id="<?php echo e($offer -> offer_id); ?>" data_status="1" href="" class="offerAcceptance custom-btn blue-bc ">
	                    <i class="fa fa-check" aria-hidden="true"></i>

	                </a>
	             <?php endif; ?>   
                 
                 <?php if(! $offer -> expire == '1'): ?>       
	                <a title="رفض "  id="refuseOffer" data_provider="<?php echo e($offer -> provider_id); ?>"  data_id="<?php echo e($offer -> offer_id); ?>"  data_status="0" href="" class="offerAcceptance custom-btn blue-bc ">
	                    <i class="fa fa-close" aria-hidden="true"></i>
	                </a>
	              <?php endif; ?>
	                
              <?php endif; ?>

              <?php if($type == 2): ?>

                <?php if(! $offer -> publish == 1): ?>
                
                    <a title="نشر العرض "   data_provider="<?php echo e($offer -> provider_id); ?>" data_id="<?php echo e($offer -> offer_id); ?>" data_status="1" href="" class="publishingOffer custom-btn blue-bc">
                                                <i class="fa fa-bullhorn" aria-hidden="true"></i>
                    </a>

               <?php endif; ?>

             <?php endif; ?>  


                <?php if($type == 4): ?>

                
                 <?php if( $offer -> publish == 1): ?> 
                    <a title="وقف الاعلان " href="" id="publishOffer"   data_provider="<?php echo e($offer -> provider_id); ?>" data_id="<?php echo e($offer -> offer_id); ?>" data_status="0" class="unpublishingOffer custom-btn blue-bc">
                                                <i class="fa fa-close" aria-hidden="true"></i>
                    </a>

                 <?php endif; ?>   

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



     $(document).on('click','.offerAcceptance',function(e){

     	$('#alert_success').empty().hide();
        $('#alert_danger').empty().hide();

          e.preventDefault();

         var status       =  $(this).attr('data_status');
         var offer_id     =  $(this).attr('data_id');
         var provider_id  =  $(this).attr('data_provider');
 

         $.ajax({
     
              type :'post',

              url  :"<?php echo e(route('offers.acceptnace')); ?>",

              data :{
     
                   '_token'            :   $('input[name="_token"]').val(),
                   'offer_id'          :   offer_id,
                   'status'            :   status, 
                   'provider_id'       :   provider_id,
       
                },

              success:function(data)
              {                                          

                    var status = data.status;                          

                    if(status == 1){

                         $('#acceptOffer').hide();

                    }

                    else if(status == 0)
                    {

                         $('#refuseOffer').hide();
                    }

                  if(data.error){



                   $('#alert_danger').show().empty().append(data.error);

                  }
  

                if(data.success){
                      
                      $('#alert_success').show().empty().append(data.success);
                }

 
                setTimeout(location.reload.bind(location), 2000);

                        

              }

                   

               
            });


 
    });



      $(document).on('click',".publishingOffer,.unpublishingOffer",function(e){

     	$('#alert_success').empty().hide();
        $('#alert_danger').empty().hide();

          e.preventDefault();

         var status       =  $(this).attr('data_status');
         var offer_id     =  $(this).attr('data_id');
         var provider_id  =  $(this).attr('data_provider');
 

         $.ajax({
     
              type :'post',

              url  :"<?php echo e(route('offers.publishing')); ?>",

              data :{
     
                   '_token'            :   $('input[name="_token"]').val(),
                   'offer_id'          :   offer_id,
                   'status'            :   status, 
                   'provider_id'       :   provider_id,
       
                },

              success:function(data)
              {                                          

                    var status = data.status;                          

                    if(status == 1){

                         $('.publishingOffer').hide();
                         $('.unpublishingOffer').show();

                    }

                    else if(status == 0)
                    {
                         
                         $('.publishingOffer').show();
                         $('.unpublishingOffer').hide();
                    }

                  if(data.error){



                   $('#alert_danger').show().empty().append(data.error);

                  }
  

                if(data.success){
                      
                      $('#alert_success').show().empty().append(data.success);
                }

 
			                setTimeout(location.reload.bind(location), 2000);
                        

	             }

	                   

               
            });


 
    });



</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>