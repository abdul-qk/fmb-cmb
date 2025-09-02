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

      @if($errors->has('items'))
      <div class="alert alert-danger d-flex align-items-center p-5">
        <i class="ki-duotone ki-shield-tick fs-2hx text-danger me-4"></i>
        <div class="d-flex flex-column">
          <span>{{ $errors->first('items') }}</span>
        </div>
      </div>
      @endif

      @if($errors->has('items.*'))
      <div class="alert alert-danger d-flex align-items-center p-5">
        <i class="ki-duotone ki-shield-tick fs-2hx text-danger me-4"></i>
        <div class="d-flex flex-column">
          @foreach ($errors->get('items.*') as $error)
          <span>{{ $error[0] }}</span>
          @endforeach
        </div>
      </div>
      @endif
      <!--begin::Card-->
      <div class="bg-transparent border-0 card shadow-none pt-2">
        <form class="form" method="POST" action="{{ route($store) }}">
          @csrf
          <div class="row">
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Serving</label>
              <select class="form-select" id="serving" name="serving" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                <option value="Tiffin" {{ old('serving') == 'Tiffin' ? 'selected' : '' }}>Tiffin</option>
                <option value="Thaal" {{ old('serving') == 'Thaal' ? 'selected' : '' }}>Thaal</option>
              </select>
              @if ($errors->has('serving'))
              <span class="text-danger">{{ $errors->first('serving') }}</span>
              @endif
            </div>

            <div class="col-md-4 col-lg-3 mb-5 thaalField" style="display: none;">
              <label class="form-label required">Quantity</label>
              <input type="number" class="form-control" min="0" id="quantity" name="quantity" value="{{ old('quantity') }}">
              @if ($errors->has('quantity'))
              <span class="text-danger">{{ $errors->first('quantity') }}</span>
              @endif
            </div>

            <div class="col-md-4 col-lg-3 mb-5 thaalField" style="display: none;">
              <label class="form-label required">Date From</label>
              <input type="date" class="form-control" name="date_from" id="date_from" value="{{ old('date_from') }}">
              @if ($errors->has('date_from'))
              <span class="text-danger">{{ $errors->first('date_from') }}</span>
              @endif
            </div>

            <div class="col-md-4 col-lg-3 mb-5 thaalField" style="display: none;">
              <label class="form-label required">Date To</label>
              <input type="date" class="form-control" name="date_to" id="date_to" value="{{ old('date_to') }}">
              @if ($errors->has('date_to'))
              <span class="text-danger">{{ $errors->first('date_to') }}</span>
              @endif
            </div>
          </div>

          <div class="row">
            <div id="tiffanField" style="display: none;" class="col-md-12 mb-5">
              @foreach($results as $index => $item)
              <div class="tiffin-group">
                <h5 class="text-capitalize">Tiffin ({{ $item->name }} <span>{{ $item->person_no }}</span> pax{{ $item->person_no == 1 ? "" : "s" }})</h5>

                <input hidden type="text" min="0" value="{{ old('items.person_no.' . $item->id, $item->person_no) }}" name="items[{{ $index }}][person_no]" />
                <input hidden type="text" min="0" value="{{ old('items.tiffin_size_id.' . $item->id, $item->id) }}" name="items[{{ $index }}][tiffin_size_id]" id="tiffin_size_id_{{ $item->id }}" placeholder="No. of Tiffin" class="form-control tiffin_size_id" />
                <hr />

                <div class="row">
                  <div class="col-md-4 col-lg-3 mb-5">
                    <label class="form-label text-capitalize required">Quantity</label>
                    <input type="number" min="0" value="{{ old('items.' . $index . '.quantity') }}" name="items[{{ $index }}][quantity]" id="quantity_{{ $item->id }}" placeholder="Quantity" class="form-control" />
                  </div>

                  <div class="col-md-4 col-lg-3 mb-5">
                    <label class="form-label required">Date From</label>
                    <input type="date" class="form-control" name="items[{{ $index }}][date_from]" id="date_from_{{ $item->id }}" value="{{ old('items.' . $index . '.date_from') }}">
                    @if ($errors->has('items.date_from.' . $item->id))
                    <span class="text-danger">{{ $errors->first('items.date_from.' . $item->id) }}</span>
                    @endif
                  </div>

                  <div class="col-md-4 col-lg-3 mb-5">
                    <label class="form-label required">Date To</label>
                    <input type="date" class="form-control" name="items[{{ $index }}][date_to]" id="date_to_{{ $item->id }}" value="{{ old('items.' . $index . '.date_to') }}">
                    @if ($errors->has('items.date_to.' . $item->id))
                    <span class="text-danger">{{ $errors->first('items.date_to.' . $item->id) }}</span>
                    @endif
                  </div>
                </div>
              </div>
              @endforeach
            </div>

            <div class="col-md-12 mt-3">
              <input type="reset" value="Reset" class="btn btn-dark w-100px mr-2" style="margin-right: 5px">
              <input type="submit" value="Create" class="btn btn-primary hover-elevate-up w-100px">
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

      $(".btn-primary").prop("disabled", false);
      $(".date-error").remove();

      if (serving === 'Thaal') {
        thaalField.show();
        thaalField.find("input[type='number'], input[type='date']").prop("required", true).prop("disabled", false);
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
        tiffanField.find("input[type='number'], input[type='date']").prop("disabled", false);
        tiffanField.find("input[type='text']").prop("disabled", false);
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
          isValid = false;
        }
      });
      $(".btn-primary").prop("disabled", !isValid);
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