<?php $__env->startSection('content'); ?>
	<div class="content">
		<div class="col-sm-12" id="container">
			<section class="page-heading">
				<div class="col-sm-6">
					<h2>حذف صفحة</h2>
				</div><!--End col-md-6-->
				<div class="col-sm-6">
					<ul class="breadcrumb">
						<li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
						<li><a href="<?php echo e(route('pages.index')); ?>">الصفحات</a></li>
						<li class="active">حذف صفحة</li>
					</ul>
				</div><!--End col-md-6-->
			</section><!--End page-heading-->
			<div class="spacer-25"></div>
			<div class="col-md-12">
				<div class="widget">
					<div class="widget-title">
						نموذج حذف صفحة
					</div>
					<div class="widget-content">

						<form class="ui form" id="create-category" method="post"
							  action="<?php echo e(route('pages.destroy', $page->id)); ?>" enctype="multipart/form-data">

							<div class="ui error message"></div>
							<?php if(!empty($errors->first())): ?>
								<div class="alert alert-danger">
									<strong>خطأ!</strong> <?php echo e($errors->first()); ?>

								</div>
							<?php endif; ?>

							<div class="one fields">

								<div class="ui field">
									<label>هل تريد بالفعل حذف هذه الصفحة ؟ <b> <?php echo e($page->ar_title); ?></b></label>
									<div class="ui input">

										<button type="submit" class="custom-btn"
												style="background-color: #d9534f !important;">
											<i class="fa fa-trash-o"></i>
											حذف
										</button>

										<a href="<?php echo e(route('pages.index')); ?>" class="custom-btn blue-bc">
											<i class="fa fa-backward"></i>
											رجوع
										</a>

									</div>
								</div>

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