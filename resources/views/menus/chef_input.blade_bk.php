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

        <div class="row text-capitalize">
          <div class="col-12 my-3">
            <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Event Details</h2>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Event Place</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{$result->event->place->name}}
            </h5>
            
            
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Event Name</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{$result->event->name}}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Event Date</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{\Carbon\Carbon::parse($result->event->date)->isoFormat('Do MMM YYYY')}}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Event Time</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{\Carbon\Carbon::parse($result->event->start)->isoFormat('hh:mm A')}} -
              {{\Carbon\Carbon::parse($result->event->end)->isoFormat('hh:mm A')}}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Meal </label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{$result->event->meal}}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">serving </label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{$result->event->serving}}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Serving Persons </label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{$result->event->serving_persons}}
            </h5>
          </div>
          <form class="form" method="POST" action="{{ route('menus.chef-input.store', $result->id) }}">
            @csrf
            <input hidden name="place_id" value="{{$result->event->place->id}}" />
            <input hidden name="event_id" value="{{$result->event->id}}" />
            @foreach($result->recipes as $recipe)
            <div class="row">
                <input hidden class="recipe_id" value="{{$recipe->id}}" />

            
                <div class="col-12 my-3">

                  <div class="border-bottom pb-2 border-bottom-3 border-primary mb-3 pb-2 my-2 d-flex justify-content-between">
                    <h2>{{$recipe->dish->name}} - {{$recipe->dish->dishCategory->name}}</h2>
                    <input hidden type="text" id="total_ingredient" value="{{ old('total_ingredient',$recipe->recipeItems->count()) ?? 1 }}" name="total_ingredient" />

                    <button type="button" id="add-more-button" class="btn btn-sm btn-primary add-more-button">
                      Add More
                    </button>
                  </div>

                </div>
                @foreach($recipe->recipeItems as $recipeItem)
                <div class="row current-ingredient">
                  <div class="col-md-4 col-lg-3 mb-5">
                    <label class="form-label required">Item</label>
                    <select required class="form-select item" name="items[{{$recipe->id}}][{{$recipeItem->id}}][ingredient_id]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                      <option value=""></option>
                      @foreach($items as $item)
                      <option value="{{ $item->id }}" {{ old('items.' . $recipe->id . '.' . $recipeItem->id . '.ingredient_id', $recipeItem->item->id) == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                      @endforeach
                    </select>
                    @if ($errors->has('items.*.*.ingredient_id'))
                      <span class="text-danger">{{ $errors->first('items.*.*.ingredient_id') }}</span>
                    @endif
                  </div>

                  <div class="col-md-4 col-lg-3 mb-5">
                    <label class="form-label required">Item Quantity</label>
                    <input type="number" step="0.001" class="form-control" name="items[{{$recipe->id}}][{{$recipeItem->id}}][item_quantity]" id="item_quantity" value="{{ old('items.' . $recipe->id . '.' . $recipeItem->id . '.item_quantity') }}">
                    
                    @if ($errors->has('items.*.*.item_quantity'))
                      <span class="text-danger">{{ $errors->first('items.*.*.item_quantity') }}</span>
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
                      <select required class="form-select unit_measure" name="items[{{$recipe->id}}][{{$recipeItem->id}}][unit_measure]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                        <option value=""></option>
                      </select>
                    </div>
                    
                    @if ($errors->has('items.*.*.item_quantity'))
                      <span class="text-danger">{{ $errors->first('items.*.*.item_quantity') }}</span>
                    @endif
                  </div>

                  <div class="col-md-4 col-lg-3 mb-5">
                    <div class="d-flex justify-content-between align-items-center">
                      <label class="form-label">Description</label>
                    </div>
                    <textarea class="form-control" name="items[{{$recipe->id}}][{{$recipeItem->id}}][description]">{{ old('items.' . $recipe->id . '.' . $recipeItem->id . '.description', $recipeItem->description) }}</textarea>
                  
                    @if ($errors->has('items.*.*.description'))
                      <span class="text-danger">{{ $errors->first('items.*.*.description') }}</span>
                    @endif
                  </div>
                </div>
                @endforeach
                <div class="row more-ingredient"></div>
              </div>
            @endforeach
            <div class="col-md-12 mt-3">
              <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2" style="margin-right: 5px">
              
              <button type="submit" class="btn btn-primary hover-elevate-up w-100px submit-button">
                <span class="spinner-border spinner-border-custom d-none" role="status" aria-hidden="true"></span>
                <span class="button-text"> Create </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  @endsection

  @push('scripts')
  <script>
    $(document).on('change', '.item', function() {
      let loader = $(this);
      let unit_measure = $(loader).closest(".row").find(".unit_measure");
      $(this).closest(".row").find('.unit_id_loader').addClass("d-flex").removeClass("d-none");

      unit_measure.empty().append('<option value="" disabled selected>Select</option>'); // Keep the empty option

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
          if (response.success == false) {
            $('.submit-button').prop("disabled", true);
            alert(`The UOM conversion for ${response?.itemBaseUom?.base_uom?.name} to ${$(loader).find("option:selected").text()} is not set.`)
          } else {
            $('.submit-button').prop("disabled", false);
          }
        });
      }
    })
    $('.add-more-button').on('click', function() {
      let totalItems = "{{$items->count()}}";
      let row =   $(this).closest(".row");
      let currentID = $(row).find('.recipe_id').val();
      
      console.log({currentID})
      let selectItems = $(this).closest(".row").find('select.item');
      let itemValues = selectItems.map(function() {
        if ($(this).val() !== '') {
          return $(this).val();
        }
      }).get();

      let filledCount = itemValues.length;

      // Get the total count of items
      let totalCount = selectItems.length;

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
        items: itemValues,
        recipe_id: currentID
      };
      $.get("/fetch-get-ingredient-chef", data, function(response) {
        var moreIngredient = response?.currentIngredient;

        // $('.more-ingredient').prepend(moreIngredient);
        $(row).find('.more-ingredient').append(moreIngredient);
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
    $('.item').trigger("change")


    // if (anyError) {
    //   $('.item-select').trigger("change")
    // }
    $('form').on('submit', function () {
      const button = $('.submit-button');
      $('.button-text').hide()
      button.prop('disabled', true);
      button.find('.spinner-border').removeClass('d-none'); // show spinner
    });
  </script>
  @endpush