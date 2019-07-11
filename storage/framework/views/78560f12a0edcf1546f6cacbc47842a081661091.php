<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة التعليقات</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                    <li>التعليقات</li>
                    <li class="active">قائمة التعليقات</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
            قائمة التعليقات
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
                        <strong>خطأ!</strong> <?php echo e(Session::get('err')); ?>

                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                <?php endif; ?>
                <div class="col-sm-12">
                    <div class="ui form">
                        <div class="two fields">
                            <div class="ui field">
                                <label>من : </label>
                                <div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                    <input class="form-control from_date" size="16" value="" readonly="" type="text">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="ui field">
                                <label>إلى : </label>
                                <div class="form-group">
                                    <div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                        <input class="form-control to_date" size="16" value="" readonly="" type="text">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>المستخدم : </label>
                                <div>
                                    <select id="users" class="users-select2 form-control" placeholder="User full name">
                                        <option value="">إختار المستخدم</option>
                                        <?php if($users->count()): ?>
                                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($user->user_id); ?>"><?php echo e($user->full_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="ui field">
                                <label>رقم التليفون :</label>
                                <div class="ui input">
                                    <input class="form-control" placeholder="مثال : 01090353855" type="text" id="phone" name="phone">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <button href="#" class="custom-btn blue-bc searchBu">
                            <i class="fa fa-search"></i> 
                           بحث
                        </button>
                    </div>
                </div>
                <span class="spacer-25"></span>
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
    $('body').on('click', '.searchBu', function(){
        var from     = $('.from_date').val();
        var to       = $('.to_date').val();
        var user     = $('#users').val();
        var phone    = $("#phone").val();
        var url      = "<?php echo e(route('comments.search', ['from' => ':frm', 'to' => ':to', 'user' => ':user', 'phone'=> ':phone'])); ?>";
        if(from != null && from != ""){
            url = url.replace(':frm', from);
        }else{
            url = url.replace(':frm', null);
        }

        if(to != null && to != ""){
            url = url.replace(':to', to);
        }else{
            url = url.replace(':to', null);
        }

        if(user != null && user != ""){
            url = url.replace(':user', user);
        }else{
            url = url.replace(':user', null);
        }

        if(phone != null && phone != ""){
            url = url.replace(':phone', phone);
        }else{
            url = url.replace(':phone', null);
        }
        window.location.href = url;
    });

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