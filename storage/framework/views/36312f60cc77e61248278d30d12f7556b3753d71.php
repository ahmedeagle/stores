<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>تقييمات الموصلين</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                    <li><a href="#">التقييمات</a></li>
                    <li class="active">تقييمات الموصلين</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               تقييمات الموصلين
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
                        <strong>خطا !</strong> <?php echo e(Session::get('err')); ?>

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
                                <label>المستخدم</label>
                                <div>
                                    <select id="users" class="users-select2 form-control">
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
                                <label>رقم هاتف المستخدم : </label>
                                <div class="ui input">
                                    <input class="form-control" placeholder="مثال : 01090353855" type="text" id="phone" name="phone">
                                </div>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>الموصلين</label>
                                <div>
                                    <select id="deliveries" class="users-select2 form-control">
                                        <option value="">إختار الموصل</option>
                                        <?php if($deliveries->count()): ?>
                                            <?php $__currentLoopData = $deliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $delivery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($delivery->delivery_id); ?>"><?php echo e($delivery->full_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="ui field">
                                <label>رقم هاتف الموصل : </label>
                                <div class="ui input">
                                    <input class="form-control" placeholder="مثال : 01090353855" type="text" id="delivery_phone" name="delivery_phone">
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
                <?php if($evaluations->count()): ?>
                <div class="table-responsive"> 
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                 <th>إسم المستخدم</th>
                                <th>رقم الهاتف</th>
                                <th class="width-90">صورة الموصل</th>
                                <th>إسم الموصل</th>
                                <th>رقم الموصل</th>
                                <th>كود الطلب</th>
                                <th>التقييم العام</th>
                                <th>التعليق</th>
                                <th>التاريخ</th>
                                <!-- <th></th> -->
                            </tr>
                        </thead>
                        <tbody id="result">
                            <?php $__currentLoopData = $evaluations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $evaluation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                               
                                <td><?php echo e($evaluation->full_name); ?></td>
                                <td><?php echo e($evaluation->phone); ?></td>
                                <td class="width-90">
                                    <a class="img-popup-link" href="<?php echo e($evaluation->delivery_pic); ?>">
                                        <img src="<?php echo e($evaluation->delivery_pic); ?>" class="table-img">
                                    </a>
                                </td>
                                <td><?php echo e($evaluation->delivery_name); ?></td>
                                <td><?php echo e($evaluation->delivery_phone); ?></td>
                                <td><?php echo e($evaluation->code); ?></td>
                                <td><?php echo e($evaluation->rating); ?></td>
                                <td><?php echo e($evaluation->comment); ?></td>
                                <td><?php echo e($evaluation->created); ?></td>
                                <!-- remember -->
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="table-responsive"> 
                    <table class="table table-bordered table-hover" style="display:none">
                        <thead>
                            <tr>
                                <th class="width-90">صورة المستخدم</th>
                                <th>إسم المستخدم</th>
                                <th>رقم الهاتف</th>
                                <th class="width-90">صورة الموصل</th>
                                <th>إسم الموصل</th>
                                <th>رقم الموصل</th>
                                <th>كود الطلب</th>
                                <th>التقييم العام</th>
                                <th>التعليق</th>
                                <th>التاريخ</th>
                                <!-- <th></th> -->
                            </tr>
                        </thead>
                        <tbody id="result">
                        </tbody>
                    </tabl>
                </div>
                <?php endif; ?>
                <div class="col-sm-12" id="pagi">
                    <?php echo e($evaluations->links()); ?>

                </div>
            </div><!--End Widget-content -->
        </div><!--End Widget-->
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('customJs'); ?>
<script type="text/javascript">

    $("body").on("click", ".searchBu", function(){
        var indicator = $(this).find('i');
        var but       = $(this);
        indicator.removeClass('fa-search');
        indicator.addClass('fa-circle-o-notch fa-spin');
        but.attr('disabled',true);
        var user           = $("#users").val();
        var user_phone     = $("#phone").val();
        var delivery       = $("#deliveries").val();
        var delivery_phone = $("#delivery_phone").val();
        var from_date      = $(".from_date").val();
        var to_date        = $(".to_date").val();
        var type           = 'delivery';

        var data = {'user':user, 'user_phone':user_phone, 'subject':delivery, 'subject_phone':delivery_phone, 'from':from_date, 'to':to_date, 'type':type};
        $.ajax({
            url:"<?php echo e(route('evaluations.search')); ?>",
            type:"POST",
            data:data,
            scriptCharset:"application/x-www-form-urlencoded; charset=UTF-8",
            success: function(result){
                $("#result").html(result);
                $("#pagi").html("");
                $(".table").css('display', 'table');
                indicator.addClass('fa-search');
                indicator.removeClass('fa-circle-o-notch fa-spin');
                but.attr('disabled',false);
            },
            error: function(){
                indicator.addClass('fa-search');
                indicator.removeClass('fa-circle-o-notch fa-spin');
                but.attr('disabled',false);
                alert('حدث خطأ ما من فضلك حاول مره اخرى');
            }
        });
    });

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>