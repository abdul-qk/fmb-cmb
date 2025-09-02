@extends('layout.master')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
@if(Session::has('success'))
      <!--begin::Alert-->
      <div class="alert alert-success d-flex align-items-center p-5">
        <!--begin::Icon-->
        <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4"><span class="path1"></span><span class="path2"></span></i>
        <!--end::Icon-->
        <!--begin::Wrapper-->
        <div class="d-flex flex-column">
          <!--begin::Content-->
          <span>{{ Session::get('success') }}</span>
          <!--end::Content-->
        </div>
        <!--end::Wrapper-->
      </div>
      <!--end::Alert-->
      @endif
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
  <div id="kt_post">
    <div class="container-fluid" id="kt_content_container">
      <div class="bg-transparent border-0 card shadow-none pt-2">
        <div class="row">
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Item</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{ $item->name }}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Category</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{ $result['itemCategoryName'] }}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">UOM</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{ $item->itemBase->baseUom->name }}
            </h5>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-rounded table-bordered border gy-7 gs-7">
            <tr>
              <th style="padding-left:.75rem !important;text-align:center">#</th>
              <th style="width: 33%;padding-left:.75rem !important">Store</th>
              <th style="width: 33%;padding-left:.75rem !important">Available Qty</th>
              <th style="width: 33%;padding-left:.75rem !important">Unit Price</th>
              <!-- <th style="width: 25%;padding-left:.75rem !important">Selling Price</th> -->
            </tr>
            @foreach ($result['stores'] as $index => $result)
            <tr class="store-{{$result['inventory_id']}}">
              <td style="">
                <!-- pr {{ $result['purchase_order_id'] ?? '-' }} - 
                in  -->
                {{ $result['inventory_id'] ?? '-' }}
              </td>
              <td style="width: 33%;">{{ $result['store_name'] ?? '-' }}</td>
              <td style="width: 33%;" class="available-value" data-minus="{{$result['minus_value']}}">{{ $result['available'] ?? '-' }}</td>
              <td style="width: 33%;" class="unit-value" data-purchase-id="{{ $result['purchase_order_id'] }}">
               <span> {{ $result['price'] ?? '0' }}</span>
               </td>
              <!-- <td style="width: 25%;" class="selling-price">
               <span> {{ $result['selling_price'] ?? '0' }}</span>
               </td> -->
            </tr>
            @endforeach
          </table>
        </div>
        <form class="form" method="POST" action="{{ route('inventories.edit-inventory.update', $item['id']) }}">
          @csrf
          @method('PUT')
          <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Update Item</h2>
          <div class="table-responsive">
            <table class="table table-rounded table-bordered border gy-3 gs-3">
              <tr>
                <th style="width:14.2%;padding-left:.75rem !important">Store</th>
                <th style="width:14.2%">Available Qty</th>
                <th style="width:14.2%">Unit Price</th>
                <!-- <th style="width:14.2%">Selling Price</th> -->
                <th style="width:14.2%">Update Qty</th>
                <th style="width:14.2%">Update Unit Price</th>
                <!-- <th style="width:14.2%">Update Selling Price</th> -->
              </tr>
              <tr>
                <td style="width:14.2%">
                  <select required class="form-select" id="store_id" name="store_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option value=""></option>
                    @foreach ($stores as $index => $store)
                      <option value="{{ $store['store_id'] }}" {{ old('store_id') == $store['store_id'] ? 'selected' : '' }}>{{ $store['inventory_id'] }} - {{ $store['store_name'] }}</option>
                    @endforeach
                  </select>
                </td>
                <td style="width:14.2%">
                  <input step="0.001" required name="available_quantity" type="number" class="form-control available-quantity disable" value="{{old('available_quantity')}}" >
                </td>
                <td style="width:14.2%">
                  <input step="0.001" required name="unit_price" type="number" class="form-control unit-price disable" value="{{old('unit_price')}}" >
                </td>
                <!-- <td style="width:14.2%">
                  <input step="0.001" required name="selling_price" type="number" class="form-control selling-price disable" value="{{old('selling_price')}}" >
                </td> -->
                <td style="width:14.2%">
                  <input name="base_uom" type="hidden" class="form-control" value="{{ $item->itemBase->baseUom->id }}">
                  <input step="0.001" required name="update_quantity" min='0' type="number" class="form-control available-quantity" value="{{old('quantity')}}">
                </td>
                <td style="width:14.2%">
                  <input step="0.01" required name="update_unit_price" min='0' type="number" class="form-control unit-price">
                  <input step="0.01" hidden required name="purchase_order_Id"  type="number" class="form-control purchase_order_Id">
                  <input step="0.01" hidden required name="inventory_id"  type="number" class="form-control inventory_id">
                  <input hidden required name="items" type="number" class="form-control" value="{{ count($stores) }}">
                  <input hidden required name="same" type="number" class="form-control" value="{{ $uniquePOCount }}">
                </td>
                <!-- <td style="width:14.2%">
                  <input step="0.01" required name="update_selling_price" min='0' type="number" class="form-control selling-price">
                </td> -->
               
              </tr>
            </table>
            <!-- transfer -->
          </div>
          <div class="row">
            <div class="col-md-12 mt-3">
              <input type="reset" value="Reset" class="btn btn-dark w-100px mr-2 confirm-reset" style="margin-right: 5px">
              
              <button type="submit" class="btn btn-primary hover-elevate-up w-100px submit-button transfer-btn">
                <span class="spinner-border spinner-border-custom d-none" role="status" aria-hidden="true"></span>
                <span class="button-text"> Update </span>
              </button>
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

    let stores = @json($stores);

    $('.submit-button').on('click', function(e) {
      if (!confirm('Are you sure you want to proceed?')) {
        e.preventDefault(); // Stop form submission
      }
    });

    // Handle Reset confirmation
    $('.confirm-reset').on('click', function(e) {
      if (confirm('Are you sure you want to reset the above selection?')) {
        location.reload();
      } else {
        e.preventDefault(); 
      }
    });
    $(document).on('change', '#store_id', function() {
      let currentId = $(this).val();
      let str = $(this).find('option:selected').text().trim()
      let firstPart = str.split('-')[0].trim();

      
      let value = $(`.store-${firstPart} .available-value`).text()?.trim();
      let unitValue = $(`.store-${firstPart} .unit-value`).text()?.trim();
      // let sellingValue = $(`.store-${firstPart} .selling-price`).text()?.trim();
      let status = $(`.store-${firstPart} .available-value`).data('minus');
      let purchaseId = $(`.store-${firstPart} .unit-value`).data('purchase-id');

      $('.unit-price').val(0);
      $('.unit-price').val(unitValue || 0);
      
      // $('.selling-price').val(0);
      // $('.selling-price').val(sellingValue || 0);
      
      $('.purchase_order_Id').val('');
      $('.purchase_order_Id').val(purchaseId || '');

      $('.inventory_id').val('');
      $('.inventory_id').val(firstPart || '');

      $('.available-quantity').val(0);
      $('.available-quantity').val(value || 0);
      

      if (status) {
        $('.transfer-btn').prop("disabled",true)
        alert('Transfer to this store is not allowed because the value is negative.');
        return
      } else {
        $('.transfer-btn').prop("disabled", false)
      }

      if (value == '') {
        $('.transfer-btn').prop("disabled",true)
        alert('Transfer not allowed due to zero available quantity.');
        return
      } else {
        $('.transfer-btn').prop("disabled", false)
      }

      // let itemOptions = '';
      // $('#other_store_id').empty().append('<option value="" disabled selected>Select</option>');
      // stores.forEach(item => {
      //   if (currentId && !currentId.includes(String(item.id))) {
      //     $('#other_store_id').select2().append(new Option(`${item?.place?.name} - ${item?.floor_name}`, item.id)).trigger('change');

      //   } else if (!currentId) {
      //     $('#other_store_id').select2().append(new Option(`${item?.place?.name} - ${item?.floor_name}`, item.id)).trigger('change');
      //   }
      // });
    })

    $('form').on('submit', function () {
      const button = $('.submit-button');
      $('.button-text').hide()
      button.prop('disabled', true);
      button.find('.spinner-border').removeClass('d-none'); // show spinner
    });
  });
</script>
@endpush