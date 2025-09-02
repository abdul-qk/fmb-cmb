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
          <form class="form" method="POST" action="{{ route('purchase_orders.approve.store', $result->id) }}">
            @csrf
            @method('PUT')
            <div class="row">
            <div class="col-md-3 col-lg-3 mb-5">
                <label class="form-label">Vendor</label>
                <input type="text" class="form-control" value="{{ $result->vendor->name }}" disabled>
                <input hidden type="text" class="form-control" name="current_vendor"  value="{{ $result->vendor->id }}">
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Events</label>
            <input type="text" class="form-control" value="{{ implode(',', $result->events->pluck('name')->toArray()) }}" disabled>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
                <label class="form-label">Place</label>
                <input type="text" class="form-control disable" value="{{ $result->place->name }}">
                <input type="hidden" name="place_id" class="form-control disable" value="{{ $result->place->id }}">
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
                <label class="form-label">Currency</label>
                <input type="text" class="form-control disable" value="{{ $result->currency->short_form }}">
                <input type="hidden" name="currency_id" class="form-control disable" value="{{ $result->currency->id }}">
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
                <label class="form-label required">Discount</label>
                <input type="number" class="form-control disable" min="0" name="discount" id="discount" value="{{old('discount',$result->discount)}}">
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
                <label class="form-label">Total Amount</label>
                <input type="hidden" name="amount" id="amount-hidden" value="{{ old('amount', $result->amount) }}">
                <input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount', $result->amount) }}" disabled>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
            </div>
            </div>
                <div id="items-section" style="display: block;">
                    <hr />
                    <div class="table-responsive">
                      <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Event</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Approved Qty</th>
                            <th>Per Unit Price</th>
                            <th>Unit</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody id="items-table-body">
                        <input type="hidden" class="form-control w-150px w-lg-100" name="current_vendor" value="{{ $result->vendor->id }}">
                        @foreach ($result->detail as $index => $item)
                        <input type="hidden" class="form-control w-150px w-lg-100" name="items[{{ $index }}][purchase_order_detail_id]" value="{{ $item->id }}">
                        <tr id="item-row-{{ $index }}">
                            <td>
                                <input type="text" class="form-control w-150px w-lg-100" value="{{ $item->event->name }}" disabled>
                                <input type="hidden" class="form-control" name="items[{{ $index }}][event_id]" value="{{ $item->event->id }}">
                            </td>
                            <td>
                                <input type="text" class="form-control disable w-150px w-lg-100" value="{{ $item->item->name }}">
                                <input type="hidden" class="form-control" name="items[{{ $index }}][item_id]" value="{{ $item->item->id }}">
                            </td>
                            <td>
                                <input type="number" step="0.001" class="form-control disable w-150px w-lg-100" value="{{ $item->quantity }}">
                            </td>
                            <td>
                                <input type="number" step="0.001" class="form-control quantity-input w-150px w-lg-100" name="items[{{ $index }}][quantity]" value="{{ old('items.' . $index . '.quantity', $item->quantity) }}">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" class="form-control unit-price-input disable" value="{{ $item->unit_price }}">
                            </td>
                            <td>
                                <input type="text" class="form-control w-150px w-lg-100" value="{{ $item->unitMeasure->short_form }}" disabled>
                                <input hidden type="text" name="items[{{ $index }}][unit_id]" class="form-control disable" value="{{ $item->unitMeasure->id }}">
                            </td>
                            <td>
                                <input type="number" class="form-control item-total w-150px w-lg-100" name="items[{{ $index }}][total]" value="{{ old('items.' . $index . '.total', $item->total) }}" disabled>
                                <input type="hidden" class="form-control item-total-hidden" name="items[{{ $index }}][total]" value="{{ old('items.' . $index . '.total', $item->total) }}">
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                   
                    <div class="col-md-12 mt-3">
                        <input type="reset" value="Reset" class="btn btn-dark w-100px mr-2" style="margin-right: 5px">
                        <input type="submit" value="Update" class="btn btn-primary hover-elevate-up w-100px">
                    </div>
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
        $('#items-table-body').on('input', '.quantity-input', function() {
            const row = $(this).closest('tr');
            const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
            const unitPrice = parseFloat(row.find('.unit-price-input').val()) || 0;
            const total = quantity * unitPrice;
            row.find('.item-total').val(total.toFixed(2));
            row.find('.item-total-hidden').val(total.toFixed(2));
            updateTotalAmount();
        });
        function updateTotalAmount() {
            let totalAmount = 0;
            $('#items-table-body .item-total').each(function() {
                totalAmount += parseFloat($(this).val()) || 0;
            });
            $('#amount').val(totalAmount.toFixed(2));
            $('#amount-hidden').val(totalAmount.toFixed(2));
        }
    });
    let anyError = <?php echo json_encode($errors->any()); ?>;
  console.log(<?php echo json_encode($errors->all()); ?>)

</script>
@endpush