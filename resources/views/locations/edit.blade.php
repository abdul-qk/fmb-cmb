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
        <form class="form" method="POST" action="{{ route($update, $result->id) }}">
          @csrf
          @method('PUT')
          <div class="row">
            <!-- <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Country</label>
              <input type="text" class="form-control" name="country" id="country" value="{{old('country',$result->country)}}">
              @if ($errors->has('country'))
              <span class="text-danger">{{ $errors->first('country') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">City</label>
              <input type="text" class="form-control" name="city" id="city" value="{{old('city',$result->city)}}">
              @if ($errors->has('city'))
              <span class="text-danger">{{ $errors->first('city') }}</span>
              @endif
            </div> -->
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Country</label>
              <select class="form-select " id="country_id" name="country_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($countries as $country)
                <option value="{{ $country->id }}" {{ old('country_id',$result->country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('country_id'))
              <span class="text-danger">{{ $errors->first('country_id') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">City</label>
              <div class="position-relative">
                <div id="city_id_loader" class="align-items-center bg-gray-100 d-none h-100 justify-content-center position-absolute start-50 top-50 translate-middle w-100 z-index-2">
                  <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </div>
                <select class="form-select form-select-solid" id="city_id" name="city_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                  <option value=""></option>
                </select>
              </div>
              @if ($errors->has('city_id'))
              <span class="text-danger">{{ $errors->first('city_id') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Area</label>
              <input type="text" class="form-control" name="area" id="area" value="{{old('area',$result->area)}}">
              @if ($errors->has('area'))
              <span class="text-danger">{{ $errors->first('area') }}</span>
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
  $(document).ready(function() {
    let cityID = "{{$result->city_id}}";
    console.log({cityID})
    $('#country_id').change(function() {
      $('#city_id_loader').addClass("d-flex").removeClass("d-none");
      let portDD = `<option value=""></option>`
      let data = {
        id: $(this).val(),
      };

      $.get("/fetch-get-cities", data, function(response) {
        response?.cities?.forEach(function(item) {
          portDD += `<option  ${cityID == item.id ? 'selected': ''} value="${item.id}">${item.name}</option>`;
        });
        $('#city_id').html(portDD);
        $('#city_id_loader').addClass("d-none").removeClass("d-flex");
        // $('#portDD').html(portDD);
      });

    });
    $('#country_id').trigger("change")
  });
</script>
@endpush