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
        <form class="form" method="POST" action="{{ route($store) }}">
          @csrf
          <div class="row">

            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Item</label>
              <select class="form-select " name="item_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($items as $result)
                <option value="{{ $result->id }}" {{ old('item_id') == $result->id ? 'selected' : '' }}>{{ $result->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('item_id'))
              <span class="text-danger">{{ $errors->first('item_id') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Base UOM </label>
              <select class="form-select " id="unit_measure_id" name="unit_measure_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($unitMeasure as $result)
                <option value="{{ $result->id }}" {{ old('unit_measure_id') == $result->id ? 'selected' : '' }}>{{ $result->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('unit_measure_id'))
              <span class="text-danger">{{ $errors->first('unit_measure_id') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Secondary UOM</label>
              <div class="position-relative">
                <div id="secondary_uom_loader" class="align-items-center bg-gray-100 d-none h-100 justify-content-center position-absolute start-50 top-50 translate-middle w-100 z-index-2">
                  <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </div>
                <select class="form-select" multiple id="secondary_uom" name="secondary_uom[]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
              </select>
              </div>
              @if ($errors->has('secondary_uom'))
              <span class="text-danger">{{ $errors->first('secondary_uom') }}</span>
              @endif
            </div>
            <div class="col-md-12 mt-3">
              <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2" style="margin-right: 5px">
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
<script>
  let anyError = <?php echo json_encode($errors->any()); ?>;
  console.log(<?php echo json_encode($errors->all()); ?>)

  if (anyError) {
    $('#country_id').trigger("change")
  }

  $(document).on("change", "#unit_measure_id", function() {
    const baseUomSelect = $('select[name="unit_measure_id"]').val();
    $('#secondary_uom_loader').addClass("d-flex").removeClass("d-none");
    
    let options = `
      @foreach($unitMeasure as $result)
      ${baseUomSelect != "{{ $result->id }}" ? 
        `<option value="{{ $result->id }}" {{ old('unit_measure_id') == $result->id ? 'selected' : '' }}>{{ $result->name }}</option>`
       :"" }
        @endforeach`;

      $('#secondary_uom').html(options);
      $('#secondary_uom_loader').addClass("d-none").removeClass("d-flex");
  });

</script>
@endpush