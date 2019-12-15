<?php $__env->startSection('content'); ?>
	<div class="content">
		<div class="col-sm-12">
			<section class="page-heading">
				<div class="col-sm-6">
					<h2>قائمة الصفحات</h2>
				</div><!--End col-md-6-->
				<div class="col-sm-6">
					<ul class="breadcrumb">
						<li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
						<li>الصفحات</li>
						<li class="active">قائمة الصفحات</li>
					</ul>
				</div><!--End col-md-6-->
			</section><!--End page-heading-->
			<div class="spacer-25"></div><!--End Spacer-->
			<div class="widget">
				<div class="widget-title">
					قائمة الصفحات
				</div>
				<div class="widget-content">
					
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
								<th> اسم الصفحة بالعربية</th>
								<th> اسم الصفحة بالإنجليزية</th>
								<th> الحالة</th>
								<th>العمليات</th>
							</tr>
							</thead>
							<tbody>
							<?php if($pages->count()): ?>
								<?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr>
										<td> <?php echo e($page->ar_title); ?> </td>
										<td> <?php echo e($page->en_title); ?> </td>
										<td> <?php echo e($page->active == 1 ? 'مفعلة' : 'غير مفعلة'); ?> </td>
										<td>
											<a href="<?php echo e(route('pages.edit', $page->id)); ?>"
											   class="custom-btn blue-bc">
												<i class="fa fa-pencil"></i>
											</a>
											&nbsp;
											<a href="<?php echo e(route('pages.delete', $page->id)); ?>" class="custom-btn"
											   title="حذف" style="background: #d9534f">
												<i class="fa fa-trash-o"></i>
											</a>

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