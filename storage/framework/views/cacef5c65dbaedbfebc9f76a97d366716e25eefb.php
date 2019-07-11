<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12" id="container">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>إضافة موصل</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                    <li>الموصلين</li>
                    <li class="active">إضاف موصل</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div>
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-title">
                    نموذج إضافة موصل
                </div>
                <div class="widget-content">
                    <form class="ui form" enctype="multipart/form-data"   method="post" action="<?php echo e(route('deliveries.store')); ?>">
                        <div class="form-title">من فضلك إملئ البيانات التالية</div>
                        <div class="form-note">[ * ] حقل مطلوب</div>
                        <div class="ui error message"></div>
                        <?php if(!empty($errors->first())): ?>
                            <div class="alert alert-danger">
                                 <strong>خطأ !</strong> لابد من تصحيح الاخطاء الاتية 
                            </div>
                        <?php endif; ?>
                        <?php if(!empty($success)): ?>
                            <div class="alert alert-success">
                                <strong>تم بنجاح !</strong> <?php echo e($msg); ?>

                            </div>
                        <?php endif; ?>

                          <?php if(!empty($faild)): ?>
                            <div class="alert alert-danger">
                                  <?php echo e($faild); ?>

                            </div>
                        <?php endif; ?>
 
                        <div class="widget-title">
                            البيانات الشخصية
                        </div>
                             <div class="ui field <?php if($errors->has('full_name')): ?> error  <?php endif; ?>">
                                <label>الاسم بالكامل  :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="full_name" id="full_name" type="text" placeholder="الاسم بالكامل " value="<?php echo e(old('full_name')); ?>" />
                                </div>

                                 <div class="error-messagen">
                                      <?php if($errors->has('full_name')): ?>  
                                          <?php echo e($errors -> first('full_name')); ?>

                                      <?php endif; ?>
                                   .</div>

                            </div>
                            
                          
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                            بيانات العنوان
                        </div>
                        <div class="two fields">
                            <div class="ui field <?php if($errors->has('country_id')): ?> error  <?php endif; ?> ">
                                <label>الدول :<span class="require">*</span></label>
                                <div class="ui input">
                                    <select class="ui dropdown country" id="country_id" name="country_id">
                                        <option value="">قم بإختيار دوله</option>
                                        <?php if($countries->count()): ?>
                                            <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($country->country_id); ?>"  <?php  if(old('country_id')  == $country->country_id ) { echo 'selected';  }     ?>  ><?php echo e($country->country_ar_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="error-messagen">
                                      <?php if($errors->has('country_id')): ?>  
                                          <?php echo e($errors -> first('country_id')); ?>

                                      <?php endif; ?>
                                  .</div>

                            </div>
                            <div class="ui field cityDiv  <?php if($errors->has('city_id')): ?> error  <?php endif; ?> " >
                                <label>المدن :<span class="require">*</span></label>
                                <select id="cities" class="ui dropdown city" name="city_id">
                                    <option value="">قم بإختيار مدينه</option>
                                </select>
                            </div>
                             <div class="error-messagen">
                                      <?php if($errors->has('city_id')): ?>  
                                          <?php echo e($errors -> first('city_id')); ?>

                                      <?php endif; ?>
                                  .</div>
                        </div>

                         <div>
                         

                           <input id="pac-input" class="controls" type="text" placeholder="أبحث هنا عن  مكانك  علي الخريطه ">

                           <input type="hidden" id="latitudef"  name="latitude">
                           <input type="hidden" id="longitudef" name="longitude">
                              
                                 <div class="error-messagen">
                                      <?php if($errors->has('latitude')): ?>  
                                          <?php echo e($errors -> first('latitude')); ?>

                                      <?php endif; ?>
                                  .</div>
                                       
 

                        <div id="map"></div>

                        </div>


                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                        بيانات التواصل والدخول
                        </div>
                        
                        <div class="ui field" >
                            <label>رقم الجوال : <span class="require">*</span></label>
                            <div class="inline-form">

                                <div  class="<?php if($errors->has('country_code')): ?> error  <?php endif; ?>"> 
                                <div class="form-group col-md-1 col-sm-2">
                                    <input class="form-control country_code" value="<?php echo e(old('country_code')); ?>" placeholder="مثال : 202" maxlength="4" name="country_code" id="country_code" type="text">

                                     <div class="error-messagen">
                                      <?php if($errors->has('country_code')): ?>  
                                          <?php echo e($errors -> first('country_code')); ?>

                                      <?php endif; ?>
                                  .</div>

                                </div>
                                
                                 </div> 
                                       

                             <div class="<?php if($errors->has('country_code')): ?> error  <?php endif; ?>">
                                <div class="form-group col-md-11 col-sm-10">
                                    <input class="form-control phone" value="<?php echo e(old('phone')); ?>" name="phone" id="phone" placeholder="مثال : 05xxxxxxxx" maxlength="11" type="text">
                                      <div class="error-messagen">
                                       <?php if($errors->has('phone')): ?>  
                                          <?php echo e($errors -> first('phone')); ?>

                                      <?php endif; ?>
                                  .</div>

                                </div>

                              
                                </div>  
                            </div>
                        </div>

                        <span class="spacer-25"></span>
                        <div class="two fields">
                            <div class="ui field <?php if($errors->has('password')): ?> error  <?php endif; ?>">
                                <label>كلمة المرور :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="password" id="password" type="password" placeholder="كلممة المرور" value="<?php echo e(old('password')); ?>" />
                                </div>
                                <div class="error-messagen">
                                      <?php if($errors->has('password')): ?>  
                                          <?php echo e($errors -> first('password')); ?>

                                      <?php endif; ?>
                                  .</div>
                            </div>
                            <div class="ui field <?php if($errors->has('password_confirmation')): ?> error  <?php endif; ?>">
                                <label>تاكيد كلمة المرور :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="password_confirmation" id="password_confirmation" type="password" placeholder="تاكيد كلمة المرور" value="<?php echo e(old('password_confirmation')); ?>" />
                                </div>

                                 <div class="error-messagen">
                                      <?php if($errors->has('password_confirmation')): ?>  
                                          <?php echo e($errors -> first('password_confirmation')); ?>

                                      <?php endif; ?>
                                  .</div>
                                 
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                            بيانات السيارة و الاوراق
                        </div>
                        <div>
                            <div class="ui field <?php if($errors->has('car_number')): ?> error  <?php endif; ?>">
                                <label> رقم  السيارة : <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="car_number" id="car_number" type="text" placeholder=" رقم  السيارة" value="<?php echo e(old('car_number')); ?>" />
                                </div>

                                 <div class="error-messagen">
                                <?php if($errors->has('car_number')): ?>  
                                          <?php echo e($errors -> first('car_number')); ?> .
                                 <?php endif; ?>
                             </div>

                            </div>
                        </div>
                        <div class="two fields">
                            <div class=" ui field <?php if($errors->has('license_img')): ?> error  <?php endif; ?>">
                                <label class="custom-file">
                                     صوره الرخصه  : <span class="require">*</span></label>
                                    <input type="file" name="license_img" value="" id="license_img" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>

                                         <div class="error-messagen">
                                              <?php if($errors->has('license_img')): ?>  
                                                  <?php echo e($errors -> first('license_img')); ?>

                                              <?php endif; ?>
                                    .</div>
                            </div>

                           


                            <div class=" ui field <?php if($errors->has('car_form_img')): ?> error  <?php endif; ?>">
                                <label class="custom-file">
                                    استماره السياره  : <span class="require">*</span></label>
                                    <input type="file" name="car_form_img" value="" id="car_form_img" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>
                                 <div class="error-messagen">
                                      <?php if($errors->has('car_form_img')): ?>  
                                          <?php echo e($errors -> first('car_form_img')); ?>

                                      <?php endif; ?>
                            .</div>
                            </div>
                             
                        </div>
                        <div class="two fields">
                            <div class=" ui field <?php if($errors->has('Insurance_img')): ?> error  <?php endif; ?>">
                                <label class="custom-file">
                                    التامين : <span class="require">*</span></label>
                                    <input type="file" name="Insurance_img" value="" id="Insurance_img" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                    <div class="error-messagen">
                                      <?php if($errors->has('Insurance_img')): ?>  
                                          <?php echo e($errors -> first('Insurance_img')); ?>

                                      <?php endif; ?>
                            .</div>
                                </label>
                                 

                            </div>
                            <div class=" ui field <?php if($errors->has('authorization_img')): ?> error  <?php endif; ?>" >
                                <label class="custom-file">
                                    التفويض : <span class="require">*</span></label>
                                    <input type="file" name="authorization_img" value="" id="authorization_img" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>

                                  <div class="error-messagen">
                                      <?php if($errors->has('authorization_img')): ?>  
                                          <?php echo e($errors -> first('authorization_img')); ?>

                                      <?php endif; ?>
                            .</div>
                            </div>

                        </div>
                        <div class="two fields">
                            <div class=" ui field <?php if($errors->has('national_img')): ?> error  <?php endif; ?>">
                                <label class="custom-file">
                                    بطاقه الرقم القومي  : <span class="require">*</span></label>
                                    <input type="file" name="national_img" value="" id="national_img" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>

                                 <div class="error-messagen">
                                      <?php if($errors->has('national_img')): ?>  
                                          <?php echo e($errors -> first('national_img')); ?>

                                      <?php endif; ?>
                            .</div>
                            </div>
                           
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
<script type="text/javascript">
    

    $(document).ready(function(){
        $("body").on("change", ".country", function(){
            var country = $(this).val();
            getCountryCities("<?php echo e(route('country.cities')); ?>", country, 'en', $('#cities'), $('.country_code'), $(".phone"), 2);
        });
    });


</script>


 <script>

    $('#latitudef').val('');
   $('#longitudef').val('');


      // This example adds a search box to a map, using the Google Place Autocomplete
      // feature. People can enter geographical searches. The search box will return a
      // pick list containing a mix of places and predicted search terms.

      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

      function initAutocomplete() {
        var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: 24.740691, lng: 46.6528521 },
          zoom: 13,
          mapTypeId: 'roadmap'
        });

        // Create the search box and link it to the UI element.
        var input = document.getElementById('pac-input');
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(input);

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function() {
          searchBox.setBounds(map.getBounds());
        });

        var markers = [];
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function() {
          var places = searchBox.getPlaces();

          if (places.length == 0) {
            return;
          }

          // Clear out the old markers.
          markers.forEach(function(marker) {
            marker.setMap(null);
          });
          markers = [];

          // For each place, get the icon, name and location.
          var bounds = new google.maps.LatLngBounds();
          places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }
            var icon = {
              url: place.icon,
              size: new google.maps.Size(100, 100),
              origin: new google.maps.Point(0, 0),
              anchor: new google.maps.Point(17, 34),
              scaledSize: new google.maps.Size(25, 25)
            };

            // Create a marker for each place.
            markers.push(new google.maps.Marker({
              map: map,
              icon: icon,
              title: place.name,
              position: place.geometry.location
            }));

 
            $('#latitudef').val(place.geometry.location.lat());
            $('#longitudef').val(place.geometry.location.lng());
 
            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
          });
          map.fitBounds(bounds);
        });
      }

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDKZAuxH9xTzD2DLY2nKSPKrgRi2_y0ejs&libraries=places&callback=initAutocomplete&language=ar&region=SA
         async defer"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('cpanel.layout.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>