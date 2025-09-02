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
            @if(isset($result->vendor->id))
              <div class="col-md-3 col-lg-3 mb-5">
                <label class="form-label">Vendor</label>
                <input type="text" class="form-control" value="{{ $result->vendor->id .' - '. $result->vendor->name }}" disabled>
              </div>
            @endif
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Store</label>
              <select class="form-select" name="store_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($stores as $store)
                <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>{{$store->place->name}} - {{ $store->floor == 0 ? "Ground" : 'Floor '. $store->floor }}</option>
                @endforeach
              </select>
              @if ($errors->has('store_id'))
              <span class="text-danger">{{ $errors->first('store_id') }}</span>
              @endif
            </div>
          </div>
          <div id="items-section" style="display: block;">
            <hr />
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th> <input
                    class="form-check-input all border border-1 border-white my-2"
                    type="checkbox"
                    name="all"
                    value="all"
                    id="all" checked></th>
                    <th style="vertical-align: middle;">Item</th>
                    <th style="vertical-align: middle;">Unit</th>
                    <th style="vertical-align: middle;">Approved Qty</th>
                    <th style="vertical-align: middle;">Received Qty</th>
                    <th style="vertical-align: middle;">Receiving Qty</th>
                  </tr>
                </thead>
                <tbody id="items-table-body">
                  @foreach ($result->detail as $index => $item)
                  
                  @if((isset($item->approvedDetail->inventory) ? $item->approvedDetail->inventory->remaining : 1))
                    <tr id="item-row-{{ $index }}">
                      <td style="width: 50px;text-align:center;vertical-align:middle">
                        <input class="form-check-input" checked type="checkbox" value="1" id="flexCheckDefault" />
                      </td>
                      <td>
                        <input type="text" class="form-control" value="{{ $item->item->name }}" disabled>
                        <input type="hidden" class="form-control item-hidden" name="items[{{ $index }}][item_id]" value="{{ $item->item->id }}">
                      </td>
                      <td>
                        <input type="text" class="form-control" value="{{ $item->unitMeasure->short_form }}" disabled>
                      </td>
                      <td>
                        <input type="number" step="0.001" class="form-control" value="{{ $item->approvedDetail->quantity }}" disabled>
                      </td>
                      <td>
                        <input hidden class="form-control input-hidden" name="items[{{ $index }}][approved_purchase_order_detail_id]" value="{{ $item->approvedDetail->id }}">
                        <input step="0.01" type="number" class="form-control" value="{{ isset($item->approvedDetail->inventory->remaining) ? ($item->approvedDetail->quantity - $item->approvedDetail->inventory->remaining) : 0 }}" disabled>
                      </td>
                      <td>
                        <input step="0.001" class="form-control input-hidden" hidden name="items[{{ $index }}][current_quantity]" value="{{ $item->approvedDetail->inventory->remaining ?? $item->approvedDetail->quantity}}">
                        <input type="number" step="0.001" min="0" max="{{$item->approvedDetail->inventory->remaining ?? $item->approvedDetail->quantity}}" class="form-control quantity-input" name="items[{{ $index }}][quantity]" value="{{ old('items.' . $index . '.quantity', $item->approvedDetail->inventory->remaining ?? $item->approvedDetail->quantity) }}">
                      </td>
                    </tr>
                  @endif
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="col-md-12 mt-3">
              <input type="reset" value="Reset" class="btn btn-dark w-100px mr-2" style="margin-right: 5px">
              <input type="submit" value="Receive" class="btn btn-primary hover-elevate-up w-100px">
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
    
    $(document).on("change", '.form-check-input', function() {
      const isChecked = $(this).is(':checked');
      const row = $(this).closest('tr');

      if (!isChecked) {
        row.find('.quantity-input, .input-hidden, .item-hidden').prop('disabled', true);
      } else {
        row.find('.quantity-input, .input-hidden, .item-hidden').prop('disabled', false);
      }
    });
    
    $(document).on("change", '.all',function() {
      if ($(this).is(':checked')) {
        $(".form-check-input").prop("checked",true)
        $('.quantity-input, .input-hidden, .item-hidden').prop('disabled', false);
      } else {
        $(".form-check-input").prop("checked",false)
        $('.quantity-input, .input-hidden, .item-hidden').prop('disabled', true)
      }
      // $('.form-check-input').trigger("change");
    });
  });

  let anyError = <?php echo json_encode($errors->any()); ?>;
  console.log(<?php echo json_encode($errors->all()); ?>)
</script>
@endpush