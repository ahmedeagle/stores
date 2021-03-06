<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>الأرصدة</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                    <li class="active">الأرصدة</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
              قائمة الأرصدة
            </div>
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
            <div class="widget-content requests">
                <div class="col-sm-12">
                    <div class="ui form">
                        <!-- <div class="two fields">
                            <div class="ui field">
                                <label>من ::</label>
                                <div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                    <input class="form-control from_date" size="16" value="" readonly="" type="text">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="ui field">
                                 <label>إلى ::</label>
                                <div class="form-group">
                                    <div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                        <input class="form-control to_date" size="16" value="" readonly="" type="text">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="two fields">
                            <div class="ui field">
                                <label>بحث بواسطة الأسم</label>
                                <div class="ui input">
                                    <input class="form-control" name="name" id="name" placeholder="بحث بواسطة الأسم" type="text">
                                </div>
                            </div>
                            <div class="ui field">
                                <label>بحث بواسطة الهاتف</label>
                                <div class="ui input">
                                    <input class="form-control" name="phone" id="phone" placeholder="بحث بواسطة الهاتف" type="text">
                                </div>
                            </div>
                        </div>
                        <!-- <div class="two fields"> -->
                        <div class="two fields">
                            <!-- <div class="ui field">
                                <label>Request status</label>
                                <div>
                                    <select id="status" class="form-control" placeholder="Request status">
                                        <option value="">Both</option>
                                        <option value="1">Pending</option>
                                        <option value="2">Done</option>
                                    </select>
                                </div>
                            </div> -->
                            <div class="ui field">
                                <label>الوظيفة</label>
                                <div>
                                    <select id="job" class="form-control">
                                        <option value="">الكل</option>
                                        <option value="provider">مقدم خدمة</option>
                                        <option value="delivery">موصل</option>
                                      <!--  <option value="marketer">مسوق</option> -->
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <button href="#" class="custom-btn blue-bc balanceSearchBu">
                            <i class="fa fa-search"></i> 
                            بحث
                        </button>
                    </div>
                </div>
                <div class="spacer-25"></div><!--End Spacer-->
                <?php if($balances->count()): ?>
                <div class="table-responsive">          
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>الأسم بالكامل</th>
                                <th>رقم  الهاتف</th>
                                <th>الرصيد المستحق</th>
                                <th>الرصيد الحالى</th>
                                <th>الأجمالى</th>
                                <th>النوع</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <?php $__currentLoopData = $balances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $balance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($balance->full_name); ?></td>
                                <td><?php echo e($balance->country_code.$balance->phone); ?></td>
                                <td><?php echo e($balance->due_balance); ?></td>
                                <td><?php echo e($balance->current_balance); ?></td>
                                <td><?php echo e($balance->current_balance - $balance->due_balance); ?></td>
                                <td><?php echo e($balance->type); ?></td>
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
$("body").on("click",".balanceSearchBu", function(){
    // var from   = $(".from_date").val();
    // var to     = $(".to_date").val();
    var name   = $("#name").val();
    var phone  = $("#phone").val();
    // var status = $("#status").val();
    var job    = $("#job").val();
    // var posted = {'from':from, 'to':to, 'name':name, 'phone':phone, 'status':status, 'job':job};
    var posted = {'name':name, 'phone':phone, 'job':job};
    $.ajax({
        url: "<?php echo e(route('balances.search')); ?>",
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