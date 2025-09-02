@push('styles')
<style>
  th,
  td {
  width: 20%;
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

  <div  id="kt_post">
     <div class="container-fluid" id="kt_content_container">
      @if(Session::has('error'))
      <div class="alert alert-danger d-flex align-items-center p-5">
        <i class="ki-duotone ki-shield-tick fs-2hx text-danger me-4"></i>
        <div class="d-flex flex-column">
          <span>{{ Session::get('error') }}</span>
        </div>
      </div>
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

      <div class="bg-transparent border-0 card shadow-none pt-2">
        <form class="form" method="POST" action="{{ route($store) }}">
          @csrf
          <div class="row">
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Vendor</label>
              <select class="form-select" id="vendor_id" name="vendor_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($vendors as $vendor)
                <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('vendor_id'))
              <span class="text-danger">{{ $errors->first('vendor_id') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Place</label>
              <select class="form-select " name="place_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($places as $place)
                  <option value="{{ $place->id }}" {{ old('place_id') == $place->id ? 'selected' : '' }}>{{ $place->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('place_id'))
              <span class="text-danger">{{ $errors->first('place_id') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Currency</label>
              <select class="form-select" name="currency_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($currencies as $currency)
                  <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }} {{ $currency->short_form == "LKR" ? 'selected' : ''  }} >{{ $currency->short_form }}</option>
                @endforeach
              </select>
              @if ($errors->has('currency_id'))
              <span class="text-danger">{{ $errors->first('currency_id') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Discount</label>
              <input type="number" class="form-control " min="0" name="discount" id="discount" value="{{old('discount', 0)}}">
              @if ($errors->has('discount'))
              <span class="text-danger">{{ $errors->first('discount') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label for="amount" class="form-label required">Total Amount</label>
              <input type="hidden" name="amount" id="amount-hidden" value="{{ old("amount") }}">
              <input type="number" name="amount" id="amount" class="form-control" value="{{ old("amount") }}" disabled>
            </div>
          </div>

          <div id="items-section">
          
            <hr />
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Per Unit Price</th>
                    <th>Total</th>
                    <th title="Add More" class="text-center"> 
                      <svg id="add-item-btn" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class="cursor-pointer add-item-btn"><g><path d="M18 2c2.206 0 4 1.794 4 4v12c0 2.206-1.794 4-4 4H6c-2.206 0-4-1.794-4-4V6c0-2.206 1.794-4 4-4zm0-2H6a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h12a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6z" fill="#fff" opacity="1" data-original="#000000" class=""></path><path d="M12 18a1 1 0 0 1-1-1V7a1 1 0 0 1 2 0v10a1 1 0 0 1-1 1z" fill="#fff" opacity="1" data-original="#000000" class=""></path><path d="M6 12a1 1 0 0 1 1-1h10a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1z" fill="#fff" opacity="1" data-original="#000000" class=""></path></g></svg>
                      
                    </th>
                  </tr>
                </thead>
                <tbody id="items-table-body">
                  @foreach (old('items', []) as $index => $item)
                  <tr id="item-row-{{ $index }}">
                      <td>
                          <select class="form-select item-select" name="items[{{ $index }}][item_id]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                              <option value=""></option>
                              <!-- @foreach($items as $availableItem)
                              <option value="{{ $availableItem->id }}" {{ $item['item_id'] == $availableItem->id ? 'selected' : '' }}>{{ $availableItem->name }}</option>
                              @endforeach -->
                          </select>
                      </td>
                      <td>
                          <input type="number" step="0.001" class="form-control quantity-input w-150px w-lg-100" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? '' }}">
                      </td>
                      <td>
                          <select class="form-select unit-select" data-id="{{ old("items.$index.unit_id") }}"  name="items[{{ $index }}][unit_id]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                              <option value=""></option>
                          </select>
                      </td>
                      <td>
                          <input type="number" step="0.01" class="form-control unit-price-input w-150px w-lg-100" name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] ?? '' }}">
                      </td>
                     
                      <td>
                          <input type="number" class="form-control item-total w-150px w-lg-100" name="items[{{ $index }}][total]" value="{{ ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) }}" disabled>
                          <input type="hidden" class="form-control item-total-hidden" name="items[{{ $index }}][total]" value="{{ ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) }}">
                      </td>
                      <td>
                        <svg title="Remove" class="remove-item-btn cursor-pointer" data-row-id="item-row-{{ $index }}" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 384 384" style="enable-background:new 0 0 512 512" xml:space="preserve"><g><path d="M64 341.333C64 364.907 83.093 384 106.667 384h170.667C300.907 384 320 364.907 320 341.333v-256H64v256zM266.667 21.333 245.333 0H138.667l-21.334 21.333H42.667V64h298.666V21.333z" fill="#f42c2c" opacity="1" data-original="#000000" class=""></path></g></svg>
                      </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <div class="col-md-12 mt-3">
            <input type="reset" value="Reset" class="btn btn-dark w-100px mr-2" style="margin-right: 5px">
            <input type="submit" value="Create" class="btn submit-button btn-primary hover-elevate-up w-100px">
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
   $(document).ready(function() {
        let itemIndex = {{ count(old('items', [])) }};
        let fetchedItems = [];
        let fetchedUnits = [];

        // Show items section if vendor is selected
        $('#vendor_id').on("change", function() {
            const vendorId = this.value;
            // $("#items-section").css("display", vendorId ? "block" : "none");
            // $('#items-table-body').empty();
            $('#amount').val('');
            $('#amount-hidden').val('');

            if (vendorId) {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('fetchVendorItems') }}",
                    data: { vendor_id: vendorId },
                    success: function(response) {
                        fetchedItems = response.items;
                        fetchedUnits = response.units;
                        addItemRow(response.items, response.units);
                    },
                    error: function() {
                        // $("#items-section").css("display", "none");
                        // $('#items-table-body').empty();
                        $('#amount').val('');
                        $('#amount-hidden').val('');
                    }
                });
            }
        });

        // Function to add a new item row
        function addItemRow(items, units,itemValues) {
            itemIndex++;
            let itemOptions = '';
            items.forEach(item => {
              if (itemValues && !itemValues.includes(String(item.id))) {
                itemOptions += `<option value="${item.id}">${item.name}</option>`;
              } else if (!itemValues) {
                itemOptions += `<option value="${item.id}">${item.name}</option>`;
              }
            });

            let unitOptions = '';
            units.forEach(unit => {
                unitOptions += `<option value="${unit.id}">${unit.name}</option>`;
            });

            $('#items-table-body').append(`
                <tr id="item-row-${itemIndex}">
                    <td>
                        <select class="form-select item-select" name="items[${itemIndex}][item_id]">
                            <option value=""></option>
                            ${itemOptions}
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control quantity-input w-150px w-lg-100" name="items[${itemIndex}][quantity]">
                    </td>
                    <td>
                      <div class="position-relative">
                        <div class="unit_id_loader align-items-center bg-gray-100 d-none h-100 justify-content-center position-absolute start-50 top-50 translate-middle w-100 z-index-2">
                          <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                          </div>
                        </div>
                        <select class="form-select unit-select" data-id="{{ old('items[${itemIndex}][unit_id]') }}" name="items[${itemIndex}][unit_id]">
                          <option value=""></option>
                        </select>
                      </div>
                    </td>
                    <td>
                        <input type="number" class="form-control unit-price-input w-150px w-lg-100" name="items[${itemIndex}][unit_price]">
                    </td>
                    <td>
                        <input type="number" class="form-control item-total w-150px w-lg-100" name="items[${itemIndex}][total]" disabled>
                        <input type="hidden" class="form-control item-total-hidden" name="items[${itemIndex}][total]">
                    </td>
                    <td class="text-center" style="vertical-align: middle;" title="Remove">
                      <svg title="Remove" class="remove-item-btn" data-row-id="item-row-${itemIndex}" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 384 384" style="enable-background:new 0 0 512 512" xml:space="preserve"><g><path d="M64 341.333C64 364.907 83.093 384 106.667 384h170.667C300.907 384 320 364.907 320 341.333v-256H64v256zM266.667 21.333 245.333 0H138.667l-21.334 21.333H42.667V64h298.666V21.333z" fill="#f42c2c" opacity="1" data-original="#000000" class=""></path></g></svg>
                    </td>
                </tr>
            `);

            $(`.item-select, .unit-select`).select2({
                closeOnSelect: false,
                placeholder: "Select an option",
                allowClear: true
            });
        }
        $(document).on('change','.item-select', function() {
          let loader = $(this);
          let unit_measure = $(loader).closest("tr").find(".unit-select");
          let current_unit_measure = unit_measure.data('id');
          $(this).closest(".row").find('.unit_id_loader').addClass("d-flex").removeClass("d-none");

          unit_measure.empty().append('<option value="" disabled selected>Select an option</option>'); // Keep the empty option

          let data = {
            id: $(this).val(),
          };
          $.get("/fetch-unit", data, function(response) {
            response?.result?.forEach(function(item) {
              $(unit_measure).select2().append(new Option(item.short_form, item.id, (current_unit_measure ==  item.id) || (response?.result.length == 1 ? true : false), (current_unit_measure ==  item.id) || (response?.result.length == 1 ? true : false))).trigger('change');
            });
            $(loader).closest(".row").find('.unit_id_loader').addClass("d-none").removeClass("d-flex");
          });
        });

        // Event delegation for dynamic rows
        $('#items-table-body').on('click', '.remove-item-btn', function() {
            const rowId = $(this).data('row-id');
            $(`#${rowId}`).remove();
            updateTotalAmount();
            if ($('#items-table-body tr').length === 0) {
                // $("#items-section").css("display", "none");
                $('#vendor_id').val('').trigger('change'); // Optionally reset vendor selection
            }
        });

        $(document).on('change', '.unit-select', function() {
          let loader = $(this);
          let item = $(loader).closest("tr").find(".item-select").val();
          let itemName = $(loader).closest("tr").find(".item-select option:selected").text();

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

        $(document).on('click','.add-item-btn', function() {
          let totalItems = "{{$items->count()}}";
          let fetchedItems = @json($items);
          let itemValues = $('select.item-select').map(function() {
            if ($(this).val() !== '') {
              return $(this).val();
            }
          }).get();

          let filledCount = itemValues.length;

          // Get the total count of items
          let totalCount = $('select.item-select').length;

          if (totalCount > filledCount) {
            alert("Item is not selected")
            return;
          }
          if (totalItems == filledCount) {
            alert("No more item to select")
            return;
          }
            addItemRow(fetchedItems, fetchedUnits,itemValues);
        });

        $('.add-item-btn').trigger("click")
        // Function to update total amount
        function updateTotalAmount() {
            let totalAmount = 0;
            $('#items-table-body .item-total').each(function() {
                totalAmount += parseFloat($(this).val()) || 0;
            });
            
            // minus value
            let discount = $('#discount').val();
            $('#amount').val((totalAmount - discount).toFixed(2));
            $('#amount-hidden').val((totalAmount - discount).toFixed(2));
        }

        // Event delegation for recalculating total on input changes
        $('#items-table-body').on('input', '.quantity-input, .unit-price-input', function() {
            const row = $(this).closest('tr');
            const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
            const unitPrice = parseFloat(row.find('.unit-price-input').val()) || 0;
            const total = quantity * unitPrice;
            row.find('.item-total').val(total.toFixed(2));
            row.find('.item-total-hidden').val(total.toFixed(2));
            updateTotalAmount();
        });
        $(document).on('input', '#discount', function() {
          updateTotalAmount();
        });
       
        let anyError = <?php echo json_encode($errors->any()); ?>;
        console.log(<?php echo json_encode($errors->all()); ?>)
        if (anyError) {
          $('.item-select').trigger("change")
        }
    });
</script>
@endpush
