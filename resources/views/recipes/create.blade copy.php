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
     <div id="kt_content_container" class="container-fluid">
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
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Dish</label>
              <select required class="form-select " id="dish_id" name="dish_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($dishes as $dish)
                <option value="{{ $dish->id }}" {{ old('dish_id') == $dish->id ? 'selected' : '' }}>{{ $dish->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('dish_id'))
              <span class="text-danger">{{ $errors->first('dish_id') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Serving</label>
              <input type="number" class="form-control" name="serving" id="serving" value="{{old('serving')}}">
              @if ($errors->has('serving'))
              <span class="text-danger">{{ $errors->first('serving') }}</span>
              @endif
            </div>
            <div class="col-md-3 mb-5">
              <label class="form-label required">Place</label>
              <select required class="form-select " id="place_id" name="place_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($places as $place)
                <option value="{{ $place->id }}" {{ old('place_id') == $place->id ? 'selected' : '' }}>{{ $place->name }} - {{$place->location->area}}</option>
                @endforeach
              </select>
              @if ($errors->has('place_id'))
              <span class="text-danger">{{ $errors->first('place_id') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Chef</label>
              <select required class="form-select" id="chef" name="chef" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($chefs as $chef)
                <option value="{{ $chef->id }}" {{ old('chef') == $chef->id ? 'selected' : '' }}>{{ $chef->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('chef'))
              <span class="text-danger">{{ $errors->first('chef') }}</span>
              @endif
            </div>

            <div class="col-md-12 mb-5">
              <div class="border-bottom border-2 pb-2 my-2 d-flex justify-content-between">
                <h2>Ingredients</h2>
                <input hidden type="text" id="total_ingredient" value="{{ old('total_ingredient') ?? 1 }}" name="total_ingredient" />
                <button type="button" id="add-more-button" class="btn btn-sm btn-primary">
                  Add More
                </button>
              </div>
              <div class="row current-ingredient">
                <div class="col-md-3 col-lg-3 mb-5">
                  <label class="form-label required">Item</label>
                  <select required class="form-select item" name="item_1" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option value=""></option>
                    @foreach($items as $item)
                    <option value="{{ $item->id }}" {{ old('item_1') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @endforeach
                  </select>
                  @if ($errors->has('item_1'))
                  <span class="text-danger">{{ $errors->first('item_1') }}</span>
                  @endif
                </div>
                <div class="col-md-3 col-lg-3 mb-5">
                  <label class="form-label required">Item Quantity</label>
                  <input type="number" step="0.001" class="form-control" name="item_quantity_1" id="item_quantity" value="{{old('item_quantity')}}">
                  @if ($errors->has('item_quantity'))
                  <span class="text-danger">{{ $errors->first('item_quantity') }}</span>
                  @endif
                </div>
                <div class="col-md-3 col-lg-3 mb-5">
                  <label class="form-label required">Unit of Measure</label>
                  <div class="position-relative">
                    <div class="unit_id_loader align-items-center bg-gray-100 d-none h-100 justify-content-center position-absolute start-50 top-50 translate-middle w-100 z-index-2">
                      <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </div>
                    <select required class="form-select unit_measure" name="unit_measure_1" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                      <option value=""></option>
                    </select>
                  </div>
                  @if ($errors->has('unit_measure_1'))
                  <span class="text-danger">{{ $errors->first('unit_measure_1') }}</span>
                  @endif
                </div>
                <div class="col-md-3 col-lg-3 mb-5">
                  <label class="form-label">Description</label>
                  <textarea class="form-control" name="description_1">{{old('description_1')}}</textarea>
                  @if ($errors->has('description_1'))
                  <span class="text-danger">{{ $errors->first('description_1') }}</span>
                  @endif
                </div>
              </div>
              <div class="row more-ingredient"></div>
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
  $(document).on('change','.item', function() {
    let loader = $(this);
    let unit_measure = $(loader).closest(".row").find(".unit_measure");
    $(this).closest(".row").find('.unit_id_loader').addClass("d-flex").removeClass("d-none");

    unit_measure.empty().append('<option value="" disabled selected>Search</option>'); // Keep the empty option

    let data = {
      id: $(this).val(),
    };
    $.get("/fetch-unit", data, function(response) {
      response?.result?.forEach(function(item) {
        $(unit_measure).select2().append(new Option(item.name, item.id, response?.result.length == 1 ? true : false, response?.result.length == 1 ? true : false)).trigger('change');
      });
      $(loader).closest(".row").find('.unit_id_loader').addClass("d-none").removeClass("d-flex");
    });
  });
  $('#add-more-button').on('click', function() {
    let ingredient = parseInt($("#total_ingredient").val())
    $("#total_ingredient").val(++ingredient);
    let data = {
      id: ingredient,
    };
    $.get("/fetch-get-ingredient", data, function(response) {
      var moreIngredient = response?.currentIngredient;
      // $('.more-ingredient').prepend(moreIngredient);
      $('.more-ingredient').append(moreIngredient);
      $('select').select2();
    })
  });
  $(document).on("click", '.remove-btn', function() {
    let ingredient = parseInt($("#total_ingredient").val())
    $("#total_ingredient").val(--ingredient);
    $(this).closest(`.col-12`).remove();
  });
</script>
@endpush