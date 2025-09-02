@push('styles')
<style>
.table td,
.table th{
  width: 32%;
  min-width: 32%;
}
</style>
@endpush
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
        <div class="d-flex justify-content-between border-bottom pb-2 border-bottom-3 border-primary my-3">
          <h2>
            {{$result->event->name}}
          </h2>
          <div>
            <svg class="close-icon cursor-pointer" style="display: none;" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 682.667 682.667" xml:space="preserve">
              <g>
                <defs>
                  <clipPath id="a" clipPathUnits="userSpaceOnUse">
                    <path d="M0 512h512V0H0Z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)"></path>
                  </clipPath>
                </defs>
                <g clip-path="url(#a)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)">
                  <path d="M0 0h157.333M-28.961 236h215.255a120 120 0 0 0 84.853-35.147l8.372-8.373a119.994 119.994 0 0 0 35.148-84.852v-215.256a119.994 119.994 0 0 0-35.148-84.852l-8.372-8.373A120 120 0 0 0 186.294-236H-28.961a120.001 120.001 0 0 0-84.853 35.147l-8.373 8.373a119.996 119.996 0 0 0-35.146 84.852v215.256a119.996 119.996 0 0 0 35.146 84.852l8.373 8.373A120.001 120.001 0 0 0-28.961 236Z" style="stroke-width:40;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(177.333 256)" fill="none" stroke="var(--bs-primary)" stroke-width="40" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="var(--bs-primary)" class=""></path>
                </g>
              </g>
            </svg>
            <svg class="open-icon cursor-pointer" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 24 24" xml:space="preserve">
              <g>
                <path d="M18 2c2.206 0 4 1.794 4 4v12c0 2.206-1.794 4-4 4H6c-2.206 0-4-1.794-4-4V6c0-2.206 1.794-4 4-4zm0-2H6a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h12a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)" class=""></path>
                <path d="M12 18a1 1 0 0 1-1-1V7a1 1 0 0 1 2 0v10a1 1 0 0 1-1 1z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)" class=""></path>
                <path d="M6 12a1 1 0 0 1 1-1h10a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)" class=""></path>
              </g>
            </svg>
          </div>
        </div>
        <div class="event-detail" style="display: none;">
          <div class="row text-capitalize" >
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Event Place</label>
              <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
                {{$result->event->place->name}}
              </h5>

            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Event Date</label>
              <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
                {{\Carbon\Carbon::parse($result->event->date)->isoFormat('Do MMM YYYY')}}
                 
                <!-- {{old('date', \Carbon\Carbon::parse($result->event->date)->format('jS F Y') )}} -->
              </h5>
            </div>
            <div class="col-md-4 col-lg-3 mb-5 d-none">
                <label class="form-label">Start Time</label>
                <input hidden class="form-control" type="time" id="start" name="start" value="{{old('start',$result->event->start)}}">
                <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                  {{old('start',$result->start)}}
                </h5>
              </div>
              <div class="col-md-4 col-lg-3 mb-5 d-none">
                <label class="form-label">End Time</label>
                <input hidden class="form-control" type="time" id="end" name="end" value="{{old('end',$result->event->end)}}">
                <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                  {{old('end',$result->end)}}
                </h5>
              </div>
            @if($result->event->serving != "tiffin")
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Event Time</label>
              <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
                {{\Carbon\Carbon::parse($result->event->start)->isoFormat('hh:mm A')}} -
                {{\Carbon\Carbon::parse($result->event->end)->isoFormat('hh:mm A')}}
              </h5>
            </div>
            @endif
            <div class="col-md-4 col-lg-3 mb-5 {{ $result->event->serving == 'tiffin' ? 'd-none' : '' }}">
              <label class="form-label">Event Hours</label>
              <!-- <input disabled class="form-control" name="event_hours" id="diff" readonly> -->
              <h5 id="diff" class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;"></h5>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Meal </label>
              <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
                {{$result->event->meal}}
              </h5>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Serving </label>
              <h5 class="border-primary text-capitalize" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
                {{$result->event->serving}}
              </h5>
            </div>
            @if ($result->event->serving == 'thaal')
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">No. of Thaal (For 8 person)</label>
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                {{old('no_of_thaal',$result->event->no_of_thaal)}}
              </h5>
              <input hidden type="number" min="0" class="form-control" name="no_of_thaal" id="no_of_thaal" value="{{old('no_of_thaal',$result->no_of_thaal)}}">
            </div>
            @endif
            @foreach($tiffinSizes as $index => $tiffinSize)
                  @php
                  $existingTiffin = collect($servingItems)->firstWhere('tiffin_size_id', $tiffinSize->id);
                  $isChecked = $existingTiffin ? 'checked' : '';
                  $tiffinCount = $existingTiffin ? $existingTiffin['count'] : 1;
                  @endphp
                  <div class="col-md-4 col-lg-3 mb-5 {{$existingTiffin ? '':'d-none'}}">
                    <label class="form-label text-capitalize">{{ $tiffinSize->name }} (For <span> {{$tiffinSize->person_no}}</span> person{{$tiffinSize->person_no == 1 ? "":"s" }})</label>
                    <!-- <div class="d-flex border border-1 rounded-4 disabled" >
                    <div class="bg-light p-3 d-flex justify-content-center align-items-center {{$existingTiffin ? 'd-none':''}}">
                      <input  hidden type="number" value="{{$existingTiffin ? $existingTiffin['id'] : ''}}" name="serving_item[]" class="form-control" />
                      <input disabled class="tiffan_type" type="checkbox" name="tiffan_type[]" value="{{ $tiffinSize->id }}" id="tiffinSize_{{ $tiffinSize->id }}"
                        {{ $isChecked }}>
                    </div>
                    < !-- <input disabled type="number" min="0" value="{{ $tiffinCount }}" name="no_of_taffin[]" id="no_of_taffin_{{$tiffinSize->id}}" placeholder="No. of Tiffin" class="form-control no_of_taffin "/> -- >
                  </div> -->
                    <input hidden type="number" min="0" value="{{ $tiffinCount }}" name="no_of_taffin[]" id="no_of_taffin_{{$tiffinSize->id}}" placeholder="No. of Tiffin" class="form-control no_of_taffin " />
                    <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                      {{ $tiffinCount }}
                    </h5>
                  </div>
                  @endforeach
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Serving Persons </label>
              <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
                {{$result->event->serving_persons}}
              </h5>
            </div>
          </div>
          <div class="row">
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
        <form class="form" id="chef-input-from" method="POST" action="{{ route('menus.chef-input.update', $result->id) }}">
          @csrf
          <div class="col-12 my-3">
            <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Chef Menu</h2>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label required">Dish</label>

            <select required class="form-select dish_id" multiple id="dish_id" name="dish_id[]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
              <option value=""></option>
              @php $dishIdsArray = is_array($dishIds) ? $dishIds : $dishIds->toArray(); @endphp

              @foreach($dishRecipes as $dishRecipe)
              <option value="{{ $dishRecipe->id }}" data-dish-category="{{$dishRecipe->dish->dishCategory->name}}"
                @if(is_array(old('dish_id')))
                {{ in_array($dishRecipe->dish->id, old('dish_id')) ? 'selected' : '' }}
                @elseif(in_array($dishRecipe->dish->id, $dishIdsArray))
                selected
                @endif
                >{{ $dishRecipe->dish->name }}</option>
              @endforeach
            </select>
            @if ($errors->has('dish_id'))
            <span class="text-danger">{{ $errors->first('dish_id') }}</span>
            @endif
          </div>
          <input hidden name="place_id" value="{{$result->event->place->id}}" />
          <input hidden name="event_id" value="{{$result->event->id}}" />
          @foreach($recipes as $recipe)
          <div class="main-row" id="dish-{{$recipe->id}}">
            <input hidden class="recipe_id" value="{{$recipe->id}}" />
            @if($recipe->chefRecipeItems && $recipe->chefRecipeItems->count())
            <h2>{{$recipe->dish->name}} - {{$recipe->dish->dishCategory->name}}</h2>
            <input hidden type="text" id="total_ingredient" value="{{ old('total_ingredient',$recipe->recipeItems->count()) ?? 1 }}" name="total_ingredient" />
            <input hidden name="purchase_order_id" value="{{ $purchaseOrderIds ?? '' }}" />
            <hr />
            @endif
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>UOM</th>
                    <th class="text-center"> </th>
                  </tr>
                </thead>
                <tbody>

                  @foreach($recipe->chefRecipeItems->reverse() as $chefRecipeItem_index => $chefRecipeItem)
                  <tr>
                    <td>
                      <select required class="form-select item" name="items[{{$recipe->id}}][{{$chefRecipeItem_index}}][ingredient_id]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                        <option value=""></option>
                        @foreach($items as $item)
                        <option value="{{ $item->id }}" {{old('',$chefRecipeItem->item->id) == $item->id ? "selected" :"" }}>{{ $item->name }}</option>
                        @endforeach
                      </select>
                      @if ($errors->has('items.*.*.ingredient_id'))
                      <span class="text-danger">{{ $errors->first('items.*.*.ingredient_id') }}</span>
                      @endif
                    </td>
                    <td>
                      <input required type="number" step="0.001" class="form-control" name="items[{{$recipe->id}}][{{$chefRecipeItem_index}}][item_quantity]" id="item_quantity" value="{{$chefRecipeItem->item_quantity}}">

                      @if ($errors->has('items.*.*.item_quantity'))
                      <span class="text-danger">{{ $errors->first('items.*.*.item_quantity') }}</span>
                      @endif
                    </td>
                    <td>
                      <div class="position-relative">
                        <div class="unit_id_loader align-items-center bg-gray-100 d-none h-100 justify-content-center position-absolute start-50 top-50 translate-middle w-100 z-index-2">
                          <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                          </div>
                        </div>
                        <select data-measure-id="{{$chefRecipeItem->measurement->id}}" required class="form-select unit_measure" name="items[{{$recipe->id}}][{{$chefRecipeItem_index}}][unit_measure]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                          <option value=""></option>
                        </select>
                      </div>

                      @if ($errors->has('items.*.*.item_quantity'))
                      <span class="text-danger">{{ $errors->first('items.*.*.item_quantity') }}</span>
                      @endif
                    </td>
                    <td style="vertical-align: middle;" class="text-center">
                      <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class="cursor-pointer add-more-button me-3">
                        <g>
                          <path d="M18 2c2.206 0 4 1.794 4 4v12c0 2.206-1.794 4-4 4H6c-2.206 0-4-1.794-4-4V6c0-2.206 1.794-4 4-4zm0-2H6a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h12a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6z" fill="var(--bs-primary)" opacity="1" data-original="#000000" class=""></path>
                          <path d="M12 18a1 1 0 0 1-1-1V7a1 1 0 0 1 2 0v10a1 1 0 0 1-1 1z" fill="var(--bs-primary)" opacity="1" data-original="#000000" class=""></path>
                          <path d="M6 12a1 1 0 0 1 1-1h10a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1z" fill="var(--bs-primary)" opacity="1" data-original="#000000" class=""></path>
                        </g>
                      </svg>
                      @if (($recipe->chefRecipeItems->count() - 1) != $chefRecipeItem_index)
                        <svg id="ingredient-detail-' . $id . '" title="Remove" class="cursor-pointer remove-btn" xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 384 384">
                          <g>
                            <path d="M64 341.333C64 364.907 83.093 384 106.667 384h170.667C300.907 384 320 364.907 320 341.333v-256H64v256zM266.667 21.333 245.333 0H138.667l-21.334 21.333H42.667V64h298.666V21.333z" fill="#f42c2c" />
                          </g>
                        </svg>
                      @endif

                      <textarea class="form-control d-none" name="items[{{$recipe->id}}][{{$chefRecipeItem_index}}][description]">{{$chefRecipeItem->description}}</textarea>

                      @if ($errors->has('items.*.*.description'))
                      <span class="text-danger">{{ $errors->first('items.*.*.description') }}</span>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

          </div>
          @endforeach
          <div class="dish-list"></div>
          <div class="col-md-4 col-lg-3 mb-5">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" id="description">{{old('description',$result)}}</textarea>
          </div>
          <div class="col-md-12 mt-3">
            <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2" style="margin-right: 5px">

            <button type="submit" class="btn btn-primary hover-elevate-up w-100px submit-button">
              <span class="spinner-border spinner-border-custom d-none" role="status" aria-hidden="true"></span>
              <span class="button-text"> Update </span>
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
  @endsection

  @push('scripts')
  <script>
    $(document).on('change', '.item', function() {
      let loader = $(this);
      let unit_measure = $(loader).closest("tr").find(".unit_measure");
      let currentUnitMeasure = unit_measure.data('measure-id')
      $(this).closest("tr").find('.unit_id_loader').addClass("d-flex").removeClass("d-none");

      unit_measure.empty().append('<option value="" disabled selected>Select</option>'); // Keep the empty option

      let data = {
        id: $(this).val(),
      };
      $.get("/fetch-unit", data, function(response) {
        response?.result?.forEach(function(item) {
          $(unit_measure).select2().append(new Option(item.short_form, item.id, ((response?.result.length == 1 ? true : false) || item.id == currentUnitMeasure), ((response?.result.length == 1 ? true : false) || item.id == currentUnitMeasure))).trigger('change');
        });
        $(loader).closest("tr").find('.unit_id_loader').addClass("d-none").removeClass("d-flex");
      });
    });
    $(document).on('change', '.unit_measure', function() {
      let loader = $(this);
      let item = $(loader).closest("tr").find(".item").val();
      let itemName = $(loader).closest("tr").find(".item option:selected").text();

      if (item != null && $(this).val() != null) {

        let data = {
          item,
          unit_measure: $(this).val(),
        };
        $.get("/fetch-uom-base", data, function(response) {
          if (response.success == false) {
            $('.submit-button').prop("disabled", true);
            alert(`The UOM conversion for ${response?.itemBaseUom?.base_uom?.name} to ${$(loader).find("option:selected").text()} is not set.`)
          } else {
            $('.submit-button').prop("disabled", false);
          }
        });
      }
    })
    $(document).on('click', '.add-more-button', function() {
      let totalItems = "{{$items->count()}}";
      let row = $(this).closest(".main-row");
      let currentID = $(row).find('.recipe_id').val();

      let selectItems = $(this).closest(".main-row").find('select.item');
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
        $(row).find('.table-responsive tbody').append(moreIngredient);
        $(`[data-control="select2"]`).select2();
      })
      $('html, body').animate({
        scrollTop: $(document).height()
      }, 500);
    });
    $(document).on("click", '.remove-btn', function() {
      $(this).closest(`tr`).remove();
    });
    $(document).on("click", '.submit-button', function(event) {
      event.preventDefault();
      // let itemValues = $('select.item').map(function() {
      //   if ($(this).val() !== '') {
      //     return $(this).val();
      //   }
      // }).get();

      // let uniqueValues = [...new Set(itemValues)];
      // if (uniqueValues.length !== itemValues.length) {
      //   alert("Duplicates items found:");
      //   return;
      // }

      let isValid = true;

$('#chef-input-from').find('select, input').each(function() {
  const value = $(this).val();

  // Handle both strings and arrays (like <select multiple>)
  const isEmpty = Array.isArray(value)
    ? value.length === 0
    : String(value).trim() === '';

  if ($(this).prop('required') && isEmpty) {
    isValid = false;
    return false; // break loop
  }
});

if (!isValid) {
  alert("Please fill in all required fields.");
  return;
}
      $('#chef-input-from').submit();
    });
    $('.item').trigger("change")

    $(".dish_id").on("select2:select", function(e) {
      const selectedText = e.params.data.text;
      const selectedId = e.params.data.id;
      const category = $(this).find('option[value="' + selectedId + '"]').data('dish-category');
      let text = selectedText.split('-')

      let dishLength = $(this).val().length;

      let fields = `<div class="row" id="dish-${selectedId}">
                  <input hidden class="recipe_id" value="${selectedId}" />
                <div class="col-12 my-3">
                  <div class="border-bottom pb-2 border-bottom-3 border-primary mb-3 pb-2 my-2 d-flex justify-content-between align-items-center">
                    <h2> ${(text[0] ?? "-") + " - " + category}  </h2>
                    <div>
                      <button type="button" id="add-more-button" data-dish-id="${selectedId}" class="btn btn-sm btn-primary add-more-button me-2 mb-2">
                        Add More
                      </button>
                    </div>
                  </div>
                </div>
                <div class="row current-ingredient">
                  <div class="col-md-4 col-lg-3 mb-5">
                    <label class="form-label required">Item</label>
                    <select required class="form-select item" name="items[${selectedId}][${dishLength}][ingredient_id]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                      <option value=""></option>
                      @foreach($items as $item)
                      <option value="{{ $item->id }}">{{ $item->name }}</option>
                      @endforeach
                    </select>
                    
                  </div>

                  <div class="col-md-4 col-lg-3 mb-5">
                    <label class="form-label required">Item Quantity</label>
                    <input required type="number" step="0.001" class="form-control" name="items[${selectedId}][${dishLength}][item_quantity]" id="item_quantity" value="">
                  </div>

                  <div class="col-md-4 col-lg-3 mb-5">
                    <label class="form-label required">Unit of Measure</label>
                    <div class="position-relative">
                      <div class="unit_id_loader align-items-center bg-gray-100 d-none h-100 justify-content-center position-absolute start-50 top-50 translate-middle w-100 z-index-2">
                        <div class="spinner-border" role="status">
                          <span class="visually-hidden">Loading...</span>
                        </div>
                      </div>
                      <select required class="form-select unit_measure" name="items[${selectedId}][${dishLength}][unit_measure]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                        <option value=""></option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4 col-lg-3 mb-5">
                    <div class="d-flex justify-content-between align-items-center">
                      <label class="form-label">Description</label>
                    </div>
                    <textarea class="form-control" name="items[${selectedId}][${dishLength}][description]"></textarea>
                  </div>
                </div>
                <div class="row more-ingredient"></div>
              </div>`;
      $('.dish-list').append(fields);
      $('select').select2()
    });


    // When an item is unselected (optional)
    $(".dish_id").on("select2:unselect", function(e) {
      const unselectedText = e.params.data.text;
      const unselectedId = e.params.data.id;
      $(`#dish-${unselectedId}`).remove();
    });
    $('.open-icon').on('click', function() {
      $(this).hide();
      $('.close-icon').show();
      $('.event-detail').slideDown(300); // 300ms animation
    });

    $('.close-icon').on('click', function() {
      $(this).hide();
      $('.open-icon').show();
      $('.event-detail').slideUp(300);
    });
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
    $('form').on('submit', function () {
      const button = $('.submit-button');
      $('.button-text').hide()
      button.prop('disabled', true);
      button.find('.spinner-border').removeClass('d-none'); // show spinner
    });
    // Initial calculation when the page loads
    updateDifference();
  </script>
  @endpush