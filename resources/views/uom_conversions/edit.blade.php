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
              <label class="form-label required">Base UOM</label>
              <select class="form-select" id="base_uom" name="base_uom" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($unitMeasures as $unitMeasure)
                <option value="{{ $unitMeasure->id }}" {{ old('base_uom',$result->base_uom) == $unitMeasure->id ? 'selected' : '' }}>{{ $unitMeasure->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('base_uom'))
              <span class="text-danger">{{ $errors->first('base_uom') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Conversion Value </label>
              <input 
                type="text" 
                class="form-control" 
                name="conversion_value" 
                id="conversion_value" 
                value="{{ old('conversion_value', rtrim(rtrim(sprintf('%.10f', $result->conversion_value ?? 0), '0'), '.')) }}">
              @if ($errors->has('conversion_value'))
              <span class="text-danger">{{ $errors->first('conversion_value') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Secondary UOM</label>
              <select class="form-select" id="secondary_uom" name="secondary_uom" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($unitMeasures as $unitMeasure)
                <option value="{{ $unitMeasure->id }}" {{ old('secondary_uom',$result->secondary_uom) == $unitMeasure->id ? 'selected' : '' }}>{{ $unitMeasure->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('secondary_uom'))
              <span class="text-danger">{{ $errors->first('secondary_uom') }}</span>
              @endif
            </div>
            <div class="col-md-12 mt-3">
              <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2" style="margin-right: 5px">
              <input type="submit" value="Update" class="btn btn-primary hover-elevate-up w-100px">
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

  $(document).on("change", "#secondary_uom , #base_uom", function() {
    const baseUomSelect = $('select[name="base_uom"]').val();
    const conversionUomSelect = $('select[name="secondary_uom"]').val();
    console.log(baseUomSelect, conversionUomSelect);
    if (baseUomSelect == conversionUomSelect) {
      $('#create-btn').prop("disabled", true);
      alert("Base UOM and Secondary UOM must not be the same.");
    } else {
      $('#create-btn').prop("disabled", false);
    }
  })
</script>
@endpush