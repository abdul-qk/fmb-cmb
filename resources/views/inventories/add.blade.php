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
        <form class="form" method="POST" action="{{ route('inventories.add.store',1) }}">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Store</label>
              <select required class="form-select" id="store_id" name="store_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($stores as $store)
                <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>{{ $store->place->name }} - {{$store->floor_name}}</option>
                @endforeach
              </select>
              @if ($errors->has('store_id'))
              <span class="text-danger">{{ $errors->first('store_id') }}</span>
              @endif
            </div>
            <div id="items-section" style="display: block;">
              <hr />
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <!-- <th class="text-center">
                                      <input type="checkbox" class="form-check-input border border-1 border-white my-2" id="select-all" checked>
                                  </th> -->
                      <th style="vertical-align: middle;">Item</th>
                      <th style="vertical-align: middle;">Base UOM</th>
                      <th style="vertical-align: middle;">Available Qty</th>
                      <th style="vertical-align: middle;">UOM</th>
                      <th style="vertical-align: middle;">Qty</th>
                      <th title="Add More" class="text-center">
                        <svg class="add-items" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class="cursor-pointer">
                          <g>
                            <path d="M18 2c2.206 0 4 1.794 4 4v12c0 2.206-1.794 4-4 4H6c-2.206 0-4-1.794-4-4V6c0-2.206 1.794-4 4-4zm0-2H6a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h12a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6z" fill="#fff" opacity="1" data-original="#000000" class=""></path>
                            <path d="M12 18a1 1 0 0 1-1-1V7a1 1 0 0 1 2 0v10a1 1 0 0 1-1 1z" fill="#fff" opacity="1" data-original="#000000" class=""></path>
                            <path d="M6 12a1 1 0 0 1 1-1h10a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1z" fill="#fff" opacity="1" data-original="#000000" class=""></path>
                          </g>
                        </svg>
                      </th>
                    </tr>
                  </thead>
                  <tbody id="items-table-body">
                    <!-- @foreach($results as $index => $item)
                                    @if(is_null(old('selected_items')) || in_array($item['id'], old('selected_items', [])))
                                    <tr>
                                        <td style="vertical-align: middle;text-align: center;">
                                            <input type="checkbox" name="selected_items[]" value="{{ $item['id'] }}" class="select-item form-check-input" checked>
                                        </td>
                                        <td>
                                          <input type="text" class="form-control w-150px w-lg-100" value="{{ $item['name'] }}" disabled>
                                          <input type="text" hidden class="form-control w-150px w-lg-100 hidden-items" name="items[{{$index}}][item_id]" value="{{ $item['id'] }}">
                                        </td>
                                        <td>
                                          <input type="text" class="form-control w-150px w-lg-100" value="{{ $item['uom'] }}" disabled>
                                          <input type="text" hidden class="form-control w-150px w-lg-100 hidden-items" name="items[{{$index}}][unit_id]" value="{{ $item['uom_id'] }}">
                                        </td>
                                        <td>
                                            <input required type="number" min="0.01" step="0.001" class="form-control w-150px w-lg-100 quantity-input" name="items[{{$index}}][quantity]" value="0">
                                            @if ($errors->has("quantity.{$item['id']}"))
                                                <span class="text-danger">{{ $errors->first("quantity.{$item['id']}") }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach-->
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-md-12 mt-3">
              <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2" style="margin-right: 5px">
              <input type="submit" value="Add" class="btn btn-primary hover-elevate-up w-100px">
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
<script type="text/javascript">
  $(document).ready(function() {

    let itemIndex = {{ count(old('items', []))}};
    let fetchedItems = [];
    let fetchedUnits = [];

    $(document).on("click", '.remove-btn', function() {
      let IssueItem = $("select.item").val()
      $(this).closest(`tr`).remove();

      IssueItem.length == 0
    });
    $('.add-items').on('click', function() {
      let totalItems = "{{$results->count()}}";
      let itemValues = $('.item').map(function() {
        if ($(this).val() !== '') {
          return $(this).val();
        }
      }).get();

      let filledCount = itemValues.length;

      let filledSelectCount = $('select.item').map(function() {
        if ($(this).val() !== '') {
          return $(this).val();
        }
      }).get();
      filledSelectCount = filledSelectCount.length

      let totalCount = $('select.item').length;

      if (totalCount > filledSelectCount) {
        alert("Item is not selected")
        return;
      }
      if (totalItems == filledCount) {
        alert("No more item to select")
        return;
      }

      let ingredient = filledCount
      let data = {
        id: ingredient,
        items: itemValues
      };
      $.get("/fetch-get-item", data, function(response) {
        var moreIngredient = response?.currentIngredient;
        // $('.more-ingredient').prepend(moreIngredient);
        $('#items-table-body').append(moreIngredient);
        $('select').select2();
      })
    });
    $('.add-items').click();

    $(document).on('change', '.item', function() {
      let loader = $(this);
      let unit_measure = $(loader).closest("tr").find(".unit_measure");
      let item_quantity = $(loader).closest("tr").find(".item_quantity");
      let remaining_quantity = $(loader).closest("tr").find(".remaining_quantity");
      let select_item = $(loader).closest("tr").find(".selected_items");
      let baseUom = $(this).closest("tr").find(".base-uom");
      let availableQuantity = $(this).closest("tr").find(".available-quantity");

      item_quantity.prop('name', `quantity[${$(this).val()}]`)
      remaining_quantity.prop('name', `remaining_quantity[${$(this).val()}]`)
      select_item.val($(this).val())

      $(this).closest("tr").find('.unit_id_loader').addClass("d-flex").removeClass("d-none");

      unit_measure.empty().append('<option value="" disabled selected>Select</option>'); // Keep the empty option

      let data = {
        id: $(this).val(),
      };
      $.get("/fetch-item-details", data, function(response) {
        $(baseUom).val(response?.item_base?.base_uom?.name ?? '-');
        $(availableQuantity).val(response?.detail?.available_quantity ?? 0);
      });
      $.get("/fetch-unit", data, function(response) {
        response?.result?.forEach(function(item) {
          $(unit_measure).select2().append(new Option(item.name, item.id, response?.result.length == 1 ? true : false, response?.result.length == 1 ? true : false)).trigger('change');
        });
        $(loader).closest("tr").find('.unit_id_loader').addClass("d-none").removeClass("d-flex");
      });
    });

    $(document).on('change', '.unit_measure', function() {
      let item = $(this).closest("tr").find(".item");
      item = item?.val();
      $(this).prop('name', `unit_id[${item}]`)
      if (item != null && $(this).val() != null) {

        let data = {
          item,
          unit_measure: $(this).val(),
        };
        $.get("/fetch-uom-base", data, function(response) {
          console.log(response)
          if (response.success == false) {
            $('.hover-elevate-up').prop("disabled", true);
            alert(`The UOM conversion for ${response?.itemBaseUom?.base_uom?.name} to ${$(this).find("option:selected").text()} is not set.`)
          } else {
            $('.hover-elevate-up').prop("disabled", false);
          }
        });
      }
    })
  });
</script>
@endpush