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
        <div class="row">
          <div class="d-flex justify-content-between border-bottom pb-2 border-bottom-3 border-primary my-3">
            <h2>
            {{old('name',$result->name)}}
            </h2>
            @if(!isset($result->menu->id))
            <div>
              @if(hasPermissionForModule('edit', $currentModuleId))
              <a title="Edit" href="{{ route($edit, $result->id) }}" class="btn btn-primary btn-sm me-3">
                <i class="fa-regular fa-pen-to-square"></i>
                Edit
              </a>
              @endif
              <!-- Delete Form (Hidden) -->
              @if(hasPermissionForModule('delete', $currentModuleId))
              <form id="delete-form-{{ $result->id }}" action="{{ route($destroy, $result->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
              </form>
              <!-- Delete Button -->
              <a
                href="javascript:void(0);"
                title="Delete"
                class="btn btn-dark btn-sm me-3"
                onclick="event.preventDefault(); confirmDelete({{ $result->id }});">
                <i class="fa-regular fa-trash-can"></i>
                Delete
              </a>
              @endif

            </div>
            @endif
          </div>
          <div class="col-md-4 col-lg-3 mb-5 d-none">
            <label class="form-label">Name</label>
            <input hidden type="text" class="form-control" name="name" id="name" value="{{old('name',$result->name)}}">
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{old('name',$result->name)}}
            </h5>
          </div>
          <div class="col-md-4 col-lg-3 mb-5">
            <label class="form-label">Event Place</label>
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
            <label class="form-label">Event Date</label>
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
          <div class="col-md-4 col-lg-3 mb-5">
            <label class="form-label">Host ITS No</label>
            <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ ucfirst(old('host_its_no',$result->host_its_no))}}
            </h5>
          </div>
          <div class="col-md-4 col-lg-3 mb-5">
            <label class="form-label">Host Sabeel No</label>
            <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ ucfirst(old('host_sabeel_no',$result->host_sabeel_no))}}
            </h5>
          </div>
          <div class="col-md-4 col-lg-3 mb-5">
            <label class="form-label">Host Name</label>
            <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ ucfirst(old('host_name',$result->host_name))}}
            </h5>
          </div>
          <div class="col-md-4 col-lg-3 mb-5">
            <label class="form-label">Menu</label>
            <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ ucfirst(old('host_menu',$result->host_menu))}}
            </h5>
          </div>
          <!-- <div class="col-md-4 col-lg-3 mb-5">
            <label class="form-label">Description</label>
            <textarea disabled class="form-control" name="description" id="description">{{old('description',$result->description)}}</textarea>
          </div> -->

          <div id="thaalField"  style="display:none;">
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
                <input type="number" hidden class="form-control" id="serving_persons" name="serving_persons" value="{{ old('no_of_thaal') }}">
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
              @foreach($tiffinSizes as $index => $tiffinSize)
              @php
              $existingTiffin = collect($servingItems)->firstWhere('tiffin_size_id', $tiffinSize->id);
              $isChecked = $existingTiffin ? 'checked' : '';
              $tiffinCount = $existingTiffin ? $existingTiffin['count'] : 1;
              @endphp

              <div class="col-md-4 col-lg-3 mb-5 {{$existingTiffin ? '':'d-none'}}">
                <label class="text-capitalize form-label">{{ $tiffinSize->name }} (For <span> {{$tiffinSize->person_no}}</span> person{{$tiffinSize->person_no == 1 ? "":"s" }})</label>
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
  function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this record?')) {
      var form = document.getElementById('delete-form-' + id);
      if (form) {
        form.submit();
      } else {
        console.error('Form not found for ID record:', id);
      }
    }
  }
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
      console.log($(this))
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
          console.log("sss")

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
    totalServing()

  });
</script>
@endpush