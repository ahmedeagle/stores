<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="col-sm-12" id="container">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>إضافة مقدم خدمة </h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
                    <li>مقدمى الخدمة</li>
                    <li class="active">إضافة مقدم خدمة</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div>
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-title">
                  نموذج إضافة مقدم خدمة
                </div>
                <div class="widget-content">
                    <form class="ui form" id="create-providerr" method="post" action="<?php echo e(route('provider.update')); ?>" enctype="multipart/form-data">

                        <input type="hidden" name="provider_id" value="<?php echo e($provider -> provider_id); ?>">
                        <div class="form-title">من فضلك إملئ الحقول التالية </div>
                        <div class="form-note">[ * ] حقل مطلوب</div>
                        <div class="ui error message"></div>
                       
                        <?php if(!empty($errors->first())): ?>
                            <div class="alert alert-danger">
                                <strong>خطأ !</strong> لابد من تصحيح الاخطاء الاتية 
                            </div>
                        <?php endif; ?>


                        <?php if(!empty($msg)): ?>
                            <div class="alert alert-success">
                                <strong>تم بنجاح !</strong> <?php echo e($msg); ?>

                            </div>
                        <?php endif; ?>



                        <div class="widget-title">
                            المعلومات الشخصية
                        </div>
                        <div class="two fields">
                            <div class="ui field <?php if($errors->has('full_name')): ?> error  <?php endif; ?>">
                                <label> الاسم بالكامل :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input   name="full_name" id="full_name" type="text" placeholder=" الاسم بالكامل " value="<?php echo e($provider -> full_name); ?>" />
 
                                     
                                </div>
                                  <div class="error-messagen">
                                      <?php if($errors->has('full_name')): ?>  
                                          <?php echo e($errors -> first('full_name')); ?>

                                      <?php endif; ?>
                                  .</div>
                                       
                            </div>
                            <div class="ui field <?php if($errors->has('store_name')): ?> error  <?php endif; ?>">
                                <label>  اسم المتجر  :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="store_name" id="store_name" type="text" placeholder="الأسم المتجر " value="<?php echo e($provider -> store_name); ?>" />

                                </div>
                                 <div class="error-messagen">
                                      <?php if($errors->has('store_name')): ?>  
                                          <?php echo e($errors -> first('store_name')); ?>

                                      <?php endif; ?>
                                  .</div>
                                  
                            </div>
                        </div>

  
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                           تفاصيل العنوان
                        </div>
                        <div class="two fields">
                            <div class="ui field <?php if($errors->has('country_id')): ?> error  <?php endif; ?>" >
                                <label>الدول :<span class="require">*</span></label>
                                <div class="ui input">
                                    <select class="ui dropdown country" id="countries" name="country_id">
                                      
                                       <option value="">اختر دولة </option>
                                       <?php if(isset($countries) && $countries -> count() > 0): ?>
                                          <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                              
                                                <option id="<?php echo e($country -> country_id); ?>" value="<?php echo e($country->country_id); ?>" 
                                                        <?php  if($country -> choosen   == 1 ) { echo 'selected';  }     ?> 

                                                    ><?php echo e($country->country_ar_name); ?></option>

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
                            <div class="ui field cityDiv <?php if($errors->has('city_id')): ?> error  <?php endif; ?>">
                                <label>المدن :<span class="require">*</span></label>
                                <select id="cities" class="ui dropdown city" name="city_id">
                                    <option value="">إختر مدينه</option>
                                     <?php if(isset($cities) && $cities -> count() > 0): ?>

                                     <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option id="<?php echo e($city -> city_id); ?>" value="<?php echo e($city -> city_id); ?>" 
                                                        <?php  if($city -> choosen   == 1 ) { echo 'selected';  }     ?> 

                                                    ><?php echo e($city -> city_ar_name); ?></option>

                                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                       <?php endif; ?>

                                </select>
                            </div>
                             <div class="error-messagen">
                                      <?php if($errors->has('city_id')): ?>  
                                          <?php echo e($errors -> first('city_id')); ?>

                                      <?php endif; ?>
                                  .</div>
 
                        </div>
                        <div>
                         

                           <input id="pac-input" class="controls" type="text" placeholder="أبحث هنا عن مكانك  علي الخريطه ">

                           <input type="hidden" id="latitudef"  value="<?php echo e($provider -> latitude); ?>" name="latitude">
                           <input type="hidden" id="longitudef" value="<?php echo e($provider -> longitude); ?>" name="longitude">
                              
                                 <div class="error-messagen">
                                      <?php if($errors->has('latitude')): ?>  
                                          <?php echo e($errors -> first('latitude')); ?>

                                      <?php endif; ?>
                                  .</div>
                                       


                        <div id="map"></div>

                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">صور للاوراق المطلوبة
                        </div>
                         
                             <div class=" ui field <?php if($errors->has('commercial_photo')): ?> error  <?php endif; ?>">
                                <label for="commercial_photo"> صوره السجل التجاري   </label>
                                <input name="commercial_photo" id="commercial_photo" type="file" />
                                
                                   <div class="error-messagen">
                                      <?php if($errors->has('commercial_photo')): ?>  
                                          <?php echo e($errors -> first('commercial_photo')); ?>

                                      <?php endif; ?>
                                  .</div>
                                       

                            </div>
                           
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                            معلومات التواصل و الدخول
                        </div>
                        
                        <div class="inline-form ui field">
                            <label class="col-md-2 col-sm-2">
                               رقم الهاتف: <span class="require">*</span>
                            </label>
                            <div class="ui input col-md-2 col-sm-3 <?php if($errors->has('country_code')): ?> error  <?php endif; ?>">
                                <input class="form-control country_code" id="country_code" value="<?php echo e($provider -> country_code); ?>" placeholder="مثال : 996,20" type="text" name="country_code">
                                   <div class="error-messagen">
                                      <?php if($errors->has('country_code')): ?>  
                                          <?php echo e($errors -> first('country_code')); ?>

                                      <?php endif; ?>
                                  .</div>
                                       

                            </div>
                            <div class="ui input col-md-8 col-sm-7 <?php if($errors->has('phone')): ?> error  <?php endif; ?>">
                                <input class="form-control phone" id="phone" value="<?php echo e($provider -> phone); ?>" placeholder="مثال : 05xxxxxxxx" type="text" name="phone">
                                <div class="error-messagen">
                                      <?php if($errors->has('phone')): ?>  
                                          <?php echo e($errors -> first('phone')); ?>

                                      <?php endif; ?>
                                  .</div>

                            </div>
                        </div><!-- End inline-from -->

                         <div class="ui field <?php if($errors->has('membership_id')): ?> error  <?php endif; ?>">
                                <label> النوع  :<span class="require">*</span></label>
                                <div class="ui input">
                                    <select class="ui dropdown"  name="membership_id">
                                        <option value="">Select</option>
                                        <?php if(isset($memberships) && $memberships -> count() > 0): ?>
                                            <?php $__currentLoopData = $memberships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($type->membership_id); ?>"   <?php  if($type -> choosen  == 1 ) { echo 'selected';  }     ?> 
                                                    ><?php echo e($type->membership_ar_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                 <div class="error-messagen">
                                      <?php if($errors->has('membership_id')): ?>  
                                          <?php echo e($errors -> first('membership_id')); ?>

                                      <?php endif; ?>
                                  .</div>
                            </div>
                        <span class="spacer-25"></span>
                        <!-- <div>
                            <div class="ui field">
                                <label>Phone : <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="phone" id="phone" type="text" placeholder="Phon number" class="phone" value="<?php echo e(old('phone')); ?>" />
                                </div>
                            </div>
                        </div> -->
                        <div class="two fields">
                            <div class="ui field <?php if($errors->has('password')): ?> error  <?php endif; ?>">
                                <label>كلمة المرور :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="password" id="password" type="password" placeholder="كلمة المرور" value="<?php echo e(old('password')); ?>" />
                                </div>
                                 <div class="error-messagen">
                                      <?php if($errors->has('password')): ?>  
                                          <?php echo e($errors -> first('password')); ?>

                                      <?php endif; ?>
                                  .</div>
                            </div>
                            <div class="ui field <?php if($errors->has('password_confirmation')): ?> error  <?php endif; ?>">
                                <label>تأكيد كلمة المرور :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="password_confirmation" id="password_confirmation" type="password" placeholder="تأكيد كلمة المرور" value="<?php echo e(old('password_confirmation')); ?>" />
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
                           تصنفيات مقدم الخدمة و طرق التوصيل 
                        </div>
                        <div class="form-title"> اختر تصنيف رئيسي  :<span class="require">*</span></div>
                        <div class="form-group  <?php if($errors->has('category_id')): ?> error  <?php endif; ?>">
                            <select   class="form-control" name="category_id">
                                    <option> اختر قسم </option>
                                <?php if(isset($categories) && $categories->count() > 0): ?>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category->cat_id); ?>" <?php  if($category ->choosen   == 1 ) { echo 'selected';  }     ?> ><?php echo e($category->cat_ar_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                            <div class="error-messagen">
                                      <?php if($errors->has('category_id')): ?>  
                                          <?php echo e($errors -> first('category_id')); ?>

                                      <?php endif; ?>
                                  .</div>
                        </div>


                        <div class="form-title"> اختر  طرق التوصيل المتاحه   :<span class="require">*</span></div>
                        <div class="form-group <?php if($errors->has('delivery_method')): ?> error  <?php endif; ?>">
                            <select id="delivery_method" multiple class="form-control multipart" name="delivery_method[]">

                                <?php if(isset($delivery_methods) && $delivery_methods->count() > 0): ?>
                                    <?php $__currentLoopData = $delivery_methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option  value="<?php echo e($method->method_id); ?>" <?php  if($method -> choosen  == 1 ) { echo 'selected';  }     ?> ><?php echo e($method->method_ar_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>

                              <div class="error-messagen">
                                      <?php if($errors->has('delivery_method')): ?>  
                                          <?php echo e($errors -> first('delivery_method')); ?>

                                      <?php endif; ?>
                                  .</div>
                        </div>

                          <div class="ui field <?php if($errors->has('delivery_price')): ?> error  <?php endif; ?>" id="delivery_price" >
                                <label> تكلفه التوصيل  : (في حاله التوصيل من المتجر مع التكلفه )</label>
                                <div class="ui input">
                                    <input name="delivery_price" type="text" placeholder=" قم بادخال تكلفه التوصيل مثال   10 " value="<?php echo e($provider  -> delivery_price); ?>" />
                                </div>
                                <div class="error-messagen">
                                      <?php if($errors->has('delivery_price')): ?>  
                                          <?php echo e($errors -> first('delivery_price')); ?>

                                      <?php endif; ?>
                                  .</div>
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

     
    $(document).ready(function(){
           
           $('#password').val('');
           $('#password_confirmation').val('');

    });
    


      // This example adds a search box to a map, using the Google Place Autocomplete
      // feature. People can enter geographical searches. The search box will return a
      // pick list containing a mix of places and predicted search terms.

      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

      function initAutocomplete() {
        var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: <?php echo e($provider -> latitude); ?>, lng: <?php echo e($provider -> longitude); ?> },
          zoom: 19,
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