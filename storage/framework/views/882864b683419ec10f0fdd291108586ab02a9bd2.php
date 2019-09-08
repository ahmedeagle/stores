<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>تعديل بيانات المستخدم</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li> 
                    <li><a href="<?php echo e(route('user.show')); ?>">المستخدمين</a></li> 
                    <li class="active">تعديل بيانات المستخدم</li> 
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
    </div>
    <div class="spacer-25"></div>
    <div class="col-md-3">
        <div class="profile-card">
            <div class="profile-img">

                 <img src="<?php echo e((!empty($user) && !empty($user->profile_pic))? env('APP_URL').'/public/userProfileImages/'.$user->profile_pic : url('admin-assets/images/avatar_big_ic.png')); ?>" alt="">
            </div><!--End profile-img-->
            <div class="profile-name">
                
            </div><!--End profile-name-->
            <div class="spacer-20"></div>
            <div class="profile-menu">
                <ul>
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-user"></i>
                            <?php echo e((!empty($user) && !empty($user->full_name))? $user->full_name : 'Update user'); ?>

                        </a>
                    </li>
                    <!-- <li>
                        <a href="account_setting.html">
                            <i class="fa fa-cogs"></i>
                            account setting   
                        </a>
                    </li>
                    <li>
                        <a href="message.html">
                            <i class="fa fa-envelope-o"></i>
                            message   
                        </a>
                    </li>
                    <li>
                        <a href="notifactions.html">
                            <i class="fa fa-bell-o"></i>
                            notifactions
                        </a>
                    </li> -->
                </ul>
            </div><!--End profile-menu-->
        </div><!--End profile-card-->
    </div><!--End col-md-3-->
    <div class="col-md-9">
        <div class="profile-content profile-account-setting">
            <div class="profile-widget-title pattern-bg">
                نموذج تعديل بيانات المستخدم
            </div><!--End profile-widget-title-->
            <div class="profile-widget-content">
                <?php if(Session::has('success')): ?>
                    <div class="alert alert-success">
                        <strong>تم بنجاح !</strong> <?php echo e(Session::get('success')); ?>

                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                <?php endif; ?>

                <?php if(Session::has('errors')): ?>
                    <div class="alert alert-danger">
                        <strong>خطأ !</strong> <?php echo e(Session::get('errors')); ?>

                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                <?php endif; ?>
                <?php if($user != NULL): ?>
                <form id="user-form" class="form ui" method="post" action="<?php echo e(route('user.update')); ?>">
                    <input type="hidden" value="<?php echo e($user->user_id); ?>" name="user_id" />
                    <div class="form-title">من فضلك إملى الحقول التالية</div>
                    <div class="form-note">[ <span class="require">*</span> ] حقل مطلوب</div>
                    <div class="ui error message"></div>
                    <!-- <div class="form-group"> -->
                    <div class="ui field">
                        <label>
                            الأسم بالكامل :<span class="require">*</span>
                        </label><!--End label-->
                        <div class="ui input">
                            <input class="form-control" type="text" value="<?php echo e((!empty(old('full_name')))? old('full_name') : $user->full_name); ?>" name="full_name">
                        </div>
                    </div><!--End form-group-->
                    <div class="ui field">
                        <label>
                           البريد الألكترونى : <span class="require">*</span>
                        </label><!--End label-->
                        <div class="ui input">
                            <input class="form-control" type="email" value="<?php echo e((!empty(old('email')))? old('email') : $user->email); ?>" name="email">
                        </div>
                    </div><!--End form-group-->
                    <div class="ui field">
                        <label>
                           الدولة :  <span class="require">*</span>
                        </label>
                        <div class="ui input">
                            <select class="country" name="country">
                                <option value="">إختار الدولة : </option>
                                <?php if($countries->count()): ?>
                                    <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option <?php echo e(($country->country_id == $country_id)? 'selected' : ''); ?> value="<?php echo e($country->country_id); ?>"><?php echo e($country->country_en_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div><!--End form-widget-->
                    <div class="ui field">
                        <label>
                           المدينة :  <span class="require">*</span>
                        </label><!--End label-->
                        <div class="ui input cityDiv">
                            <select id="cities" class="city" name="city">
                                <?php if($cities->count()): ?>
                                    <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($city->city_id); ?>"  <?php echo e(($city->city_id == $user->city_id)? 'selected' : ''); ?>><?php echo e($city->city_en_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div><!--End form-group-->
                    <div class="inline-form ui field">
                        <label class="col-md-2 col-sm-2">
                            رقم الهاتف : <span class="require">*</span>
                        </label>
                        <div class="ui input col-md-2 col-sm-3">
                            <input class="form-control country_code" value="<?php echo e((!empty(old('country_code')))? old('country_code') : $user->country_code); ?>" placeholder="متال : 0200" type="text" name="country_code">
                        </div>
                        <div class="ui input col-md-8 col-sm-7">
                            <input class="form-control phone" value="<?php echo e((!empty(old('phone')))? old('phone') : $user->phone); ?>" placeholder="مثال : 01090353855" type="text" name="phone">
                        </div>
                    </div><!-- End inline-from -->
                    <span class="spacer-20"></span>
                    <hr>
                    <div class="spacer-20"></div>
                    <div class="ui field">
                        <label>
                           كلمة المرور :  <span class="require">*</span>
                        </label><!--End label-->
                        <div class="ui input">
                            <input class="form-control" type="text" value="<?php echo e(old('password2')); ?>" name="password2">
                        </div>
                    </div><!--End form-group-->
                    <div class="ui field">
                        <label>
                           تأكيد كلمة المرور :
                        </label><!--End label-->
                        <div class="ui input">
                            <input class="form-control" type="text" value="<?php echo e(old('passwordcon2')); ?>" name="passwordcon2">
                        </div>
                    </div><!--End form-group-->


                       <div class="ui field">
                        <label>
                            الحالة  :  <span class="require">*</span>
                        </label>
                        <div class="ui input">
                            <select   name="status">
                                <option value="">إختار  حاله  : </option>
                               
                                  <option value="1" <?php echo e($user-> status == 1 ? 'selected' : ''); ?>>مفعل 
                                  </option>

                                  <option value="0" <?php echo e($user-> status == 0 ? 'selected' : ''); ?>> غير مفعل  
                                  </option>
                                   
                            </select>
                        </div>
                    </div><!--End form-widget-->


                    <hr>
                    <div class="spacer-20"></div>
                    <div class="ui form">
                        <button type="submit" class="custom-btn">
                            <i class="fa fa-save"></i>
                           تعديل
                        </button>
                    </div>
                </form><!-- End form-->
                <?php endif; ?>
            </div><!--profile-->                                                            
        </div>
    </div><!--End col-md-9-->
</div><!--End Content-->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('customJs'); ?>
<script type="text/javascript">
/*Select With Search
============================*/
$(document).ready(function () {
    "use strict";
    $(".country").select2();
    $("body").on("change", ".country", function(){
        var country = $(this).val();
        getCountryCities("<?php echo e(route('country.cities')); ?>", country, 'en', $('#cities'), $('.country_code'), $(".phone"), 1);
    });
 })
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>