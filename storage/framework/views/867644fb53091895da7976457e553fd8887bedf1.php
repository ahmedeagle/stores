<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة الفواتير</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                    <li>الفواتير</li>
                    <li class="active">قائمة الفواتير</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة الفواتير
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
                    <a href="<?php echo e(route('invoices.create')); ?>" class="custom-btn red-bc">
                        <i class="fa fa-plus"></i>
                        إضافة   فاتورة
                    </a>
                </div>
                <?php if($invoices->count()): ?>
                <div class="col-sm-12">
                    <div class="ui form">
                        <div class="two fields">
                            <div class="ui field">
                                <label>من :</label>
                                <div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                    <input class="form-control from_date" size="16" value="" readonly="" type="text">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="ui field">
                                 <label>إلى :</label>
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
                                <label>بحث بواسطة الأسم</label>
                                <div class="ui input">
                                    <input class="form-control" name="name" id="name" placeholder="بحث بواسطة الأسم" type="text">
                                </div>
                            </div>
                            <div class="ui field">
                                <label>بحث بواسطة رقم الهاتف</label>
                                <div class="ui input">
                                    <input class="form-control" name="phone" id="phone" placeholder="بحث بواسطة رقم الهاتف" type="text">
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="ui field">
                                <label>نوع الفاتورة</label>
                                <div>
                                    <select id="type" class="form-control" placeholder="Invoice type">
                                        <option value="">الكل</option>
                                        <option value="1">بيع</option>
                                        <option value="2">مسترجع</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <span class="spacer-25"></span>
                    <div class="col-sm-3">
                        <button href="#" class="custom-btn blue-bc invoiceSearchBu">
                            <i class="fa fa-search"></i> 
                            بحث
                        </button>
                    </div>
                </div>
                <div class="spacer-25"></div><!--End Spacer-->
                <div class="table-responsive">          
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>رقم الفاتورة</th>
                                <th>الأسم</th>
                                <th>رقم الهاتف</th>
                                <th>القيمة</th>
                                <th>النوع</th>
                                <th>الوصف</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($invoice->invo_id); ?></td>
                                <td><?php echo e($invoice->name); ?></td>
                                <td><?php echo e($invoice->phone); ?></td>
                                <td><?php echo e($invoice->value); ?></td>
                                <td><?php echo e(($invoice->type == 1)? 'Sale' : 'Return'); ?></td>
                                <td><?php echo e($invoice->invo_desc); ?></td>
                                <td><?php echo e(date('Y-m-d', strtotime($invoice->created_at))); ?></td>
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
$("body").on("click",".invoiceSearchBu", function(){
    var indicator = $(this).find('i');
    var but       = $(this);
    indicator.removeClass('fa-search');
    indicator.addClass('fa-circle-o-notch fa-spin');
    but.attr('disabled',true);
    var from   = $(".from_date").val();
    var to     = $(".to_date").val();
    var name   = $("#name").val();
    var phone  = $("#phone").val();
    var type   = $("#type").val();
    var posted = {'from':from, 'to':to, 'name':name, 'phone':phone, 'type':type};
    $.ajax({
        url: "<?php echo e(route('invoices.search')); ?>",
        type:"POST",
        data:posted,
        scriptCharset:"application/x-www-form-urlencoded; charset=UTF-8",
        success: function(result){
            indicator.addClass('fa-search');
            indicator.removeClass('fa-circle-o-notch fa-spin');
            but.attr('disabled',false);
            if(result != ''){
                $("#tableBody").html(result);
            }else{
                alert('empty result');
            }
        },
        error: function(){
            indicator.addClass('fa-search');
            indicator.removeClass('fa-circle-o-notch fa-spin');
            but.attr('disabled',false);
            alert('Something wrong, try again later');
        }
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>