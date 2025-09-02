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
  <div  id="kt_post">
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
      <!--begin::Card-->
      <div class="bg-transparent border-0 card shadow-none pt-2">
        <form class="form" method="POST" action="{{ route($store) }}">
          @csrf
          <div class="row">

            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Event</label>
              <select required class="form-select" id="event_id" name="event_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($events as $event)
                <option value="{{ $event->purchaseOrder[0]->id }}" {{ old('event_id') == $event->purchaseOrder[0]->id ? 'selected' : '' }}>{{$event->name}}</option>
                @endforeach
              </select>
              @if ($errors->has('event_id'))
              <span class="text-danger">{{ $errors->first('event_id') }}</span>
              @endif
            </div>
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
            <div id="items-section" style="display: {{ old('event_id') ? 'block' : 'none' }};">
              <hr />
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th class="text-center"> <input
                      class="form-check-input all border border-1 border-white my-2"
                      type="checkbox"
                      name="all"
                      value="all"
                      {{ old('all') == 'all' ? 'checked' : '' }}
                      id="all"></th>
                      <th style="vertical-align: middle;">Item</th>
                      <th style="vertical-align: middle;">Unit</th>
                      <th style="vertical-align: middle;">Approved Qty</th>
                      <th style="vertical-align: middle;">Received Qty</th>
                      <th style="vertical-align: middle;">Receiving Qty</th>
                    </tr>
                  </thead>
                  <tbody id="items-table-body"></tbody>
                </table>
              </div>
            </div>
            <div class="col-md-12 mt-3">
              <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2" style="margin-right: 5px">
              <input type="submit" value="Receive" class="btn btn-primary hover-elevate-up w-100px">
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
    let itemIndex = @json(count(old('items', [])));
    let fetchedItems = [];
    $('.all').change(function() {
      if ($(this).is(':checked')) {
        $(".enable-checkbox").prop("checked",true)
      } else {
        $(".enable-checkbox").prop("checked",false)
      }
      $('.enable-checkbox').trigger("change");
    });

    $('#event_id').on("change", function() {
      const selectedEvent = $(this).val();
      const store_id = $('#store_id').val();
      $("#items-section").css("display", selectedEvent ? "block" : "none");
      $('#items-table-body').empty();

      if (selectedEvent) {
        $.ajax({
          type: 'GET',
          url: "{{ route('fetchEventItems') }}", // Update this route to your events endpoint
          data: {
            event_id: selectedEvent,
          },
          success: function(response) {
            $("#items-section").css("display", response?.purchaseOrder?.detail.length > 0 ? "block" : "none");
            $('#items-table-body').empty();
            if (response?.allCompleted) {
              alert("No Received Items Available");
            }
            fetchedItems = response?.purchaseOrder?.detail;
            addItemRows(fetchedItems);
          },
          error: function() {
            $("#items-section").css("display", "none");
            $('#items-table-body').empty();
          }
        });
      }
    });

    function addItemRows(items) {
      items.forEach(itemData => {
        console.log({
          itemData
        })
        addItemRow(itemData);
      });
    }

    function addItemRow(itemData) {
      itemIndex++;
      console.log({
        itemData
      })
      // itemData?.item?.forEach((item, index) => {
      // Check if the item is eligible to be rendered
      const remaining = itemData.approved_detail?.inventory?.remaining ?? 1;
      if (remaining > 0) {
        $('#items-table-body').append(`
            <tr id="item-row-${itemData.id}">
            <td style="width: 50px;text-align:center;vertical-align:middle">
                <input class="form-check-input enable-checkbox" type="checkbox" value="1" id="flexCheckDefault" />
            </td>
            <td>
                <input type="text" class="form-control" value="${itemData.item.name}" disabled>
                <input type="hidden" class="form-control item-hidden" name="items[${itemData.id}][item_id]" value="${itemData.item.id}" disabled>
            </td>
            <td>
                <input type="text" class="form-control" value="${itemData.unit_measure.name}" disabled>
            </td>
            <td>
                <input step="0.001" type="number" class="form-control" value="${itemData.approved_detail.quantity}" disabled>
            </td>
            <td>
                <input hidden disabled class="form-control input-hidden" name="items[${itemData.id}][approved_purchase_order_detail_id]" value="${itemData.approved_detail.id}">
                <input type="number" step="0.001" class="form-control" value="${itemData.approved_detail.inventory?.remaining ? (itemData.approved_detail.quantity - itemData.approved_detail.inventory.remaining) : 0}" disabled>
            </td>
            <td>
                <input step="0.001" class="form-control input-hidden" disabled hidden name="items[${itemData.id}][current_quantity]" value="${itemData.approved_detail.inventory?.remaining ?? itemData.approved_detail.quantity}">
                <input step="0.001" type="number" step="0.001" min="0" max="${itemData.approved_detail.inventory?.remaining ?? itemData.approved_detail.quantity}" class="form-control quantity-input" name="items[${itemData.id}][quantity]" value="${itemData.approved_detail.inventory?.remaining ?? itemData.approved_detail.quantity}" disabled>
            </td>
        </tr>
        `);
      }
      // });
    }

    $('#items-table-body').on('change', '.enable-checkbox', function() {
      // Cache the row element for performance
      const $row = $(this).closest('tr'); // Find the closest row to the checkbox
      const isChecked = $(this).is(':checked');

      // Enable/Disable fields based on checkbox state
      $row.find('.quantity-input , .input-hidden , .item-hidden')
        .prop('disabled', !isChecked);
    });
  });
</script>
@endpush