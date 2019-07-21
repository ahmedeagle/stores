@extends('cpanel.layout.master')


@section('content')
<div class="content">
    <div class="col-sm-12" id="container">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2> تعديل  مقدم خدمة </h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li> <a href="{{route('provider.show')}}">المتاجر </a></li>
                    <li class="active"> تعديل  مقدم خدمة</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div>
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-title">
                  نموذج  تعديل  مقدم خدمة
                </div>
                <div class="widget-content">
                    <form class="ui form" id="create-providerr" method="post" action="{{ route('provider.update') }}" enctype="multipart/form-data">

                        <input type="hidden" name="provider_id" value="{{$provider -> provider_id}}">
                        <div class="form-title">من فضلك إملئ الحقول التالية </div>
                        <div class="form-note">[ * ] حقل مطلوب</div>
                        <div class="ui error message"></div>
                       
                        @if(!empty($errors->first()))
                            <div class="alert alert-danger">
                                <strong>خطأ !</strong> لابد من تصحيح الاخطاء الاتية 
                            </div>
                        @endif


                        @if(!empty($msg))
                            <div class="alert alert-success">
                                <strong>تم بنجاح !</strong> {{ $msg }}
                            </div>
                        @endif



                        <div class="widget-title">
                            المعلومات الشخصية
                        </div>
                        <div class="two fields">
                            <div class="ui field @if ($errors->has('full_name')) error  @endif">
                                <label> الاسم بالكامل :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input   name="full_name" id="full_name" type="text" placeholder=" الاسم بالكامل " value="{{ $provider -> full_name }}" />
 
                                     
                                </div>
                                  <div class="error-messagen">
                                      @if($errors->has('full_name'))  
                                          {{$errors -> first('full_name')}}
                                      @endif
                                  .</div>
                                       
                            </div>
                            <div class="ui field @if ($errors->has('store_name')) error  @endif">
                                <label>  اسم المتجر  :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="store_name" id="store_name" type="text" placeholder="الأسم المتجر " value="{{ $provider -> store_name }}" />

                                </div>
                                 <div class="error-messagen">
                                      @if($errors->has('store_name'))  
                                          {{$errors -> first('store_name')}}
                                      @endif
                                  .</div>
                                  
                            </div>
                        </div>

  
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                           تفاصيل العنوان
                        </div>
                        <div class="two fields">
                            <div class="ui field @if ($errors->has('country_id')) error  @endif" >
                                <label>الدول :<span class="require">*</span></label>
                                <div class="ui input">
                                    <select class="ui dropdown country" id="countries" name="country_id">
                                      
                                       <option value="">اختر دولة </option>
                                       @if(isset($countries) && $countries -> count() > 0)
                                          @foreach($countries as $country)
                                              
                                                <option id="{{$country -> country_id}}" value="{{ $country->country_id }}" 
                                                        @php if($country -> choosen   == 1 ) { echo 'selected';  }    @endphp 

                                                    >{{ $country->country_ar_name }}</option>

                                          @endforeach
                                       @endif
 
                                    </select>
                                </div>

                                   <div class="error-messagen">
                                      @if($errors->has('country_id'))  
                                          {{$errors -> first('country_id')}}
                                      @endif
                                  .</div>
                                    
                            </div>
                            <div class="ui field cityDiv @if ($errors->has('city_id')) error  @endif">
                                <label>المدن :<span class="require">*</span></label>
                                <select id="cities" class="ui dropdown city" name="city_id">
                                    <option value="">إختر مدينه</option>
                                     @if(isset($cities) && $cities -> count() > 0)

                                     @foreach($cities as $city)
                                                <option id="{{$city -> city_id }}" value="{{ $city -> city_id  }}" 
                                                        @php if($city -> choosen   == 1 ) { echo 'selected';  }    @endphp 

                                                    >{{ $city -> city_ar_name }}</option>

                                          @endforeach
                                       @endif

                                </select>
                            </div>
                             <div class="error-messagen">
                                      @if($errors->has('city_id'))  
                                          {{$errors -> first('city_id')}}
                                      @endif
                                  .</div>
 
                        </div>
                        <div>
                         

                           <input id="pac-input" class="controls" type="text" placeholder="أبحث هنا عن مكانك  علي الخريطه ">

                           <input type="hidden" id="latitudef"  value="{{$provider -> latitude}}" name="latitude">
                           <input type="hidden" id="longitudef" value="{{$provider -> longitude}}" name="longitude">
                              
                                 <div class="error-messagen">
                                      @if($errors->has('latitude'))  
                                          {{$errors -> first('latitude')}}
                                      @endif
                                  .</div>
                                       


                        <div id="map"></div>

                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">صور للاوراق المطلوبة
                        </div>
                         
                             <div class=" ui field @if ($errors->has('commercial_photo')) error  @endif">
                                <label for="commercial_photo"> صوره السجل التجاري   </label>
                                <input name="commercial_photo" id="commercial_photo" type="file" />
                                
                                   <div class="error-messagen">
                                      @if($errors->has('commercial_photo'))  
                                          {{$errors -> first('commercial_photo')}}
                                      @endif
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
                            <div class="ui input col-md-2 col-sm-3 @if ($errors->has('country_code')) error  @endif">
                                <input class="form-control country_code" id="country_code" value="{{$provider -> country_code  }}" placeholder="مثال : 996,20" type="text" name="country_code">
                                   <div class="error-messagen">
                                      @if($errors->has('country_code'))  
                                          {{$errors -> first('country_code')}}
                                      @endif
                                  .</div>
                                       

                            </div>
                            <div class="ui input col-md-8 col-sm-7 @if ($errors->has('phone')) error  @endif">
                                <input class="form-control phone" id="phone" value="{{ $provider -> phone }}" placeholder="مثال : 05xxxxxxxx" type="text" name="phone">
                                <div class="error-messagen">
                                      @if($errors->has('phone'))  
                                          {{$errors -> first('phone')}}
                                      @endif
                                  .</div>

                            </div>
                        </div><!-- End inline-from -->

                         <div class="ui field @if ($errors->has('membership_id')) error  @endif">
                                <label> النوع  :<span class="require">*</span></label>
                                <div class="ui input">
                                    <select class="ui dropdown"  name="membership_id">
                                        <option value="">Select</option>
                                        @if(isset($memberships) && $memberships -> count() > 0)
                                            @foreach($memberships AS $type)
                                                <option value="{{ $type->membership_id }}"   @php if($type -> choosen  == 1 ) { echo 'selected';  }    @endphp 
                                                    >{{ $type->membership_ar_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                 <div class="error-messagen">
                                      @if($errors->has('membership_id'))  
                                          {{$errors -> first('membership_id')}}
                                      @endif
                                  .</div>
                            </div>
                        <span class="spacer-25"></span>
                        <!-- <div>
                            <div class="ui field">
                                <label>Phone : <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="phone" id="phone" type="text" placeholder="Phon number" class="phone" value="{{ old('phone') }}" />
                                </div>
                            </div>
                        </div> -->
                        <div class="two fields">
                            <div class="ui field @if ($errors->has('password')) error  @endif">
                                <label>كلمة المرور :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="password" id="password" type="password" placeholder="كلمة المرور" value="{{ old('password') }}" />
                                </div>
                                 <div class="error-messagen">
                                      @if($errors->has('password'))  
                                          {{$errors -> first('password')}}
                                      @endif
                                  .</div>
                            </div>
                            <div class="ui field @if ($errors->has('password_confirmation')) error  @endif">
                                <label>تأكيد كلمة المرور :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="password_confirmation" id="password_confirmation" type="password" placeholder="تأكيد كلمة المرور" value="{{ old('password_confirmation') }}" />
                                </div>
                                <div class="error-messagen">
                                      @if($errors->has('password_confirmation'))  
                                          {{$errors -> first('password_confirmation')}}
                                      @endif
                                  .</div>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                           تصنفيات مقدم الخدمة و طرق التوصيل 
                        </div>
                        <div class="form-title"> اختر تصنيف رئيسي  :<span class="require">*</span></div>
                        <div class="form-group  @if ($errors->has('category_id')) error  @endif">
                            <select   class="form-control" name="category_id">
                                    <option> اختر قسم </option>
                                @if(isset($categories) && $categories->count() > 0)
                                    @foreach($categories AS $category)
                                        <option value="{{ $category->cat_id }}" @php if($category ->choosen   == 1 ) { echo 'selected';  }    @endphp >{{ $category->cat_ar_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="error-messagen">
                                      @if($errors->has('category_id'))  
                                          {{$errors -> first('category_id')}}
                                      @endif
                                  .</div>
                        </div>


                        <div class="form-title"> اختر  طرق التوصيل المتاحه   :<span class="require">*</span></div>
                        <div class="form-group @if ($errors->has('delivery_method')) error  @endif">
                            <select id="delivery_method" multiple class="form-control multipart" name="delivery_method[]">

                                @if(isset($delivery_methods) && $delivery_methods->count() > 0)
                                    @foreach($delivery_methods AS $method)
                                        <option  value="{{ $method->method_id }}" @php if($method -> choosen  == 1 ) { echo 'selected';  }    @endphp >{{ $method->method_ar_name }}</option>
                                    @endforeach
                                @endif
                            </select>

                              <div class="error-messagen">
                                      @if($errors->has('delivery_method'))  
                                          {{$errors -> first('delivery_method')}}
                                      @endif
                                  .</div>
                        </div>

                          <div class="ui field @if ($errors->has('delivery_price')) error  @endif" id="delivery_price" >
                                <label> تكلفه التوصيل  : (في حاله التوصيل من المتجر مع التكلفه )</label>
                                <div class="ui input">
                                    <input name="delivery_price" type="text" placeholder=" قم بادخال تكلفه التوصيل مثال   10 " value="{{ $provider  -> delivery_price  }}" />
                                </div>
                                <div class="error-messagen">
                                      @if($errors->has('delivery_price'))  
                                          {{$errors -> first('delivery_price')}}
                                      @endif
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

@stop
@section('customJs')
<script type="text/javascript">
    

    $(document).ready(function(){
        $("body").on("change", ".country", function(){
            var country = $(this).val();
            getCountryCities("{{ route('country.cities') }}", country, 'en', $('#cities'), $('.country_code'), $(".phone"), 2);
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
          center: {lat: {{$provider -> latitude}}, lng: {{$provider -> longitude}} },
          zoom: 10,
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
@stop

