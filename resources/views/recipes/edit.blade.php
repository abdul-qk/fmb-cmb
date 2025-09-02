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
        <form id="recipe-from" class="form" method="POST" action="{{ route($update, ['recipe' => $result->id]) }}">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Dish</label>
              <select required class="form-select " id="dish_id" name="dish_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($dishes as $dish)
                <option value="{{ $dish->id }}" {{ old('dish_id',$result->dish_id) == $dish->id ? 'selected' : '' }}>{{ $dish->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('dish_id'))
              <span class="text-danger">{{ $errors->first('dish_id') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Serving</label>
              <select class="form-select" id="serving" name="serving_item" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                <option value="tiffin" {{ old('serving_item',$result->serving_item) == 'tiffin' ? 'selected' : '' }}>Tiffin</option>
                <option value="thaal" {{ old('serving_item',$result->serving_item) == 'thaal' ? 'selected' : '' }}>Thaal</option>
                <option value="thaal-tiffin" {{ old('serving_item',$result->serving_item) == 'thaal-tiffin' ? 'selected' : '' }}>Thaal + Tiffin</option>
              </select>
              @if ($errors->has('serving_item'))
              <span class="text-danger">{{ $errors->first('serving_item') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Serving Pax</label>
              <input type="number" class="form-control" name="serving" id="serving" value="{{old('serving',$result->serving)}}">
              @if ($errors->has('serving'))
                <span class="text-danger">{{ $errors->first('serving') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Place</label>
              <select required class="form-select " id="place_id" name="place_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($places as $place)
                <option value="{{ $place->id }}" {{ old('place_id',$result->place_id) == $place->id ? 'selected' : '' }}>{{ $place->name }} - {{$place->location->area}}</option>
                @endforeach
              </select>
              @if ($errors->has('place_id'))
              <span class="text-danger">{{ $errors->first('place_id') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Chef</label>
              <select required class="form-select" id="chef" name="chef" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($chefs as $chef)
                <option value="{{ $chef->id }}" {{ old('chef',$result->chef) == $chef->id ? 'selected' : '' }}>{{ $chef->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('chef'))
              <span class="text-danger">{{ $errors->first('chef') }}</span>
              @endif
            </div>
            
            <div class="col-md-12">
              <div class="border-bottom border-2 pb-2 my-2 d-flex justify-content-between">
                <h2>Ingredients</h2>
                <input hidden type="text" id="total_ingredient" value="{{ old('total_ingredient',$totalRecipeItems) ?? 1 }}" name="total_ingredient" />
                <button type="button" id="add-more-button" class="btn btn-sm btn-primary">
                  Add More
                </button>
              </div>

              @foreach($recipeItems as $recipeItem)
              <div class="row current-ingredient">
                <div class="col-md-4 col-lg-3 mb-5">
                  <label class="form-label required">Item</label>
                  <input hidden name="recipeItem_{{ $loop->iteration }}" value="{{$recipeItem->id}}" />
                  <select required class="form-select item" name="item_{{ $loop->iteration }}" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option value=""></option>
                    @foreach($items as $item)
                    <option value="{{ $item->id }}" {{ old('item_' . $loop->iteration, $recipeItem->item_id) == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @endforeach
                  </select>
                  @if ($errors->has('item_' . $loop->iteration))
                  <span class="text-danger">{{ $errors->first('item_' . $loop->iteration) }}</span>
                  @endif
                </div>
                <div class="col-md-4 col-lg-3 mb-5">
                  <label class="form-label required">Item Quantity</label>
                  <input type="number" step="0.001" class="form-control" name="item_quantity_{{ $loop->iteration }}" id="item_quantity_{{ $loop->iteration }}" value="{{ old('item_quantity_' . $loop->iteration, $recipeItem->item_quantity) }}">
                  @if ($errors->has('item_quantity_'. $loop->iteration))
                    <span class="text-danger">{{ $errors->first('item_quantity_'. $loop->iteration) }}</span>
                  @endif
                </div>
                <div class="col-md-4 col-lg-3 mb-5">
                  <label class="form-label required">Unit of Measure</label>
                  <div class="position-relative">
                    <div class="unit_id_loader align-items-center bg-gray-100 d-none h-100 justify-content-center position-absolute start-50 top-50 translate-middle w-100 z-index-2">
                      <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </div>
                    <select required class="form-select unit_measure" name="unit_measure_{{ $loop->iteration }}" data-id="{{$recipeItem->measurement_id}}" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                      <option value=""></option>
                    </select>
                  </div>

                  <!-- <select required class="form-select " name="unit_measure_{{ $loop->iteration }}" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option value=""></option>
                    @foreach($unitMeasures as $unitMeasure)
                    <option value="{{ $unitMeasure->id }}" {{ old('unit_measure_' . $loop->iteration, $recipeItem->measurement_id) == $unitMeasure->id ? 'selected' : '' }}>{{ $unitMeasure->short_form }}</option>
                    @endforeach
                  </select> -->
                  @if ($errors->has('unit_measure_' . $loop->iteration))
                  <span class="text-danger">{{ $errors->first('unit_measure_' . $loop->iteration) }}</span>
                  @endif
                </div>
                <div class="col-md-4 col-lg-3 mb-5">
                  <label class="form-label">Description</label>
                  <textarea class="form-control" name="description_{{ $loop->iteration }}">{{ old('description_' . $loop->iteration, $recipeItem->description) }}</textarea>
                  @if ($errors->has('description_' . $loop->iteration))
                  <span class="text-danger">{{ $errors->first('description_' . $loop->iteration) }}</span>
                  @endif
                </div>
              </div>
              @endforeach

              <div class="row more-ingredient">

              </div>
            </div>

            <div class="col-md-12">
              <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2" style="margin-right: 5px">
              <input type="button" value="Update" class="submit-button btn btn-primary hover-elevate-up w-100px">
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
  $(document).on('change','.item', function() {
    let loader = $(this);
    let unit_measure = $(loader).closest(".row").find(".unit_measure");
    let current_unit_measure = unit_measure.data('id');
    $(this).closest(".row").find('.unit_id_loader').addClass("d-flex").removeClass("d-none");
    unit_measure.empty().append('<option value="" disabled selected>Select</option>'); // Keep the empty option

    let data = {
      id: $(this).val(),
    };
    $.get("/fetch-unit", data, function(response) {
      response?.result?.forEach(function(item) {
        $(unit_measure).select2().append(new Option(item.name, item.id, current_unit_measure ==  item.id, current_unit_measure ==  item.id)).trigger('change');
      });
      $(loader).closest(".row").find('.unit_id_loader').addClass("d-none").removeClass("d-flex");
    });
  });
  $(document).on('change', '.unit_measure', function() {
    let loader = $(this);
    let item = $(loader).closest(".row").find(".item").val();
    let itemName = $(loader).closest(".row").find(".item option:selected").text();

    if (item != null && $(this).val() != null) {

      let data = {
        item,
        unit_measure: $(this).val(),
      };
      $.get("/fetch-uom-base", data, function(response) {
        console.log(response)
        if(response.success == false) {
          $('.submit-button').prop("disabled",true);
          alert(`The UOM conversion for ${response?.itemBaseUom?.base_uom?.name} to ${$(loader).find("option:selected").text()} is not set.`)
        }else {
          $('.submit-button').prop("disabled",false);
        }
      });
    }
  })
  $('#add-more-button').on('click', function() {
    let totalItems = "{{$items->count()}}";
    let itemValues = $('select.item').map(function() {
      if ($(this).val() !== '') {
        return $(this).val();
      }
    }).get();

    let filledCount = itemValues.length;

    // Get the total count of items
    let totalCount = $('select.item').length;

    if (totalCount > filledCount) {
      alert("Item is not selected")
      return;
    }
    if (totalItems == filledCount) {
      alert("No more item to select")
      return;
    }

    let ingredient = parseInt($("#total_ingredient").val())
    $("#total_ingredient").val(++ingredient);
    let data = {
      id: ingredient,
      items: itemValues
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

  $(document).on("click", '.submit-button', function(event) {
    event.preventDefault();
    let itemValues = $('select.item').map(function() {
      if ($(this).val() !== '') {
        return $(this).val();
      }
    }).get();

    let uniqueValues = [...new Set(itemValues)];
    if (uniqueValues.length !== itemValues.length) {
      alert("Duplicates items found:");
      return;
    }

    $('#recipe-from').submit();
  });

  function defaultFunction() {
    $('#dish_id').trigger("change")
    $('.item').trigger("change")
  }
  defaultFunction()
</script>
@endpush