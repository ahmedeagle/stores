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

               <?php if(!empty($errors)): ?>
                 <?php if($errors -> has('content')): ?>
                  <div class="alert alert-danger"> <?php echo e($errors -> first('content')); ?></div>
                  <?php endif; ?>
              <?php endif; ?>
     
               <?php if(Session::has('success')): ?>
                   <div class="alert alert-success"> <?php echo e(Session::get('success')); ?></div>
               <?php endif; ?>

                <div class="row">
      <div class="col-md-12">
         <div class="">
            <div class="row timeline-right p-t-35">
               <div class="col-12 col-sm-10 col-xl-11 p-l-5 p-b-35">
                  <div class="card">
                     <div class="card-block post-timelines">
                       
                        <div class="chat-header f-w-600"><?php echo e($username); ?></div>
                        <div class="social-time text-muted"><?php echo e($ticket->created_at); ?></div>
                        <br><br>

                     </div>
                     <div class="card-block">
                        <div class="timeline-details">
                           <div class="chat-header">(<?php echo e($ticket->type_name); ?>)</div>
                           <br>
                           <p class="text-muted"><?php echo e($ticket->title); ?></p>
                        </div>
                     </div>
                     <hr>
                     <div class="card-block b-b-theme b-t-theme social-msg">
                        <a> <i class="icofont icofont-comment text-muted"></i> <span class="b-r-muted">الردود <?php echo e(count($ticket_replys)); ?></span></a>
                     </div>
                     <hr>
                     <br>
                     <div class="card-block user-box">
                        <div class="p-b-30"><span class="f-right">  جميع الردود</span></div>
                        <br>
                     <?php $__currentLoopData = $ticket_replys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="media m-b-20">
                           <div class="media-body b-b-muted social-client-description" style="padding-right: 20px;">
                              <div class="chat-header">
                                  <?php echo e(($reply->FromUser == "0") ?'ادارة الموقع' : $username); ?>

                                  <span class="text-muted" style="padding-right: 5px;"><?php echo e($reply->created_at); ?></span>
                              </div>
                              <br>
                              <p class="text-muted"><?php echo e($reply->reply); ?></p>
                           </div>
                        </div>
                        <hr>
                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                     <br>
                        <div class="media">
                           <div class="media-body" style="padding-right: 20px;">
                              <form action="<?php echo e(route('post.replay')); ?>" method="POST" >
                                  <div class="">
                                    <input type="text" class="form-control" name="content" placeholder="اضافة رد"/>

                                     <input type="hidden" name="ticket_id" value="<?php echo e($ticket->id); ?>" />
                                     <br>
                                    <div class="text-right m-t-20"> <button style="width: 63px" type="submit" class="btn btn-md btn-success">رد</button>  <a href="<?php echo e(route('tickets' , $type)); ?>" class="btn btn-md btn-danger">رجوع</a></div>
                                 </div>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
               
            </div>
        </div><!--End Widget-->
    </div>
</div>
<?php $__env->stopSection(); ?>

 
 
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>