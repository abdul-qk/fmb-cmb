@push('styles')
<style>
  td {
    width: 16%;
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

  <div id="kt_post">
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
        <form class="form" method="POST" action="{{ route($store) }}" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Vendor</label>
              <select required class="form-select" id="vendor_id" name="vendor_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
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
              <label class="form-label required">Store</label>
              <select required class="form-select" name="store_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($stores as $store)
                  @if ($store->default == 'yes' && $hasAccessStoreFixed )
                  <option selected value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>{{ $store->place->name }} - {{$store->floor_name}}</option>
                  @else
                  <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>{{ $store->place->name }} - {{$store->floor_name}}</option>
                  @endif
                @endforeach
              </select>
              @if ($errors->has('store_id'))
              <span class="text-danger">{{ $errors->first('store_id') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Currency</label>
              <select required class="form-select" name="currency_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($currencies as $currency)
                @if ($currency->default == 'yes' && $hasAccessCurrencyFixed )
                <option selected value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>{{ $currency->short_form }}</option>
                @else
                <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>{{ $currency->short_form }}</option>
                @endif
                @endforeach
              </select>
              @if ($errors->has('currency_id'))
              <span class="text-danger">{{ $errors->first('currency_id') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">GRN Date</label>
              <input type="date" required class="form-control {{ !$hasAccessGrnDateEditable ? 'disable' : '' }}" name="grn_date" id="grn_date" value="{{ !$hasAccessGrnDateEditable ? now()->toDateString() : old('grn_date') }}">
              @if ($errors->has('grn_date'))
              <span class="text-danger">{{ $errors->first('grn_date') }}</span>
              @endif
            </div>
            <!-- <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Paid By</label>
              <select required class="form-select" name="paid_by" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($customers as $customers)
                <option value="{{ $customers->id }}" {{ old('paid_by') == $customers->id ? 'selected' : '' }}>{{ $customers->id }} - {{$customers->name}}</option>
                @endforeach
              </select>
              @if ($errors->has('paid_by'))
              <span class="text-danger">{{ $errors->first('paid_by') }}</span>
              @endif
            </div> -->
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Bill No </label>
              <input required type="text" class="form-control" name="bill_no" id="bill_no" value="{{ old('bill_no') }}" />
              @if ($errors->has('bill_no'))
              <span class="text-danger">{{ $errors->first('bill_no') }}</span>
              @endif
            </div>
            <div class="col-md-4 mb-5">
              <label class="form-label">Upload Bill</label>
              <input type="file" class="form-control" name="upload_bill[]" multiple>
            </div>
            <!-- <div id="items-section" style="display: {{ old('vendor_id') ? 'block' : 'none' }};"> -->
            <div id="items-section" style="">
              <hr />
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Item</th>
                      <th>Qty</th>
                      <th>Unit</th>
                      <th>Per Unit Price</th>
                      <th>Discount</th>
                      <th>Total</th>
                      <th title="Add More" class="text-center">
                        <!-- <svg id="add-item-btn" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class="cursor-pointer">
                          <g>
                            <path d="M18 2c2.206 0 4 1.794 4 4v12c0 2.206-1.794 4-4 4H6c-2.206 0-4-1.794-4-4V6c0-2.206 1.794-4 4-4zm0-2H6a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h12a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6z" fill="#fff" opacity="1" data-original="#000000" class=""></path>
                            <path d="M12 18a1 1 0 0 1-1-1V7a1 1 0 0 1 2 0v10a1 1 0 0 1-1 1z" fill="#fff" opacity="1" data-original="#000000" class=""></path>
                            <path d="M6 12a1 1 0 0 1 1-1h10a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1z" fill="#fff" opacity="1" data-original="#000000" class=""></path>
                          </g>
                        </svg> -->

                      </th>
                    </tr>
                  </thead>
                  <tbody id="items-table-body">
                    @php
                      $oldItems = old('items', []);
                    @endphp

                    @if (!empty($oldItems))
                    @foreach ($oldItems as $index => $item)
                    <tr id="item-row-{{ $index }}">
                      <td>
                        <select required class="form-select item-select" name="items[{{ $index }}][item_id]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                          <option value=""></option>
                        </select>
                      </td>
                      <td>
                        <input required type="number" step="0.001" class="form-control quantity-input w-150px w-lg-100" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? '' }}">
                      </td>
                      <td>
                        <select required class="form-select unit-select" data-id="{{ $item['unit_id'] ?? '' }}" name="items[{{ $index }}][unit_id]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                          <option value=""></option>
                        </select>
                      </td>
                      <td>
                        <input required type="number" step="0.01" class="form-control unit-price-input w-150px w-lg-100" name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] ?? '' }}">
                      </td>
                      <td>
                        <div class="d-flex w-100">
                          <input required type="number" class="form-control per-item-discount" name="items[{{ $index }}][per_item_discount]" value="{{ $item['per_item_discount'] ?? '' }}" value="0">
                          <select required name="items[{{ $index }}][discount_option]" class="form-select discount-option" style="width:45%">
                            <option value="v" {{ ($item['discount_option'] ?? '') == 'v' ? 'selected' : '' }}>v</option>
                            <option value="%" {{ ($item['discount_option'] ?? '') == '%' ? 'selected' : '' }}>%</option>
                          </select>
                        </div>
                      </td>
                      <td>
                        <input type="number" step="0.01" class="form-control item-total w-150px w-lg-100" disabled value="{{ ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) }}">
                        <input type="hidden" step="0.01" class="form-control item-total-hidden" name="items[{{ $index }}][total]" value="{{ ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) }}">
                        <input type="hidden" step="0.01" class="form-control without-discount" name="items[{{ $index }}][sub_total]" value="{{ ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) }}">
                      </td>
                      <td>
                        <svg class="add-item-btn" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class="cursor-pointer">
                          <g>
                            <path d="M18 2c2.206 0 4 1.794 4 4v12c0 2.206-1.794 4-4 4H6c-2.206 0-4-1.794-4-4V6c0-2.206 1.794-4 4-4zm0-2H6a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h12a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6z" fill="#fff" opacity="1" data-original="#000000" class=""></path>
                            <path d="M12 18a1 1 0 0 1-1-1V7a1 1 0 0 1 2 0v10a1 1 0 0 1-1 1z" fill="#fff" opacity="1" data-original="#000000" class=""></path>
                            <path d="M6 12a1 1 0 0 1 1-1h10a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1z" fill="#fff" opacity="1" data-original="#000000" class=""></path>
                          </g>
                        </svg>
                        @if($index != 0)
                        <svg title="Remove" class="remove-item-btn cursor-pointer" data-row-id="item-row-{{ $index }}" xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 384 384">
                          <g>
                            <path d="M64 341.333C64 364.907 83.093 384 106.667 384h170.667C300.907 384 320 364.907 320 341.333v-256H64v256zM266.667 21.333 245.333 0H138.667l-21.334 21.333H42.667V64h298.666V21.333z" fill="#f42c2c" />
                          </g>
                        </svg>
                        @endif
                      </td>
                    </tr>
                    @endforeach
                    @else
                    
                    <tr id="item-row-0">
                      <td>
                      <select required class="form-select item-select" name="items[0][item_id]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                          <option value=""></option>
                          @foreach($items as $availableItem)
                              <option value="{{ $availableItem->id }}"
                                  @if(old("items.1.item_id") == $availableItem->id)
                                      selected
                                  
                                  @endif>
                                  {{ $availableItem->name }}
                              </option>
                          @endforeach
                      </select>
                      </td>
                      <td>
                        <input required type="number" step="0.001" class="form-control quantity-input w-150px w-lg-100" name="items[0][quantity]" value="">
                      </td>
                      <td>
                        <select required class="form-select unit-select" data-id="" name="items[0][unit_id]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                          <option value=""></option>
                        </select>
                      </td>
                      <td>
                        <input required type="number" step="0.01" class="form-control unit-price-input w-150px w-lg-100" name="items[0][unit_price]" value="">
                      </td>
                      <td>
                        <div class="d-flex w-100">
                          <input required type="number" class="form-control per-item-discount" name="items[0][per_item_discount]" value="0">
                          <select required name="items[0][discount_option]" class="form-select discount-option" style="width:45%">
                            <option value="v">v</option>
                            <option value="%">%</option>
                          </select>
                        </div>
                      </td>
                      <td>
                        <input type="number" class="form-control item-total w-150px w-lg-100" disabled>
                        <input type="hidden" class="form-control item-total-hidden" name="items[0][total]" value="">
                        <input type="hidden" class="form-control without-discount" name="items[0][sub_total]" value="">
                      </td>
                      <td class="text-center" style="vertical-align: middle;">
                        <svg class="add-item-btn" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class="cursor-pointer">
                          <g>
                            <path d="M18 2c2.206 0 4 1.794 4 4v12c0 2.206-1.794 4-4 4H6c-2.206 0-4-1.794-4-4V6c0-2.206 1.794-4 4-4zm0-2H6a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h12a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6z" fill="var(--bs-primary)" opacity="1" data-original="#000000" class=""></path>
                            <path d="M12 18a1 1 0 0 1-1-1V7a1 1 0 0 1 2 0v10a1 1 0 0 1-1 1z" fill="var(--bs-primary)" opacity="1" data-original="#000000" class=""></path>
                            <path d="M6 12a1 1 0 0 1 1-1h10a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1z" fill="var(--bs-primary)" opacity="1" data-original="#000000" class=""></path>
                          </g>
                        </svg>
                      </td>
                    </tr>
                    @endif

                  </tbody>
                </table>
              </div>
              <div class="row mt-3">
                
                <div class="col-md-3 col-lg-3 mb-5">
                  <label for="sub-amount" class="form-label required">Amount</label>
                  <input type="hidden" name="sub_amount" id="sub-amount-hidden" value="{{ old("sub_amount") }}">
                  <input type="number" name="sub_amount" id="sub-amount" class="form-control" value="{{ old("sub_amount") }}" disabled>
                </div>
                <div class="col-md-3 col-lg-3 mb-5">
                  <label for="sub-amount" class="form-label">Additional Charges</label>
                  <input type="number" name="additional_charges" id="additional-charges" class="form-control" value="{{ old("additional_charges",0) }}">
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
                <div class="col-md-4 col-lg-3 mb-5">
                  <label class="form-label">Description</label>
                  <textarea class="form-control" name="description" id="description">{{old('description')}}</textarea>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-12 mt-3">
            <input type="reset" value="Reset" class="btn btn-dark w-100px mr-2 confirm-reset" style="margin-right: 5px">
          
            <button type="submit" class="btn btn-primary hover-elevate-up w-100px submit-button confirm-submit">
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
  $(document).ready(function() {
    $('.confirm-submit').on('click', function(e) {
      if (!confirm('Are you sure you want to proceed?')) {
        e.preventDefault(); // Stop form submission
      }
    });

    // Handle Reset confirmation
    $('.confirm-reset').on('click', function(e) {
      if (confirm('Are you sure you want to reset the above selection?')) {
        // Reload the page if user confirms
        location.reload();
      } else {
        e.preventDefault(); // Stay on the page if user cancels
      }
    });
    let itemIndex = {{count(old('items', []))}};
    let fetchedItems = [];
    let fetchedUnits = [];

    // Show items section if vendor is selected
    // $('#vendor_id').on("change", function() {
    //     const vendorId = this.value;
    //     // $("#items-section").css("display", vendorId ? "block" : "none");
    //     // $('#items-table-body').empty();
    //     $('#amount').val('');
    //     $('#amount-hidden').val('');

    //     if (vendorId) {
    //         $.ajax({
    //             type: 'GET',
    //             url: "{{ route('fetchVendorItems') }}",
    //             data: { vendor_id: vendorId },
    //             success: function(response) {
    //                 fetchedItems = response.items;
    //                 fetchedUnits = response.units;
    //                 addItemRow(response.items, response.units);
    //             },
    //             error: function() {
    //                 $("#items-section").css("display", "none");
    //                 $('#items-table-body').empty();
    //                 $('#amount').val('');
    //                 $('#amount-hidden').val('');
    //             }
    //         });
    //     }
    // });

    // Function to add a new item row
    function addItemRow(items, units, itemValues) {
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
                        <select required class="form-select item-select" name="items[${itemIndex}][item_id]">
                            <option value=""></option>
                            ${itemOptions}
                        </select>
                    </td>
                    <td>
                        <input required type="number" step="0.001" class="form-control quantity-input w-150px w-lg-100" name="items[${itemIndex}][quantity]">
                    </td>
                    <td>
                      <div class="position-relative">
                        <div class="unit_id_loader align-items-center bg-gray-100 d-none h-100 justify-content-center position-absolute start-50 top-50 translate-middle w-100 z-index-2">
                          <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                          </div>
                        </div>
                        <select required class="form-select unit-select" data-id="{{ old('items[${itemIndex}][unit_id]') }}" name="items[${itemIndex}][unit_id]">
                          <option value=""></option>
                        </select>
                      </div>
                    </td>
                    <td>
                        <input required type="number" step="0.01" class="form-control unit-price-input w-150px w-lg-100" name="items[${itemIndex}][unit_price]">
                    </td>
                    <td>
                      <div class="d-flex w-100">
                        <input required type="number" class="form-control per-item-discount" name="items[${itemIndex}][per_item_discount]" value="0">
                        <select required name="items[${itemIndex}][discount_option]" class="form-select discount-option" style="width:45%">
                          <option>v</option>
                          <option>%</option>
                        </select>
                      </div>
                    </td>
                    <td>
                        <input required type="number" class="form-control item-total w-150px w-lg-100" name="items[${itemIndex}][total]" disabled>
                        <input type="hidden" class="form-control item-total-hidden" name="items[${itemIndex}][total]">
                        <input type="hidden" class="form-control without-discount" name="items[${itemIndex}][sub_total]">
                    </td>
                    <td class="text-center" style="vertical-align: middle;" >
                      <div>
                        <svg title="Add" class="add-item-btn me-3" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class="cursor-pointer">
                          <g>
                            <path d="M18 2c2.206 0 4 1.794 4 4v12c0 2.206-1.794 4-4 4H6c-2.206 0-4-1.794-4-4V6c0-2.206 1.794-4 4-4zm0-2H6a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h12a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6z" fill="var(--bs-primary)" opacity="1" data-original="#000000" class=""></path>
                            <path d="M12 18a1 1 0 0 1-1-1V7a1 1 0 0 1 2 0v10a1 1 0 0 1-1 1z" fill="var(--bs-primary)" opacity="1" data-original="#000000" class=""></path>
                            <path d="M6 12a1 1 0 0 1 1-1h10a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1z" fill="var(--bs-primary)" opacity="1" data-original="#000000" class=""></path>
                          </g>
                        </svg>
                      <svg title="Remove" class="remove-item-btn" data-row-id="item-row-${itemIndex}" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 384 384" style="enable-background:new 0 0 512 512" xml:space="preserve"><g><path d="M64 341.333C64 364.907 83.093 384 106.667 384h170.667C300.907 384 320 364.907 320 341.333v-256H64v256zM266.667 21.333 245.333 0H138.667l-21.334 21.333H42.667V64h298.666V21.333z" fill="#f42c2c" opacity="1" data-original="#000000" class=""></path></g></svg>
                      </div>
                    </td>
                </tr>
            `);

      $(`.item-select, .unit-select`).select2({
        closeOnSelect: false,
        placeholder: "Select",
        allowClear: true
      });
    }
    $(document).on('change', '.item-select', function() {
      
      let loader = $(this);
      let unit_measure = $(loader).closest("tr").find(".unit-select");
      let current_unit_measure = unit_measure.data('id');
      $(this).closest("tr").find('.unit_id_loader').addClass("d-flex").removeClass("d-none");

      unit_measure.empty().append('<option value="" disabled selected>Select</option>'); // Keep the empty option

      let data = {
        id: $(this).val(),
        vendor_id: $('#vendor_id').val(),
      };
      $.get("/fetch-unit", data, function(response) {
        response?.result?.forEach(function(item, index) {
          $(unit_measure).select2().append(new Option(item.short_form, item.id, (current_unit_measure == item.id) || (response?.result.length == 1 ? true : false) || (index == 0), (current_unit_measure == item.id) || (response?.result.length == 1 ? true : false) || (index == 0))).trigger('change');
        });
        $(loader).closest("tr").find('.unit-price-input').val(response?.purchaseOrderDetail?.unit_price)
        $(loader).closest("tr").find('.unit_id_loader').addClass("d-none").removeClass("d-flex");
      });
    });

    // Event delegation for dynamic rows
    $('#items-table-body').on('click', '.remove-item-btn', function() {
      const rowId = $(this).data('row-id');
      $(`#${rowId}`).remove();
      updateTotalAmount();
      if ($('#items-table-body tr').length === 0) {
        $("#items-section").css("display", "none");
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
          if (response.success == false) {
            $('.submit-button').prop("disabled", true);
            alert(`The UOM conversion for ${response?.itemBaseUom?.base_uom?.name} to ${$(loader).find("option:selected").text()} is not set.`)
          } else {
            $('.submit-button').prop("disabled", false);
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
      addItemRow(fetchedItems, fetchedUnits, itemValues);
    });

    // Function to update total amount
    function updateTotalAmount() {
      let subTotalAmount = 0;
      $('#items-table-body .without-discount').each(function() {
        subTotalAmount += parseFloat($(this).val()) || 0;
      });
      $('#sub-amount').val((subTotalAmount).toFixed(2));
      $('#sub-amount-hidden').val((subTotalAmount).toFixed(2));


      let totalAmount = 0;
      $('#items-table-body .item-total').each(function() {
        totalAmount += parseFloat($(this).val()) || 0;
      });

      // minus value
      let discount = $('#discount').val();
      let additionalCharges = $('#additional-charges').val();
      $('#amount').val(((totalAmount + Number(additionalCharges)) - discount).toFixed(2));
      $('#amount-hidden').val(((totalAmount + Number(additionalCharges)) - discount).toFixed(2));

    }

    // Event delegation for recalculating total on input changes
    $('#items-table-body').on('input', '.quantity-input, .unit-price-input', function() {
      const row = $(this).closest('tr');
      const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
      const unitPrice = parseFloat(row.find('.unit-price-input').val()) || 0;

      const total = quantity * unitPrice;
      if (quantity != '' && unitPrice != '') {
        row.find('.per-item-discount').removeClass('disable');
      } else {
        row.find('.per-item-discount').addClass('disable');
      }
      if (row.find('.discount-option').val() == 'v') {
        if (quantity != '' && unitPrice != '') {
          row.find('.per-item-discount').prop("max", total);
        }
        let discountValue = parseFloat(row.find('.per-item-discount').val()) || 0;

        row.find('.item-total').val((total - discountValue).toFixed(2));
        row.find('.item-total-hidden').val((total - discountValue).toFixed(2));
        row.find('.without-discount').val((total).toFixed(2));
        updateTotalAmount();
      }
      if (row.find('.discount-option').val() == '%') {
        let percentageValue = (total * discountValue) / 100
        row.find('.item-total').val((total - percentageValue).toFixed(2));
        row.find('.item-total-hidden').val((total - percentageValue).toFixed(2));
        row.find('.without-discount').val((total).toFixed(2));
      }

    });
    $(document).on('input', '#discount', function() {
      updateTotalAmount();
    });
    $(document).on('input', '#additional-charges', function() {
      updateTotalAmount();
    });
    $(document).on('change', '.discount-option', function() {
      let current = $(this).closest('.d-flex');
      const row = $(this).closest('tr');
      const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
      const unitPrice = parseFloat(row.find('.unit-price-input').val()) || 0;

      const total = quantity * unitPrice;
      let discountValue = row.find('.per-item-discount').val();

      if ($(this).val() == "%") {
        current.find('.per-item-discount').prop("max", "100");
        console.log(current.find('.per-item-discount').val())
        if (current.find('.per-item-discount').val() > 100) {
          alert("The percentage cannot exceed 100.");
          current.find('.per-item-discount').val("")
        }
        let percentageValue = (total * discountValue) / 100
        row.find('.item-total').val((total - percentageValue).toFixed(2));
        row.find('.item-total-hidden').val((total - percentageValue).toFixed(2));
        row.find('.without-discount').val((total).toFixed(2));
      }
      if ($(this).val() == "v") {
        if (quantity != '' && unitPrice != '') {
          row.find('.per-item-discount').removeClass('disable');
          row.find('.per-item-discount').prop("max", total);

          row.find('.item-total').val((total - discountValue).toFixed(2));
          row.find('.item-total-hidden').val((total - discountValue).toFixed(2));
          row.find('.without-discount').val((total).toFixed(2));
        }
      }
      updateTotalAmount();
    })

    $(document).on('input', '.per-item-discount', function() {

      let current = $(this).closest('.d-flex');
      const row = $(this).closest('tr');
      const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
      const unitPrice = parseFloat(row.find('.unit-price-input').val()) || 0;

      const total = quantity * unitPrice;
      let discountValue = $(this).val();

      if (current.find('.discount-option').val() == "%") {
        let percentageValue = (total * discountValue) / 100;
        row.find('.item-total').val((total - percentageValue).toFixed(2));
        row.find('.item-total-hidden').val((total - percentageValue).toFixed(2));
      }
      if (current.find('.discount-option').val() == "v") {
        if (quantity != '' && unitPrice != '') {

          row.find('.item-total').val((total - discountValue).toFixed(2));
          row.find('.item-total-hidden').val((total - discountValue).toFixed(2));
        }
      }
      updateTotalAmount();
    });


    let anyError = <?php echo json_encode($errors->any()); ?>;
    console.log(<?php echo json_encode($errors->all()); ?>)
    if (anyError) {
      $('.item-select').trigger("change")
    }
    $('form').on('submit', function () {
      const button = $('.submit-button');
      $('.button-text').hide()
      button.prop('disabled', true);
      button.find('.spinner-border').removeClass('d-none'); // show spinner
    });
  });
</script>
@endpush