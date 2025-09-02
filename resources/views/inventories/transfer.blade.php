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
              {{ $result['itemCategoryName'] ?? "-" }}
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
              <th style="width: 25%;padding-left:.75rem !important">Store</th>
              <th style="width: 25%;padding-left:.75rem !important">Received Qty</th>
              <th style="width: 25%;padding-left:.75rem !important">Issued Qty</th>
              <th style="width: 25%;padding-left:.75rem !important">Available Qty</th>
            </tr>
            @foreach ($result['stores'] as $index => $result)
            <tr class="store-{{$result['store_id']}}">
              <td style="width: 25%;">{{ $result['place_name'] .' - '. $result['store_name']   ?? '-' }}</td>
              <td style="width: 25%;">{{ $result['received'] ?? '-' }}</td>
              <td style="width: 25%;">{{ $result['issued'] ?? '-' }}</td>
              <td style="width: 25%;" class="available-value" data-minus="{{$result['minus_value']}}">{{ $result['available'] ?? '-' }}</td>
            </tr>
            @endforeach
          </table>
        </div>
        <form class="form" method="POST" action="{{ route('inventories.transfer.store', $item['id']) }}">
          @csrf
          @method('PUT')
          <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Transfer to Store</h2>
          <div class="table-responsive">
            <table class="table table-rounded table-bordered border gy-3 gs-3">
              <tr>
                <th style="width:25%;padding-left:.75rem !important">From Store</th>
                <th style="width:25%">Available Qty</th>
                <th style="width:25%">To Store</th>
                <th style="width:25%">Transfer Qty</th>
              </tr>
              <tr>
                <td style="width:25%">
                  <select required class="form-select" id="store_id" name="store_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option value=""></option>
                    @foreach($stores as $store)
                    <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>{{ $store->place->name }} - {{$store->floor_name}}</option>
                    @endforeach
                  </select>
                </td>
                <td style="width:25%">
                  <input step="0.001" required name="available_quantity" type="text" class="form-control available-quantity disable" value="{{old('available_quantity')}}" >
                </td>
                <td style="width:25%">
                  <select required class="form-select" id="other_store_id" data-id="{{old('other_store_id')}}" name="other_store_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </td>
                <td style="width:25%">
                  <input name="base_uom" type="hidden" class="form-control" value="{{ $item->itemBase->baseUom->id }}">
                  <input step="0.001" required name="quantity" type="number" class="form-control" value="{{old('quantity')}}">
                </td>
               
              </tr>
            </table>
            <!-- transfer -->
          </div>
          <div class="row">
            <div class="col-md-12 mt-3">
              <input type="reset" value="Reset" class="btn btn-dark w-100px mr-2 confirm-reset" style="margin-right: 5px">
              
              <button type="submit" class="btn btn-primary hover-elevate-up w-100px submit-button transfer-btn">
                <span class="spinner-border spinner-border-custom d-none" role="status" aria-hidden="true"></span>
                <span class="button-text"> Transfer </span>
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
      console.log(`.store-${currentId}`)
      let value = $(`.store-${currentId} .available-value`).text();
      let status = $(`.store-${currentId} .available-value`).data('minus');

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

      let itemOptions = '';
      $('#other_store_id').empty().append('<option value="" disabled selected>Select</option>');
      stores.forEach(item => {
        if (currentId && !currentId.includes(String(item.id))) {
          $('#other_store_id').select2().append(new Option(`${item?.place?.name} - ${item?.floor_name}`, item.id)).trigger('change');

        } else if (!currentId) {
          $('#other_store_id').select2().append(new Option(`${item?.place?.name} - ${item?.floor_name}`, item.id)).trigger('change');
        }
      });
      


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