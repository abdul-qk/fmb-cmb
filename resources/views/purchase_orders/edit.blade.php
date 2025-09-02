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
        <form class="form" method="POST" action="{{ route($update, $result->id) }}">
          @csrf
          @method('PUT')

          <div class="row">
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Vendor</label>
              <input type="text" class="form-control quantity-input" value="{{ $result->vendor->name }}" disabled>
              <input hidden type="text" class="form-control" name="current_vendor"  value="{{ $result->vendor->id }}">
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Event</label>
              <select class="form-select" id="events" name="events[]" data-control="select2" multiple="multiple" data-close-on-select="false" data-placeholder="Select Events" data-allow-clear="true" disabled>
                @foreach($events as $event)
                  <option value="{{ $event->id }}" {{ in_array($event->id, $selectedEvents) ? 'selected' : '' }}>
                      {{ $event->name }}
                  </option>
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
                    <option value="{{ $place->id }}" {{ old('place_id',$result->place_id) == $place->id ? 'selected' : '' }}>{{ $place->name }}</option>
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
                  <option value="{{ $currency->id }}" {{ old('currency_id',$result->currency_id) == $currency->id ? 'selected' : '' }}>{{ $currency->short_form }}</option>
                @endforeach
              </select>
              @if ($errors->has('currency_id'))
              <span class="text-danger">{{ $errors->first('currency_id') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Discount</label>
              <input type="number" class="form-control" min="0" name="discount" id="discount" value="{{old('discount',$result->discount)}}">
              @if ($errors->has('discount'))
              <span class="text-danger">{{ $errors->first('discount') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
                <label for="amount" class="form-label required">Total Amount</label>
                <input type="hidden" name="amount" id="amount-hidden" value="{{ old('amount', $result->amount) }}">
                <input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount', $result->amount) }}" disabled>
              </div>
            @if ($pdfUrl != '')
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">PO Document</label>
              <div class="flex-wrap mb-5 d-flex align-items-end" style="gap: 10px; height: 40px;">
                <span class="doc-file position-relative">
                  <a title="PO Document" class="d-inline-flex align-items-end" style="display: inline-block;" href="{{ url($pdfUrl) }}" target="_blank">
                    <i class="bi bi-file-earmark-text text-primary fs-2x"></i>
                  </a>
                </span>
              </div>
            </div>
            @endif
            <div class="col-md-3 col-lg-3 mb-5">
            </div>
          </div>
          <div id="items-section" style="display: {{ old('vendor_id', $result->vendor_id) ? 'block' : 'none' }};">
            <hr />
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Event</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Per Unit Price</th>
                    <th>Unit</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody id="items-table-body">
                  @php
                    $itemsData = old('items', $result->detail->toArray());
                  @endphp
                  @foreach ($itemsData as $index => $item)
                  <tr id="item-row-{{ $index }}">
                      <td>
                        @foreach($events as $event)
                          @php
                            if((old('items.' . $index . '.event_id') == $event->id) || (isset($item['event']['id']) && $item['event']['id'] == $event->id)){
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
                            if((old('items.' . $index . '.item_id') == $availableItem->id) || (isset($item['item']['id']) && $item['item']['id'] == $availableItem->id)){
                              $itemId = $availableItem->id;
                              $itemName = $availableItem->name;
                            }
                          @endphp
                        @endforeach
                        <input type="text" class="form-control w-150px w-lg-100" name="items[{{ $index }}][item_id]" value="{{ $itemName }}" disabled>
                        <input type="hidden" class="form-control item_id" name="items[{{ $index }}][item_id]" value="{{ $itemId }}">
                      </td>
                      <td>
                          <input type="number" step="0.001" class="form-control w-150px w-lg-100 quantity-input" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? '' }}">
                      </td>
                      <td>
                          <input type="number" step="0.01" class="form-control unit-price-input w-150px w-lg-100" name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] ?? '' }}">
                      </td>
                      <td>
                        @foreach($units as $unit)
                          @php
                            if((old('items.' . $index . '.unit_id') == $unit->id) || (isset($item['unit_measure']['id']) && $item['unit_measure']['id'] == $unit->id)){
                              $unitId = $unit->id;
                              $unitName = $unit->name;
                            }
                          @endphp
                        @endforeach
                        <input type="text" class="form-control w-150px w-lg-100" name="items[{{ $index }}][unit_id]" value="{{ $unitName }}" disabled>
                        <input type="hidden" class="form-control unit_id" name="items[{{ $index }}][unit_id]" value="{{ $unitId }}">
                      </td>
                      <td>
                          <input type="number" class="form-control w-150px w-lg-100 item-total" value="{{ ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) }}" disabled>
                          <input type="hidden" class="form-control item-total-hidden" name="items[{{ $index }}][total]" value="{{ ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) }}">
                      </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            
          </div>

          <div class="col-md-12 mt-3">
            <input type="reset" value="Reset" class="btn btn-dark w-100px mr-2" style="margin-right: 5px">
            <input type="submit" value="Update" class="btn btn-primary hover-elevate-up w-100px">
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
    // Function to update total amount
    function updateTotalAmount() {
      let totalAmount = 0;
      $('#items-table-body .item-total').each(function() {
          totalAmount += parseFloat($(this).val()) || 0;
      });


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
