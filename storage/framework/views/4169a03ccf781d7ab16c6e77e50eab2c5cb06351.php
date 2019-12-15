<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>عمليات السحب اليومية</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                    <li>سحب الرصيد</li>
                    
                    <li class="active">عمليات السحب اليومية</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
                قائمة عمليات السحب اليومية
            </div>
            <div class="widget-content requests">
                <div class="spacer-25"></div><!--End Spacer-->
                <div class="spacer-25"></div><!--End Spacer-->
                <?php if(Session::has('success')): ?>
                    <div class="alert alert-success">
                        <strong>تم بنجاح !</strong> <?php echo e(Session::get('success')); ?>

                    </div>
                <?php endif; ?>

                <?php if(Session::has('err')): ?>
                    <div class="alert alert-danger">
                        <strong>خطأ !</strong> <?php echo e(Session::get('err')); ?>

                    </div>
                <?php endif; ?>
                <?php if($requests->count()): ?>
                <div class="table-responsive">          
                    <table id="datatable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>الأسم بالكامل</th>
                                <th>رقم الهاتف</th>
                                <th>الرصيد المستحق</th>
                                <th>الرصيد الحالى</th>
                                <th>الأجمالى</th>
                                <th>النوع</th>
                                <th>الحالة</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($request->full_name); ?></td>
                                <td><?php echo e($request->country_code.$request->phone); ?></td>
                                <td><?php echo e($request->due_balance); ?></td>
                                <td><?php echo e($request->current_balance); ?></td>
                                <td><?php echo e($request->current_balance - $request->due_balance); ?></td>
                                <td><?php echo e($request->type); ?></td>
                                <td><?php echo e(($request->status == 1)? 'Pending' : 'Done'); ?></td>
                                <td>
                                    <!-- <button class="custom-btn green-bc">
                                        <i class="fa fa-eye"></i>
                                    </button> -->
                                    <!-- <a href="" class="custom-btn blue-bc">
                                        <i class="fa fa-pencil"></i>
                                    </a> -->
                                    <!-- <button class="custom-btn red-bc">
                                        <i class="fa fa-trash-o"></i>
                                    </button> -->
                                    <?php if($request->status == 1): ?>
                                        <a href="<?php echo e(route('requests.execute', $request->id)); ?>" class="custom-btn blue-bc">
                                            <i class="fa fa-return"></i>
                                           تنفيذ
                                        </a>
                                    <?php else: ?>
                                        تم
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div><!--End Widget-->
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('customJs'); ?>
<script type="text/javascript">
$("body").on("click",".requestSearchBu", function(){
    var from   = $(".from_date").val();
    var to     = $(".to_date").val();
    var name   = $("#name").val();
    var phone  = $("#phone").val();
    var status = $("#status").val();
    var job    = $("#job").val();
    var posted = {'from':from, 'to':to, 'name':name, 'phone':phone, 'status':status, 'job':job};
    $.ajax({
        url: "<?php echo e(route('requests.search')); ?>",
        type:"POST",
        data:posted,
        scriptCharset:"application/x-www-form-urlencoded; charset=UTF-8",
        success: function(result){
            if(result != ''){
                $("#tableBody").html(result);
            }else{
                alert('empty result');
            }
        },
        error: function(){
            alert('Something wrong, try again later');
        }
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>