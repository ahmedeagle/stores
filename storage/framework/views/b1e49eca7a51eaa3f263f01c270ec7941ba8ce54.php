<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>التعليقات اليومية</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                   <li>التعليقات</li>
                    <li class="active">التعليقات اليومية</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
             التعليقات اليومية
            </div>
            <div class="widget-content requests">
                <?php if(Session::has('success')): ?>
                    <div class="alert alert-success">
                        <strong>تم بنجاح !</strong> <?php echo e(Session::get('success')); ?>

                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                <?php endif; ?>
                <?php if(Session::has('err')): ?>
                    <div class="alert alert-danger">
                        <strong>خطأ !</strong> <?php echo e(Session::get('err')); ?>

                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                <?php endif; ?>
                
                <?php if($comments->count()): ?>
                <div class="table-responsive"> 
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="width-90">صورة  المنتج </th>
                                <th>إسم  المنتج </th>
                                <th>إسم المستخدم</th>
                                <th>رقم الهاتف</th>
                                <th>التلعيق</th>
                                <th>التاريخ</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="width-90">
                                    <a class="img-popup-link" href="<?php echo e($comment -> product_image); ?>">
                                        <img src="<?php echo e($comment -> product_image); ?>" class="table-img">
                                    </a>
                                </td>
                                <td><?php echo e($comment->title); ?></td>
                                <td><?php echo e($comment->full_name); ?></td>
                                <td><?php echo e($comment->phone); ?></td>
                                <td><?php echo e($comment->comment); ?></td>
                                <td><?php echo e($comment->created); ?></td>
                                <td>
                                    <button data-id="<?php echo e($comment->id); ?>" class="custom-btn red-bc deleteMeal">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                <div class="col-sm-12">
                    <?php echo e($comments->links()); ?>

                </div>
            </div><!--End Widget-content -->
        </div><!--End Widget-->
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('customJs'); ?>
<script type="text/javascript">
$(document).ready(function(){
    $('body').on('click', '.deleteMeal', function(){
        if(confirm("Are you sure?")){
            var id = $(this).attr('data-id');
            var url = "<?php echo e(route('comments.delete', ['id' => ':id'])); ?>";

            url = url.replace(':id', id);

            window.location.href = url;
        }
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>