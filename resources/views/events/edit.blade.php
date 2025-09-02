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
  <div  id="kt_post">
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
        <form class="form" method="POST" action="{{ route($update, $result->id) }}">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Name</label>
              <input type="text" class="form-control" name="name" id="name" value="{{old('name',$result->name)}}">
              @if ($errors->has('name'))
              <span class="text-danger">{{ $errors->first('name') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Place</label>
              <select class="form-select " name="place_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($places as $place)
                  <option value="{{ $place->id }}" {{ old('place_id',$result->place_id) == $place->id ? 'selected' : '' }}>{{ $place->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('place_id'))
              <span class="text-danger">{{ $errors->first('place_id') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Date</label>
              <input type="date" class="form-control" name="date" id="date" value="{{old('date', $result->date)}}">
              @if ($errors->has('date'))
              <span class="text-danger">{{ $errors->first('date') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label tiffin-required">Start Time</label>
              <input class="form-control tiffin-field" type="time" id="start" name="start" value="{{old('start',$result->start)}}">
              @if ($errors->has('start'))
              <span class="text-danger">{{ $errors->first('start') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label tiffin-required">End Time</label>
              <input class="form-control tiffin-field" type="time" id="end" name="end" value="{{old('end',$result->end)}}">
              @if ($errors->has('end'))
              <span class="text-danger">{{ $errors->first('end') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Event Hours</label>
              <input class="form-control" name="event_hours" id="diff" readonly>
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Meal</label>
              <select class="form-select " name="meal" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                <option {{old('meal',$result->meal) == "breakfast" ? "selected": "" }} value="breakfast">Breakfast</option>
                <option {{old('meal',$result->meal) == "lunch" ? "selected": "" }} value="lunch">Lunch</option>
                <option {{old('meal',$result->meal) == "dinner" ? "selected": "" }} value="dinner">Dinner</option>
              </select>
              @if ($errors->has('meal'))
              <span class="text-danger">{{ $errors->first('meal') }}</span>
              @endif
            </div>
            <!-- <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Description</label>
              <textarea class="form-control" name="description" id="description">{{old('description',$result->description)}}</textarea>
              @if ($errors->has('description'))
              <span class="text-danger">{{ $errors->first('description') }}</span>
              @endif
            </div> -->
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Serving</label>
              <select class="form-select" id="serving" name="serving" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                <option value="tiffin" {{ old('serving',$result->serving) == 'tiffin' ? 'selected' : '' }}>Tiffin</option>
                <option value="thaal" {{ old('serving',$result->serving) == 'thaal' ? 'selected' : '' }}>Thaal</option>
                <option value="thaal-tiffin" {{ old('serving',$result->serving) == 'thaal-tiffin' ? 'selected' : '' }}>Thaal + Tiffin</option>
              </select>
              <input type="number" hidden class="form-control" id="serving_persons" name="serving_persons" value="{{ old('no_of_thaal') }}"> 
              @if ($errors->has('serving'))
              <span class="text-danger">{{ $errors->first('serving') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Host ITS No</label>
              <input type="text" class="form-control" name="host_its_no" id="host_its_no" value="{{old('host_its_no',$result->host_its_no)}}">
              @if ($errors->has('host_its_no'))
              <span class="text-danger">{{ $errors->first('host_its_no') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Host Sabeel No</label>
              <input type="text" class="form-control" name="host_sabeel_no" id="host_sabeel_no" value="{{old('host_sabeel_no',$result->host_sabeel_no)}}">
              @if ($errors->has('host_sabeel_no'))
              <span class="text-danger">{{ $errors->first('host_sabeel_no') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Host Name</label>
              <input type="text" class="form-control" name="host_name" id="host_name" value="{{old('host_name',$result->host_name)}}">
              @if ($errors->has('host_name'))
              <span class="text-danger">{{ $errors->first('host_name') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Menu</label>
              <textarea class="form-control" name="host_menu" id="host_menu">{{old('host_menu',$result->host_menu)}}</textarea>
              @if ($errors->has('host_menu'))
              <span class="text-danger">{{ $errors->first('host_menu') }}</span>
              @endif
            </div>
            <div id="thaalField" class="col-md-4 col-lg-3 mb-5" style="display:none;">
              <label class="form-label required">No. of Thaal (For 8 person)</label>
              <input type="number"  min="0" class="form-control" name="no_of_thaal" id="no_of_thaal" value="{{old('no_of_thaal',$result->no_of_thaal)}}">
              
              @if ($errors->has('no_of_thaal'))
              <span class="text-danger">{{ $errors->first('no_of_thaal') }}</span>
              @endif
            </div>
            <div id="tiffanField" style="display: none;" class="col-md-12 mb-5">
              <h5>Tiffin</h5>
              <hr />
              <div class="row">
                @foreach($tiffinSizes as $index => $result)
                @php
                $existingTiffin = collect($servingItems)->firstWhere('tiffin_size_id', $result->id);
                $isChecked = $existingTiffin ? 'checked' : '';
                $tiffinCount = $existingTiffin ? $existingTiffin['count'] : 1;
                @endphp
                <div class="col-md-4 col-lg-3 mb-5">
                  <label class="text-capitalize form-label">{{ $result->name }} (For <span> {{$result->person_no}}</span> person{{$result->person_no == 1 ? "":"s" }})</label>
                  <div class="d-flex border border-1 rounded-4">
                    <div class="bg-light p-3 d-flex justify-content-center align-items-center {{$existingTiffin ? 'd-none':''}}">
                      <input hidden type="number" value="{{$existingTiffin ? $existingTiffin['id'] : ''}}" {{$existingTiffin ? '' : 'disabled'}} name="serving_item[]" class="form-control" />
                      <input class="tiffan_type" type="checkbox" name="tiffan_type[]" value="{{ $result->id }}" id="result_{{ $result->id }}"
                        {{ $isChecked }}>
                    </div>
                    <input type="number"  min="0" value="{{ $tiffinCount }}" name="no_of_taffin[]" id="no_of_taffin_{{$result->id}}" placeholder="No. of Tiffin" class="form-control no_of_taffin" {{ $isChecked ? '' : 'disabled' }} />
                  </div>
                </div>

                @endforeach
              </div>
            </div>
            <div class="col-md-12 mt-3">
              <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2" style="margin-right: 5px">
              
              <button type="submit" class="btn btn-primary hover-elevate-up w-100px submit-button">
                <span class="spinner-border spinner-border-custom d-none" role="status" aria-hidden="true"></span>
                <span class="button-text"> Update </span>
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
<!-- <script src="assets/plugins/custom/datatables/datatables.bundle.js"></script> -->
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
            let tiffanPersons = tiffanInput.closest('.col-md-4').find("label span").text();

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
      const thaalInput = $('#no_of_taffin_' + $(checkbox).val());
      if ($(checkbox).is(':checked')) {
        thaalInput.prop('disabled', false);
      } else {
        thaalInput.prop('disabled', true).val('');
      }
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
      document.getElementById("diff").value = diffValue;
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

        $('.tiffin-required').addClass('required');
        $('.tiffin-field').prop("required", true);
      } else if (serving === 'thaal-tiffin') {
        thaalField.css('display', 'block');
        thaalField.find("input[type='number']").prop("required", true);

        tiffanField.css('display', 'block');

        $('.tiffin-required').addClass('required');
        $('.tiffin-field').prop("required", true);
      } else {
        $('.tiffin-required').removeClass('required');
        $('.tiffin-field').prop("required", false);
        $('#start').val('07:00');
        $('#end').val('10:00');

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
    totalServing()
    $('form').on('submit', function () {

      const anyChecked = $('.tiffan_type:checked').length >= 0;
      // if($('#serving option:selected').val())
      if($('#serving option:selected').val() == 'tiffin' || $('#serving option:selected').val() == 'thaal-tiffin')  {
        if($('.tiffan_type:checked').length == 0)  {
           e.preventDefault();
          alert('Please select at least one Tiffin type.');
          return
        }
      }

      if (!anyChecked) {
        e.preventDefault();
        alert('Please select at least one Tiffin type.');
        return
      }
      e.preventDefault();
      return false;
      
      const button = $('.submit-button');
      $('.button-text').hide()
      button.prop('disabled', true);
      button.find('.spinner-border').removeClass('d-none'); // show spinner
    });
  });
</script>
@endpush