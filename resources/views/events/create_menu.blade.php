@extends('layout.master')
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Toolbar-->
  <div class="toolbar" id="kt_toolbar">
    <!--begin::Container-->
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <!--begin::Page title-->
      <div data-kt-swapper-mode="prepend" class="page-title d-flex align-items-center me-3 flex-wrap lh-1">
        <!--begin::Separator-->
        <!--end::Separator-->
        <!--begin::Breadcrumb-->
        @include('layout.breadcrumb')
        <!--end::Breadcrumb-->
      </div>
      <!--end::Page title-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Toolbar-->
  <!--begin::Post-->
  <div id="kt_post">
    <!--begin::Container-->
    <div class="container-fluid" id="kt_content_container">
      @if(Session::has('error'))
      <!--begin::Alert-->
      <div class="alert alert-danger d-flex align-items-center p-5">
        <!--begin::Icon-->
        <i class="ki-duotone ki-shield-tick fs-2hx text-danger me-4"><span class="path1"></span><span class="path2"></span></i>
        <!--end::Icon-->

        <!--begin::Wrapper-->
        <div class="d-flex flex-column">
          <!--begin::Content-->
          <span>{{ Session::get('error') }}</span>
          <!--end::Content-->
        </div>
        <!--end::Wrapper-->
      </div>
      <!--end::Alert-->
      @endif
      <!--begin::Card-->
      <div class="bg-transparent border-0 card shadow-none pt-2">
        <form class="form" method="POST" action="{{ route('events.store-menu', $result->id) }}">
          @csrf
          <div class="d-flex justify-content-between border-bottom pb-2 border-bottom-3 border-primary my-3">
            <h2>
              {{old('name',$result->name)}}
            </h2>
            <div>
              <svg class="close-icon cursor-pointer" style="display: none;" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 682.667 682.667" xml:space="preserve"><g><defs><clipPath id="a" clipPathUnits="userSpaceOnUse"><path d="M0 512h512V0H0Z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)"></path></clipPath></defs><g clip-path="url(#a)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)"><path d="M0 0h157.333M-28.961 236h215.255a120 120 0 0 0 84.853-35.147l8.372-8.373a119.994 119.994 0 0 0 35.148-84.852v-215.256a119.994 119.994 0 0 0-35.148-84.852l-8.372-8.373A120 120 0 0 0 186.294-236H-28.961a120.001 120.001 0 0 0-84.853 35.147l-8.373 8.373a119.996 119.996 0 0 0-35.146 84.852v215.256a119.996 119.996 0 0 0 35.146 84.852l8.373 8.373A120.001 120.001 0 0 0-28.961 236Z" style="stroke-width:40;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(177.333 256)" fill="none" stroke="var(--bs-primary)" stroke-width="40" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="var(--bs-primary)" class=""></path></g></g></svg>
              <svg class="open-icon cursor-pointer"  xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 24 24" xml:space="preserve"><g><path d="M18 2c2.206 0 4 1.794 4 4v12c0 2.206-1.794 4-4 4H6c-2.206 0-4-1.794-4-4V6c0-2.206 1.794-4 4-4zm0-2H6a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h12a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)" class=""></path><path d="M12 18a1 1 0 0 1-1-1V7a1 1 0 0 1 2 0v10a1 1 0 0 1-1 1z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)" class=""></path><path d="M6 12a1 1 0 0 1 1-1h10a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)" class=""></path></g></svg>
            </div>
          </div>
          <div class="row event-detail" style="display: none;">
            <div class="col-md-4 col-lg-3 mb-5 d-none">
              <label class="form-label">Name</label>
              <input hidden type="text" class="form-control" name="name" id="name" value="{{old('name',$result->name)}}">
              <input type="hidden" class="form-control disable" name="event_id" id="event_id" value="{{old('event_id',$result->id)}}">
              <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
                {{old('name',$result->name)}}
              </h5>
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Place</label>
              <select class="form-select  d-none" name="place_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($places as $place)
                <option value="{{ $place->id }}" {{ old('place_id',$result->place_id) == $place->id ? 'selected' : '' }}>{{ $place->name }}</option>
                @endforeach
              </select>
              @php
              $placeName = collect($places)->firstWhere('id', old('place_id', $result->place_id))?->name;
              @endphp

              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                {{ $placeName }}
              </h5>
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Date</label>
              <input hidden type="date" class="form-control" name="date" id="date" value="{{old('date', $result->date)}}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{old('date', \Carbon\Carbon::parse($result->date)->format('jS F Y') )}}
              </h5>
            </div>

            <div class="col-md-4 col-lg-3 mb-5 {{ $result->serving == 'tiffin' ? 'd-none' : '' }}">
              <label class="form-label">Event Time</label>
             
              <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{\Carbon\Carbon::parse($result->start)->isoFormat('hh:mm A')}} -
              {{\Carbon\Carbon::parse($result->end)->isoFormat('hh:mm A')}}
            </h5>
            </div>
            <div class="col-md-4 col-lg-3 mb-5 d-none">
              <label class="form-label">Start Time</label>
              <input hidden class="form-control" type="time" id="start" name="start" value="{{old('start',$result->start)}}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                {{old('start',$result->start)}}
              </h5>
            </div>
            <div class="col-md-4 col-lg-3 mb-5 d-none">
              <label class="form-label">End Time</label>
              <input hidden class="form-control" type="time" id="end" name="end" value="{{old('end',$result->end)}}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                {{old('end',$result->end)}}
              </h5>
            </div>
            <div class="col-md-4 col-lg-3 mb-5 {{ $result->serving == 'tiffin' ? 'd-none' : '' }}">
              <label class="form-label">Event Hours</label>
              <!-- <input disabled class="form-control" name="event_hours" id="diff" readonly> -->
              <h5 id="diff" class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;"></h5>
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Meal</label>
              <select class="form-select d-none" name="meal" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                <option {{old('meal',$result->meal) == "breakfast" ? "selected": "" }} value="breakfast">Breakfast</option>
                <option {{old('meal',$result->meal) == "lunch" ? "selected": "" }} value="lunch">Lunch</option>
                <option {{old('meal',$result->meal) == "dinner" ? "selected": "" }} value="dinner">Dinner</option>
              </select>
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                {{ ucfirst(old('meal',$result->meal))}}
              </h5>
            </div>

            <div id="thaalField" style="display:none;">
              <div class="row">
                <div class="col-md-4 col-lg-3 mb-5">
                  <label class="form-label">Serving</label>
                  <select class="form-select d-none" id="serving" name="serving" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option value=""></option>
                    <option value="tiffin" {{ old('serving',$result->serving) == 'tiffin' ? 'selected' : '' }}>Tiffin</option>
                    <option value="thaal" {{ old('serving',$result->serving) == 'thaal' ? 'selected' : '' }}>Thaal</option>
                    <option value="thaal-tiffin" {{ old('serving',$result->serving) == 'thaal-tiffin' ? 'selected' : '' }}>Thaal + Tiffin</option>
                  </select>
                  <input type="number" hidden class="form-control" id="serving_persons" name="serving_persons" value="{{ old('no_of_thaal') }}">
                  <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                    {{ ucfirst(old('serving',$result->serving))}}
                  </h5>
                  
                </div>
                <div class="col-md-4 col-lg-3 mb-5">
                  <label class="form-label">No. of Thaal (For 8 person)</label>
                  <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                    {{old('no_of_thaal',$result->no_of_thaal)}}
                  </h5>
                  <input hidden type="number" min="0" class="form-control" name="no_of_thaal" id="no_of_thaal" value="{{old('no_of_thaal',$result->no_of_thaal)}}">
                </div>
              </div>
            </div>
            <div id="tiffanField" style="display: none;">
              <div class="row">
                <div class="col-md-4 col-lg-3 mb-5">
                  <label class="form-label">Serving</label>
                  <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                    {{ ucfirst(old('serving',$result->serving))}}
                  </h5>
                </div>
                @foreach($tiffinSizes as $index => $result)
                @php
                $existingTiffin = collect($servingItems)->firstWhere('tiffin_size_id', $result->id);
                $isChecked = $existingTiffin ? 'checked' : '';
                $tiffinCount = $existingTiffin ? $existingTiffin['count'] : 1;
                @endphp

                <div class="col-md-4 col-lg-3 mb-5 {{$existingTiffin ? '':'d-none'}}">
                  
                  <label class="form-label text-capitalize">{{ $result->name }} (For <span> {{$result->person_no}}</span> person{{$result->person_no == 1 ? "":"s" }})</label>
                  <!-- <div class="d-flex border border-1 rounded-4 disabled" >
                <div class="bg-light p-3 d-flex justify-content-center align-items-center {{$existingTiffin ? 'd-none':''}}">
                  <input  hidden type="number" value="{{$existingTiffin ? $existingTiffin['id'] : ''}}" name="serving_item[]" class="form-control" />
                  <input disabled class="tiffan_type" type="checkbox" name="tiffan_type[]" value="{{ $result->id }}" id="result_{{ $result->id }}"
                    {{ $isChecked }}>
                </div>
                < !-- <input disabled type="number" min="0" value="{{ $tiffinCount }}" name="no_of_taffin[]" id="no_of_taffin_{{$result->id}}" placeholder="No. of Tiffin" class="form-control no_of_taffin "/> -- >
              </div> -->
                  <input hidden type="number" min="0" value="{{ $tiffinCount }}" name="no_of_taffin[]" id="no_of_taffin_{{$result->id}}" placeholder="No. of Tiffin" class="form-control no_of_taffin " />
                  <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                    {{ $tiffinCount }}
                  </h5>
                </div>
                @endforeach
              </div>
            </div>

            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Created</label>
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">

                {{ $result->createdBy ? $result->createdBy->name : '-' }}
                <br />
                {{ \Carbon\Carbon::parse($result->created_at)->isoFormat('Do MMM YYYY, hh:mm A') }}
              </h5>
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Last Modified</label>
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">

                {{ $result->updated_at ? $result->updatedBy->name : '-' }}
                <br />
                {{ $result->updated_at ? \Carbon\Carbon::parse($result->updated_at)->isoFormat('Do MMM YYYY, hh:mm A') :'-' }}
              </h5>

            </div>
          </div>

          <div class="row">
            <div class="col-12 my-3">
              <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Recipe Menu</h2>
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Dish</label>
              <select multiple class="form-select " name="dish_id[]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($results as $result)
                <!-- <option value="{{ $result->id }}" {{ old('dish_id') == $result->id  ? 'selected' : '' }}>{{ $result->dish->name .' - '. $result->chefUser->name }} - {{$result->serving}} pax</option> -->
                <option value="{{ $result->id }}" {{ in_array($result->id, old('dish_id', [])) ? 'selected' : '' }}>
                  {{ $result->dish->name .' - '. $result->chefUser->name }} - {{ $result->serving }} pax
                </option>
                @endforeach
              </select>
              @if ($errors->has('dish_id'))
              <span class="text-danger">{{ $errors->first('dish_id') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Description</label>
              <textarea class="form-control" name="description" id="description">{{old('description',$result->description)}}</textarea>
            </div>
            <div class="col-md-12 mt-3">
              <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2" style="margin-right: 5px">
              <button type="submit" class="btn btn-primary hover-elevate-up w-100px submit-button">
                <span class="spinner-border spinner-border-custom d-none" role="status" aria-hidden="true"></span>
                <span class="button-text"> Create </span>
              </button>
            </div>
          </div>
        </form>
      </div>
      <!--end::Card-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
@endsection

@push('scripts')
<script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
  $(document).ready(function() { 
    function totalServing() {
      // Reset tiffanPersonsCount at the start of the function to avoid accumulation
      let tiffanPersonsCount = 0;

      // Get the value of thaal persons
      let thaal_persons = $('#no_of_thaal').val() !== "" ? $('#no_of_thaal').val() * 8 : 0;

      $('.tiffan_type').each(function() {
        const tiffanInput = $('#no_of_taffin_' + $(this).val());
        if ($(this).is(':checked')) {
          // Ensure tiffanInput is valid and has a value
          if (tiffanInput.length && !isNaN(tiffanInput.val()) && tiffanInput.val() !== "") {
            let tiffanPersons = tiffanInput.closest('.col-sm-2').find("label span").text();

            // Ensure tiffanPersons is numeric
            if (!isNaN(tiffanPersons)) {
              tiffanPersonsCount += parseFloat(tiffanPersons) * parseFloat(tiffanInput.val());
            }
          }
        }
      });

      $('#serving_persons').val(tiffanPersonsCount + thaal_persons);

    }

    function toggleThaalInput(checkbox) {
      // const thaalInput = $('#no_of_taffin_' + $(checkbox).val());
      // if ($(checkbox).is(':checked')) {
      //   thaalInput.prop('disabled', false);
      // } else {
      //   thaalInput.prop('disabled', true).val('');
      // }
      totalServing();
    }
    $(document).on('keyup', '#no_of_thaal, .no_of_taffin', function() {
      totalServing();
    });
    $(document).on('click', '.tiffan_type', function() {
    
      toggleThaalInput($(this));
    });
    document.getElementById("start").onchange = function() {
      updateDifference();
    };
    document.getElementById("end").onchange = function() {
      updateDifference();
    };

    function updateDifference() {
      var start = document.getElementById("start").value;
      var end = document.getElementById("end").value;
      var diffValue = calculateTimeDifference(start, end);
      document.getElementById("diff").textContent = diffValue;
    }

    function calculateTimeDifference(start, end) {
      // Ensure the start and end values are not empty
      if (!start || !end) {
        return "";
      }

      // Split the time values into hours and minutes
      start = start.split(":");
      end = end.split(":");

      // Create Date objects for comparison
      var startDate = new Date(0, 0, 0, start[0], start[1], 0);
      var endDate = new Date(0, 0, 0, end[0], end[1], 0);

      // Check if the end time is less than the start time
      if (endDate <= startDate) {
        $("#diff").css("color", "red")
        $("input[type='submit']").prop("disabled", true)
        return "Invalid time range";
      } else {
        $("#diff").css("color", "black")
        $("input[type='submit']").prop("disabled", false)
      }

      // Calculate the time difference
      var diff = endDate.getTime() - startDate.getTime();
      var hours = Math.floor(diff / 1000 / 60 / 60);
      diff -= hours * 1000 * 60 * 60;
      var minutes = Math.floor(diff / 1000 / 60);

      // Return formatted time difference
      return (hours < 10 ? "0" : "") + hours + ":" + (minutes < 10 ? "0" : "") + minutes;
    }

    // Initial calculation when the page loads
    updateDifference();

    function toggleFields(e) {
      var serving = $('#serving').val();
      var thaalField = $('#thaalField');
      var tiffanField = $('#tiffanField');

      if (serving === 'thaal') {
        thaalField.css('display', 'block');
        tiffanField.css('display', 'none');
        $('.tiffan_type').prop("checked", false);
        e && thaalField.find("input[type='number']").val('');
        $(thaalField).find("input[type='number']").prop("required", true);
        $('.tiffan_type').each(function() {

          const tiffanInput = $('#no_of_taffin_' + $(this).val());
          tiffanInput.prop('disabled', true);
          tiffanInput.val('');
          $(this).prop('checked', false);

        });
      } else if (serving === 'thaal-tiffin') {
        thaalField.css('display', 'block');
        thaalField.find("input[type='number']").prop("required", true);

        tiffanField.css('display', 'block');
      } else {

        thaalField.css('display', 'none');
        thaalField.find("input[type='number']").prop("required", false);
        thaalField.find("input[type='number']").val("");
        tiffanField.css('display', 'block');
      }

      totalServing();
    }

    // Trigger toggleFields on serving change
    $('#serving').on("change", function() {
      toggleFields(true);
    });
    let anyError = <?php echo json_encode($errors->any()); ?>;
    console.log(<?php echo json_encode($errors->all()); ?>)
    if (anyError) {
      toggleFields(false); // Call the toggleFields function
      $('.tiffan_type').each(function() {
        toggleThaalInput(this); // Pass each checkbox to the function
      });
    }
    toggleFields(false); // Call the toggleFields function
    $('.tiffan_type').each(function() {
      toggleThaalInput(this); // Pass each checkbox to the function
    });
    totalServing();

    $('.open-icon').on('click', function() {
      $(this).hide();
      $('.close-icon').show();
      $('.event-detail').slideDown(300); // 300ms animation
    });
    
    $('.close-icon').on('click', function() {
      $(this).hide();
      $('.open-icon').show();
      $('.event-detail').slideUp(300);
    });
  });
  $('form').on('submit', function () {
  const button = $('.submit-button');
  $('.button-text').hide()
  button.prop('disabled', true);
  button.find('.spinner-border').removeClass('d-none'); // show spinner
});
</script>
@endpush