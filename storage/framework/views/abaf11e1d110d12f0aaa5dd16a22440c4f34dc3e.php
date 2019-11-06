<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12" id="container">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>إضافة تصنيف جديد</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                    <li>التصنيفات</li>
                    <li class="active">إضافة تصنيف</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div>
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-title">
                  نموذج إضافة تصنيف جديد
                </div>
                <div class="widget-content">
                    <form class="ui form" id="create-category" method="post" action="<?php echo e(route('category.store')); ?>" enctype="multipart/form-data">
                        <div class="form-title">من فضلك إملئ الحقول التالية</div>
                        <div class="form-note">[ * ] حقل مطلوب</div>
                        <div class="ui error message"></div>
                        <?php if(!empty($errors->first())): ?>
                            <div class="alert alert-danger">
                                <strong>خطأ!</strong> <?php echo e($errors->first()); ?>

                            </div>
                        <?php endif; ?>
                        <?php if(Session::has('success')): ?>
                            <div class="alert alert-success">
                                <strong>تم بنجاح !</strong> <?php echo e(Session::get('success')); ?>

                            </div>
                        <?php endif; ?>
                        <div class="widget-title">
                            المعلومات الشخصية
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>التصنيف باللغة الأنجليزية<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="en_name" id="en_name" type="text" placeholder="التصنيف باللغة الأنجليزية" value="<?php echo e(old('en_name')); ?>" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>التصنيف باللغة العربية<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="ar_name" id="ar_name" type="text" placeholder="التصنيف باللغة العربية" value="<?php echo e(old('ar_name')); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class=" ui field">
                            <label class="custom-file">
                              <input type="file" name="category_img" value="" id="category_img" class="custom-file-input">
                              <span class="custom-file-control"></span>
                            </label>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="ui right algined inline field">
                            <button type="submit" class="custom-btn">
                                <i class="fa fa-plus"></i>
                               إضافة
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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>