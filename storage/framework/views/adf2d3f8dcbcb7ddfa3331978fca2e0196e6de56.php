<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة التصنفيات</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                    <li>التصنفيات</li>
                    <li class="active">قائمة التصنيفات</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة التصنيفات
            </div>
            <div class="widget-content">
                <div class="col-sm-12">
                    <a href="<?php echo e(route('category.create')); ?>" class="custom-btn red-bc">
                        <i class="fa fa-plus"></i>
                        إضافة تصنيف جديد
                    </a>
                </div>
                <div class="spacer-25"></div><!--End Spacer-->
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
                                <th> الصورة </th>
                                <th> اسم التصنيف بالانجليزية</th>
                                <th> إسم التصنيف بالعربية </th>
                                <th> حالة  النشر ؟ </th>
                                <th>  </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($categories->count()): ?>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="width-90">
                                            <a class="img-popup-link" href="<?php echo e(env('APP_URL').'/public/categoriesImages/'.$category->cat_img); ?>">
                                                <img src="<?php echo e(env('APP_URL').'/public/categoriesImages/'.$category->cat_img); ?>" class="table-img">
                                            </a>
                                        </td>
                                        <td> <?php echo e($category->cat_en_name); ?> </td>
                                        <td> <?php echo e($category->cat_ar_name); ?> </td>
                                        <td> <?php echo e(($category->publish == 1)? 'Published' : 'Deleted'); ?> </td>
                                        <td>
                                            <a href="<?php echo e(route('category.edit', $category->cat_id)); ?>" class="custom-btn blue-bc">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <?php if($category->publish == 1): ?>
                                                <a href="<?php echo e(route('publishing', ['id' => $category->cat_id, 'val' => 2, 'proccess' => 'Deleted', 'col' => 'cat_id', 'table' => 'categories'])); ?>" class="custom-btn red-bc">
                                                    <i class="fa fa-trash-o"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo e(route('publishing', ['id' => $category->cat_id, 'val' => 1, 'proccess' => 'Published', 'col' => 'cat_id', 'table' => 'categories'])); ?>" class="custom-btn red-bc">
                                                    <i class="fa fa-plus"></i>
                                                </a>
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
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>