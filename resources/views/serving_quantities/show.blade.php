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
        <div class="row">
          <div class="col-md-4 col-lg-3 mb-5">
            <label class="form-label required">Serving</label>
            <select class="form-select disable" id="serving" name="serving" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
              <option value=""></option>
              <option value="Tiffin" {{ old('serving', $result['serving']) == 'Tiffin' ? 'selected' : '' }}>Tiffin</option>
              <option value="Thaal" {{ old('serving', $result['serving']) == 'Thaal' ? 'selected' : '' }}>Thaal</option>
            </select>
          </div>

          <div class="col-md-4 col-lg-3 mb-5 thaalField" style="{{ old('serving', $result['serving']) == 'Thaal' ? '' : 'display: none;' }}">
            <label class="form-label required">Quantity</label>
            <input type="number" class="form-control disable" min="0" id="quantity" name="quantity" value="{{ old('quantity', $result['quantity']) }}">

          </div>

          <div class="col-md-4 col-lg-3 mb-5 thaalField" style="{{ old('serving', $result['serving']) == 'Thaal' ? '' : 'display: none;' }}">
            <label class="form-label required">Date From</label>
            <input type="date" class="form-control disable" name="date_from" id="date_from" value="{{ old('date_from', $result['date_from']) }}">

          </div>

          <div class="col-md-4 col-lg-3 mb-5 thaalField" style="{{ old('serving', $result['serving']) == 'Thaal' ? '' : 'display: none;' }}">
            <label class="form-label required">Date To</label>
            <input type="date" class="form-control disable" name="date_to" id="date_to" value="{{ old('date_to', $result['date_to']) }}">

          </div>
        </div>

        <div class="row">
          <div id="tiffinField" style="{{ old('serving', $result['serving']) == 'Tiffin' ? '' : 'display: none;' }}" class="col-md-12 mb-5">
            @foreach($result['servingQuantityItems'] as $index => $item)
            <div class="tiffin-group">
              <h5 class="text-capitalize">Tiffin ({{ $item['servingQuantityTiffinItems']['name'] ?? 'N/A' }} <span>{{ $item['servingQuantityTiffinItems']['person_no'] ?? 'N/A' }}</span> pax{{ ($item['servingQuantityTiffinItems']['person_no'] ?? 0) == 1 ? "" : "s" }})</h5>

              <input type="hidden" name="items[{{ $index }}][tiffin_size_id]" id="tiffin_size_id_{{ $item['id'] }}" value="{{ old('items.' . $index . '.tiffin_size_id', $item['tiffin_size_id']) }}" />
              <input type="hidden" name="items[{{ $index }}][id]" id="id_{{ $item['id'] }}" value="{{ old('items.' . $index . '.id', $item['id']) }}" />
              <hr />

              <div class="row">
                <div class="col-md-4 col-lg-3 mb-5">
                  <label class="form-label text-capitalize required">Quantity</label>
                  <input type="number" min="0" value="{{ old('items.' . $index . '.quantity', $item['quantity']) }}" name="items[{{ $index }}][quantity]" id="quantity_{{ $item['id'] }}" placeholder="Quantity" class="form-control disable" />
                </div>

                <div class="col-md-4 col-lg-3 mb-5">
                  <label class="form-label required">Date From</label>
                  <input type="date" class="form-control disable" name="items[{{ $index }}][date_from]" id="date_from_{{ $item['id'] }}" value="{{ old('items.' . $index . '.date_from', $item['date_from']) }}">

                </div>

                <div class="col-md-4 col-lg-3 mb-5">
                  <label class="form-label required">Date To</label>
                  <input type="date" class="form-control disable" name="items[{{ $index }}][date_to]" id="date_to_{{ $item['id'] }}" value="{{ old('items.' . $index . '.date_to', $item['date_to']) }}">

                </div>
              </div>
            </div>
            @endforeach
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
  $(document).ready(function() {
    // function toggleFields(reload = true) {
    //   var serving = $('#serving').val();
    //   var thaalField = $('.thaalField');
    //   var tiffanField = $('#tiffanField');
    //   $(".btn-primary").prop("disabled", false)
    //   $(".date-error").remove()
    //   if (serving === 'Thaal') {
    //     thaalField.show();
    //     thaalField.find("input[type='number'], input[type='date']").prop("required", true).prop("disabled", false);
    //     tiffanField.hide();
    //     tiffanField.find("input[type='number'], input[type='date']").val('').prop("disabled", true);
    //     tiffanField.find("input[type='text']").prop("disabled", true);
    //   } else if (serving === 'Tiffin') {
    //     thaalField.hide().find("input[type='number'], input[type='date']").val('').prop("disabled", true).prop("required", false);
    //     tiffanField.show();
    //     tiffanField.find("input[type='number'], input[type='date']").prop("disabled", false);
    //     tiffanField.find("input[type='text']").prop("disabled", false);
    //   }
    // }

    function toggleFields(reload = true) {
      var serving = $('#serving').val();
      var thaalField = $('.thaalField');
      var tiffanField = $('#tiffanField');

      $(".btn-primary").prop("disabled", true);
      $(".date-error").remove();

      if (serving === 'Thaal') {
        thaalField.show();
        thaalField.find("input[type='number'], input[type='date']").prop("required", true).prop("disabled", true);
        tiffanField.hide();

        if (!reload) {
          tiffanField.find("input[type='number'], input[type='date']").val('');
        }
        tiffanField.find("input[type='number'], input[type='date']").prop("disabled", true);
        tiffanField.find("input[type='text']").prop("disabled", true);
      } else if (serving === 'Tiffin') {
        thaalField.hide();

        if (!reload) {
          thaalField.find("input[type='number'], input[type='date']").val('');
        }
        thaalField.find("input[type='number'], input[type='date']").prop("disabled", true).prop("required", false);

        tiffanField.show();
        tiffanField.find("input[type='number'], input[type='date']").prop("disabled", true);
        tiffanField.find("input[type='text']").prop("disabled", true);
      }
    }

    function validateAllDates() {
      let isValid = true;

      $('.row').each(function() {
        const row = $(this);
        const dateFrom = row.find('input[name^="date_from"]:not(:disabled)').val();
        const dateTo = row.find('input[name^="date_to"]:not(:disabled)').val();

        row.find('.date-error').remove();
        if (dateFrom && dateTo && new Date(dateFrom) > new Date(dateTo)) {
          row.find('input[name^="date_to"]').after('<span class="text-danger date-error">Date To must be greater than Date From.</span>');
          isValid = true;
        }
      });
      $(".btn-primary").prop("disabled", isValid);
    }

    $('input[name^="date_from"]:not(:disabled), input[name^="date_to"]:not(:disabled)').on('change', validateAllDates);

    // Initial check in case form loads with existing data
    validateAllDates();

    $('#serving').on("change", toggleFields);

    $('#serving').trigger("change")
    if (<?php echo json_encode($errors->any()); ?>) {
      toggleFields(false);
    }
    let anyError = <?php echo json_encode($errors->any()); ?>;
    console.log(<?php echo json_encode($errors->all()); ?>)
  });
</script>
@endpush