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
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label required">Location</label>
            <select disabled class="form-select " id="location_id" name="location_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
              <option value=""></option>
              @foreach($locations as $location)
              <option value="{{ $location->id }}" {{ old('location_id',$result->location_id) == $location->id ? 'selected' : '' }}>{{ $location->country->code .' - '. $location->city->name .' - '. $location->area }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4 col-lg-3 mb-5">
            <label class="form-label required">Name</label>
            <input disabled type="text" class="form-control" name="name" id="name" value="{{old('name',$result->name)}}">
          </div>
          <div class="col-md-4 col-lg-3 mb-5">
            <label class="form-label required">Contact No</label>
            <input disabled type="text" class="form-control" name="contact_no" id="contact_no" value="{{old('contact_no', $result->contact_no)}}">
          </div>
          <div class="col-md-4 col-lg-3 mb-5">
            <label class="form-label">Description</label>
            <textarea disabled class="form-control" name="description" id="description">{{old('description',$result->description)}}</textarea>

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
<script>
  // $('#dish_id').change(function() {

  //   $('#d_dish_category_loader').addClass("d-flex").removeClass("d-none");
  //   let options = `<option value=""></option>`
  //   let data = {
  //     id: $(this).val(),
  //   };

  //   $.get("/fetch-dish-category", data, function(response) {
  //     response?.result?.forEach(function(item) {
  //       options += `<option ${response?.result.length == 1 ? "selected" : "" } value="${item.id}">${item.name}</option>`;
  //     });
  //     $('#dish_category_id').html(options);
  //     $('#d_dish_category_loader').addClass("d-none").removeClass("d-flex");
  //   })
  // });
  $('#add-more-button').on('click', function() {
    let ingredient = parseInt($("#total_ingredient").val())
    $("#total_ingredient").val(++ingredient);
    let data = {
      id: ingredient,
    };
    $.get("/fetch-get-ingredient", data, function(response) {
      var moreIngredient = response?.currentIngredient;
      $('.more-ingredient').prepend(moreIngredient);
      $('select').select2();
    })
  });
  $(document).on("click", '.remove-btn', function() {
    let ingredient = parseInt($("#total_ingredient").val())
    $("#total_ingredient").val(--ingredient);
    $(this).closest(`.col-12`).remove();
  });

  function defaultFunction() {
    $('#dish_id').trigger("change")
  }
  defaultFunction()
</script>
@endpush