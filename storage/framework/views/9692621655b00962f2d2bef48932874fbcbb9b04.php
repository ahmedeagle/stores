<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12" id="container">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>إنشاء فاتورة</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                    <li>الفواتير</li>
                    <li class="active">إنشاء فاتورة</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div>
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-title">
                    نموذج إنشاء فاتورة
                </div>
                <div class="widget-content">
                    <form class="ui form" id="create-invoice" method="post" action="<?php echo e(route('invoices.store')); ?>">
                        <div class="form-title">من فضلك إملئ الحقول التالية </div>
                        <div class="form-note">[ * ] حقل مطلوب</div>
                        <div class="ui error message"></div>
                        <?php if(!empty($errors->first())): ?>
                            <div class="alert alert-danger">
                                <strong>خطأ !</strong> <?php echo e($errors->first()); ?>

                            </div>
                        <?php endif; ?>
                        <?php if(!empty($msg)): ?>
                            <div class="alert alert-success">
                                <strong>تم بنجاح !</strong> <?php echo e($msg); ?>

                            </div>
                        <?php endif; ?>
                        <div class="two fields">
                            <div class="ui field">
                                <label>الأسم<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="name" id="name" type="text" placeholder="الأسم" value="<?php echo e(old('name')); ?>" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>رقم الهاتف<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="phone" id="phone" type="text" placeholder="رقم الهاتف" value="<?php echo e(old('phone')); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>القيمة:<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="value" id="value" type="text" placeholder="القيمة" value="<?php echo e(old('value')); ?>" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>نوع الفاتورة:<span class="require">*</span></label>
                                <select id="type" class="ui dropdown" name="type">
                                    <option value="">إختار</option>
                                    <option value="1">بيع</option>
                                    <option value="2">مسترجع</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <div class="ui field">
                                <label>الوصف <span class="require">*</span></label>
                                <textarea class="form-control" name="desc" rows="5" placeholder="الوصف"><?php echo e(old('desc')); ?></textarea>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="ui right algined inline field">
                            <button type="submit" class="custom-btn">
                                <i class="fa fa-plus"></i>
                                إنشاء
                            </button>
                        </div>
                    </form>
                </div><!-- end widget-content -->
            </div><!-- end widget -->
        </div>
    </div><!-- end container -->
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('customJs'); ?>
<script>
  var map;
  function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        geolocation: true,
        center: {
                    lat: 24.1796095, 
                    lng: 38.2744758
                },
        zoom: 7,
        searchbox: true,
        cluster: true,
        geocoder: true
    });
    
  }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>