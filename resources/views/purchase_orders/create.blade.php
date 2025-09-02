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

      @if($errors->has('events.*'))
        <!--begin::Alert-->
        <div class="alert alert-danger d-flex align-items-center p-5">
          <!--begin::Icon-->
          <i class="ki-duotone ki-shield-tick fs-2hx text-danger me-4"><span class="path1"></span><span class="path2"></span></i>
          <!--end::Icon-->
          <!--begin::Wrapper-->
          <div class="d-flex flex-column">
            <!--begin::Content-->
            @foreach ($errors->get('events.*') as $error)
              <span>{{ $error[0] }}</span>
            @endforeach
            <!--end::Content-->
          </div>
          <!--end::Wrapper-->
        </div>
        <!--end::Alert-->
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
              <label class="form-label required">Events</label>
              <select class="form-select" id="events" name="events[]" multiple data-control="select2" data-close-on-select="false" data-placeholder="Select Events" data-allow-clear="true" {{ old('vendor_id') ? '' : 'disabled' }}>
                <option value=""></option>
                @foreach($events as $event)
                <option value="{{ $event->id }}" {{ (is_array(old('events')) && in_array($event->id, old('events'))) ? 'selected' : '' }}>{{ $event->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('events'))
              <span class="text-danger">{{ $errors->first('events') }}</span>
              @endif
            </div>
            @if(hasPermissionForModule('place-independent', $currentModuleId))
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
            @else
              <div class="col-md-3 col-lg-3 mb-5">
                <label class="form-label required">Place</label>
                <input type="hidden" name="place_id" value="{{ $user->place->id }}">
                <input type="text" name="place_id" class="form-control" value="{{ $user->place->name }}" disabled>
              </div>
            @endif

            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Currency</label>
              <select class="form-select" name="currency_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($currencies as $currency)
                  <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }} {{ $currency->short_form == "LKR" ? 'selected' : ''  }}>{{ $currency->short_form }}</option>
                @endforeach
              </select>
              @if ($errors->has('currency_id'))
              <span class="text-danger">{{ $errors->first('currency_id') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Discount</label>
              <input type="number" class="form-control" min="0" name="discount" id="discount" value="{{old('discount', 0)}}">
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

          <div id="items-section" style="display: {{ old('events') ? 'block' : 'none' }};">
          
            <hr />
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th><input
                    class="form-check-input all border border-1 border-white"
                    type="checkbox"
                    name="all"
                    value="all"
                    {{ old('all') == 'all' ? 'checked' : '' }}
                    id="all"></th>
                    <th>Event</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Per Unit Price</th>
                    <th>Unit</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody id="items-table-body">
                  @foreach (old('items', []) as $index => $item)
                    <tr id="item-row-{{ $index }}">
                      <td style="vertical-align: middle;" class="text-center">
                        <input type="checkbox" class="enable-checkbox form-check-input border border-1 border-white" data-row-id="item-row-{{ $index }}" checked>
                      </td>
                      <td>
                          @foreach($events as $event)
                            @php
                              if($item['event_id'] == $event->id){
                                $eventId = $event->id;
                                $eventName = $event->name;
                              }
                            @endphp
                          @endforeach
                          <input type="text" class="form-control w-150px w-lg-100" name="items[{{ $index }}][event_id]" value="{{ $eventName }}" disabled>
                          <input type="hidden" class="form-control event_id" name="items[{{ $index }}][event_id]" value="{{ $eventId }}">
                      </td>
                      <td>
                          @foreach($items as $availableItem)
                            @php
                              if($item['item_id'] == $availableItem->id){
                                $itemId = $availableItem->id;
                                $itemName = $availableItem->name;
                              }
                            @endphp
                          @endforeach
                          <input type="text" class="form-control w-150px w-lg-100" name="items[{{ $index }}][item_id]" value="{{ $itemName }}" disabled>
                          <input type="hidden" class="form-control item_id" name="items[{{ $index }}][item_id]" value="{{ $itemId }}">
                      </td>
                      <td>
                        <input type="number" step="0.001" class="form-control quantity-input w-150px w-lg-100" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? '' }}">
                        <input type="hidden" step="0.001" class="form-control quantity-input-hidden w-150px w-lg-100" name="items[{{ $index }}][quantity_hidden]" value="{{ $item['quantity_hidden'] ?? '' }}">
                      </td>
                      <td>
                        <input type="number" step="0.01" class="form-control unit-price-input w-150px w-lg-100" name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] ?? '' }}">
                      </td>
                      <td>
                          @foreach($units as $unit)
                            @php
                              if($item['unit_id'] == $unit->id){
                                $unitId = $unit->id;
                                $unitName = $unit->name;
                              }
                            @endphp
                          @endforeach
                          <input type="text" class="form-control w-150px w-lg-100" name="items[{{ $index }}][unit_id]" value="{{ $unitName }}" disabled>
                          <input type="hidden" class="form-control unit_id" name="items[{{ $index }}][unit_id]" value="{{ $unitId }}">
                      </td>
                      <td>
                        <input step="0.01" type="number" class="form-control item-total w-150px w-lg-100" name="items[{{ $index }}][total]" value="{{ ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) }}" disabled>
                        <input step="0.01" type="hidden" class="form-control item-total-hidden" name="items[{{ $index }}][total]" value="{{ ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) }}">
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <div class="col-md-12 mt-3">
            <input type="reset" value="Reset" class="btn btn-dark w-100px mr-2" style="margin-right: 5px">
            <input type="submit" value="Create" class="btn btn-primary hover-elevate-up w-100px">
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

    $('#vendor_id').on("change", function() {
        $('#events').prop("disabled", false);
    });

    // Show items section if events is selected
    $('#events').on("change", function() {
        const selectedEvents = $(this).val();
        const vendorId = $('#vendor_id').val();
        $("#items-section").css("display", selectedEvents && selectedEvents.length > 0 ? "block" : "none");
        $('#items-table-body').empty();
        $('#amount').val('');
        $('#amount-hidden').val('');

        if (selectedEvents && selectedEvents.length > 0) {
            $.ajax({
                type: 'GET',
                url: "{{ route('fetchVendorEventItems') }}", // Update this route to your events endpoint
                data: { event_ids: selectedEvents, vendor_id: vendorId },
                success: function(response) {
                    fetchedItems = response; // Store items with their units directly
                    addItemRows(fetchedItems);
                },
                error: function() {
                    $("#items-section").css("display", "none");
                    $('#items-table-body').empty();
                    $('#amount').val('');
                    $('#amount-hidden').val('');
                }
            });
        }
    });

    // Function to add item rows with their respective units
    function addItemRows(items) {
        items.forEach(itemData => {
            addItemRow(itemData);
        });
    }

    // Function to add a single item row with its unit
    function addItemRow(itemData) {
        itemIndex++;

        // Set the item and unit directly from the itemData object
        const eventId = itemData.event_id;
        const eventName = itemData.event_name;
        const recipeId = itemData.recipe_id;
        const itemQuantity = itemData.item_quantity;
        const item = itemData.item;
        const unit = itemData.unit;

        $('#items-table-body').append(`
            <tr id="item-row-${itemIndex}">
                <td style="vertical-align: middle;" class="text-center">
                    <input type="checkbox" class="enable-checkbox form-check-input" data-row-id="item-row-${itemIndex}">
                </td>
                <td>
                    <input type="text" class="form-control w-150px w-lg-100" name="items[${itemIndex}][event_id]" value="${eventName}" disabled>
                    <input type="hidden" class="form-control event_id" name="items[${itemIndex}][event_id]" value="${eventId}" disabled>
                    <input type="hidden" class="form-control event_id" name="items[${itemIndex}][recipe_id]" value="${recipeId}" disabled>
                </td>
                <td>
                    <input type="text" class="form-control w-150px w-lg-100" name="items[${itemIndex}][item_id]" value="${item.name}" disabled>
                    <input type="hidden" class="form-control item_id" name="items[${itemIndex}][item_id]" value="${item.id}" disabled>
                   
                </td>
                <td>
                    <input type="number" step="0.001" class="form-control quantity-input w-150px w-lg-100" name="items[${itemIndex}][quantity]" value="${itemQuantity?.toFixed(1)}" disabled>
                    <input type="hidden" step="0.001" class="form-control quantity-input-hidden w-150px w-lg-100" name="items[${itemIndex}][quantity_hidden]" value="${itemQuantity?.toFixed(1)}" disabled>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control unit-price-input w-150px w-lg-100" name="items[${itemIndex}][unit_price]" disabled>
                </td>
                <td>
                    <input type="text" class="form-control w-150px w-lg-100" name="items[${itemIndex}][unit_id]" value="${unit.name}" disabled>
                    <input type="hidden" class="form-control unit_id" name="items[${itemIndex}][unit_id]" value="${unit.id}" disabled>
                </td>
                <td>
                    <input type="number" class="form-control item-total w-150px w-lg-100" name="items[${itemIndex}][total]" disabled>
                    <input type="hidden" class="form-control item-total-hidden" name="items[${itemIndex}][total]" disabled>
                </td>
            </tr>
        `);
    }
    $('.all').change(function() {
      if ($(this).is(':checked')) {
        $(".enable-checkbox").prop("checked",true)
      } else {
        $(".enable-checkbox").prop("checked",false)
      }
      $('.enable-checkbox').trigger("change");
    });
    // Event delegation for enabling/disabling inputs based on checkbox
    $('#items-table-body').on('change', '.enable-checkbox', function() {
        const rowId = $(this).data('row-id');
        const isChecked = $(this).is(':checked');
        $(`#${rowId} .quantity-input, #${rowId} .quantity-input-hidden, #${rowId} .unit-price-input,
        #${rowId} .event_id, #${rowId} .item_id, #${rowId} .unit_id, #${rowId} .item-total-hidden`).prop('disabled', !isChecked);
    });

    // Function to update total amount
    function updateTotalAmount() {
        let totalAmount = 0;
        $('#items-table-body .item-total').each(function() {
            totalAmount += parseFloat($(this).val()) || 0;
        });
        
        // Subtract discount
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
});


</script>
@endpush
