@push('styles')
<style>
  th,
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
              <label class="form-label required">Kitchen</label>
              <select class="form-select" id="kitchen_id" name="kitchen_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                  <option value=""></option>
                  @foreach($kitchens as $kitchen)
                  <option value="{{ $kitchen->id }}" {{ old('kitchen_id') == $kitchen->id ? 'selected' : '' }}>{{$kitchen->floor_name}}</option>
                  @endforeach
              </select>
              @if ($errors->has('kitchen_id'))
              <span class="text-danger">{{ $errors->first('kitchen_id') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Event</label>
              <select class="form-select" id="event_id" name="event_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true" {{ old('kitchen_id') ? '' : 'disabled' }}>
                  <option value=""></option>
                  @foreach($events as $event)
                  <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>{{$event->name}}</option>
                  @endforeach
              </select>
              @if ($errors->has('event_id'))
              <span class="text-danger">{{ $errors->first('event_id') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Return By</label>
              <select class="form-select" id="return_by" name="return_by" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ old('return_by') == $user->id ? 'selected' : '' }}>{{$user->name}}</option>
                @endforeach
                <option value="0">Other</option>
              </select>
              @if ($errors->has('return_by'))
              <span class="text-danger">{{ $errors->first('return_by') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5 other-option" style="display: none;">
              <label class="form-label required">Name</label>
              <input disabled  type="text" class="form-control" name="worker_name"  value="{{ old('worker_name') }}">
            </div>
          </div>

          <div id="items-section" style="display: {{ old('kitchen_id') ? 'block' : 'none' }};">
          
            <hr />
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>Issued UOM</th>
                    <th>Issued Qty</th>
                    <th>Return UOM</th>
                    <th>Return Qty</th>
                    <th>Reason</th>
                    <th title="Add More" class="text-center"> 
                      <svg id="add-item-btn" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class="cursor-pointer d-none add-item-btn"><g><path d="M18 2c2.206 0 4 1.794 4 4v12c0 2.206-1.794 4-4 4H6c-2.206 0-4-1.794-4-4V6c0-2.206 1.794-4 4-4zm0-2H6a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h12a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6z" fill="#fff" opacity="1" data-original="#000000" class=""></path><path d="M12 18a1 1 0 0 1-1-1V7a1 1 0 0 1 2 0v10a1 1 0 0 1-1 1z" fill="#fff" opacity="1" data-original="#000000" class=""></path><path d="M6 12a1 1 0 0 1 1-1h10a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1z" fill="#fff" opacity="1" data-original="#000000" class=""></path></g></svg>
                      
                    </th>
                  </tr>
                </thead>
                <tbody id="items-table-body">
                  @foreach (old('items', []) as $index => $item)
                  <tr id="item-row-{{ $index }}">
                      <td>
                          <select class="form-select item-select" name="items[{{ $index }}][item_id]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                              <option value=""></option>
                              @foreach($items as $availableItem)
                              <option value="{{ $availableItem->id }}" {{ $item['item_id'] == $availableItem->id ? 'selected' : '' }}>{{ $availableItem->name }}</option>
                              @endforeach
                          </select>
                      </td>
                      <td>
                          <input type="text" class="form-control base-uom w-150px w-lg-100" name="items[{{ $index }}][base_unit]" value="{{$item['base_unit']}}" readonly>
                      </td>
                      <td>
                          <input type="number" step="0.001" class="form-control issued-quantity w-150px w-lg-100" name="items[{{ $index }}][issued_quantity]" value="{{$item['issued_quantity']}}" readonly>
                          <input type="hidden" class="form-control inventory-detail-id w-150px w-lg-100" name="items[{{ $index }}][inventory_detail_id]" value="{{$item['inventory_detail_id']}}">
                      </td>
                      <td>
                          <select class="form-select unit-select" data-id="{{ old("items.$index.unit_id") }}"  name="items[{{ $index }}][unit_id]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                              <option value=""></option>
                          </select>
                      </td>
                      <td>
                          <input type="number" step="0.001" class="form-control quantity-input w-150px w-lg-100" name="items[{{ $index }}][quantity]" value="{{$item['quantity']}}">
                      </td>
                      <td>
                          <input type="text" class="form-control reason w-150px w-lg-100" name="items[{{ $index }}][reason]" value="{{$item['reason']}}">
                      </td>

                      <td>
                        <svg id="add-item-btn" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class="cursor-pointer add-item-btn"><g><path d="M18 2c2.206 0 4 1.794 4 4v12c0 2.206-1.794 4-4 4H6c-2.206 0-4-1.794-4-4V6c0-2.206 1.794-4 4-4zm0-2H6a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h12a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)" class=""></path><path d="M12 18a1 1 0 0 1-1-1V7a1 1 0 0 1 2 0v10a1 1 0 0 1-1 1z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)" class=""></path><path d="M6 12a1 1 0 0 1 1-1h10a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)" class=""></path></g></svg>
                        @if($index != 0)
                          <svg title="Remove" class="ms-3 remove-item-btn cursor-pointer" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 384 384" style="enable-background:new 0 0 512 512" xml:space="preserve"><g><path d="M64 341.333C64 364.907 83.093 384 106.667 384h170.667C300.907 384 320 364.907 320 341.333v-256H64v256zM266.667 21.333 245.333 0H138.667l-21.334 21.333H42.667V64h298.666V21.333z" fill="#f42c2c" opacity="1" data-original="#000000" class=""></path></g></svg>
                        @endif
                      </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
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

    $('#return_by').on("change", function() {
      var selectedText = $(this).find(":selected").text().trim().toLowerCase(); // Trim to remove extra spaces
      if (selectedText === "other") {
        $('.other-option input').prop('required', true).prop('disabled', false);
        $('.other-option').show();
      } else {
        $('.other-option').hide();
        $('.other-option input').prop('required', false).prop('disabled', true);
      }
    });

    $('#kitchen_id').on("change", function() {
      $('#event_id').prop("disabled", false);
    });

    let skipPopulateDetails = false;
    var totalItems = [];
    let itemIndex = {{ count(old('items', [])) }};
    let fetchedItems = [];
    let fetchedUnits = [];

    // Show items section if vendor is selected
    $('#event_id').on("change", function(event) {
        event.preventDefault();
        const selectedEvent = $(this).val();
        const kitchenId = $('#kitchen_id').val();
        $("#items-section").css("display", selectedEvent ? "block" : "none");
        $('#items-table-body').empty();
        if (selectedEvent) {
            $.ajax({
                type: 'GET',
                url: "{{ route('fetchReturnFromKitchenItems') }}",
                data: { event_id: selectedEvent, kitchen_id: kitchenId },
                success: function(response) {
                    fetchedItems = response.items;
                    fetchedUnits = response.units;
                    totalItems = fetchedItems.length;
                    addItemRow(response.items, response.units);
                },
                error: function() {
                    $("#items-section").css("display", "none");
                    $('#items-table-body').empty();
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
            unitOptions += `<option value="${unit.id}">${unit.short_form}</option>`;
        });

        let itemLength = $('#items-table-body .base-uom').length;
        $('#items-table-body').append(`
            <tr id="item-row-${itemIndex}">
                <td>
                    <select class="form-select item-select" name="items[${itemIndex}][item_id]">
                        <option value=""></option>
                        ${itemOptions}
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control base-uom w-150px w-lg-100" name="items[${itemIndex}][base_unit]" readonly>
                </td>
                <td>
                    <input type="text" step="0.001" class="form-control issued-quantity w-150px w-lg-100" name="items[${itemIndex}][issued_quantity]" readonly>
                    <input type="hidden" class="form-control inventory-detail-id w-150px w-lg-100" name="items[${itemIndex}][inventory_detail_id]">
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
                    <input type="number" step="0.001" class="form-control quantity-input w-150px w-lg-100" name="items[${itemIndex}][quantity]">
                </td>
                <td>
                    <input type="text" class="form-control reason w-150px w-lg-100" name="items[${itemIndex}][reason]">
                </td>
                <td class="text-center" style="vertical-align: middle;" title="Remove">
                  <svg id="add-item-btn" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class="cursor-pointer add-item-btn"><g><path d="M18 2c2.206 0 4 1.794 4 4v12c0 2.206-1.794 4-4 4H6c-2.206 0-4-1.794-4-4V6c0-2.206 1.794-4 4-4zm0-2H6a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h12a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)" class=""></path><path d="M12 18a1 1 0 0 1-1-1V7a1 1 0 0 1 2 0v10a1 1 0 0 1-1 1z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)" class=""></path><path d="M6 12a1 1 0 0 1 1-1h10a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)" class=""></path></g></svg>
      ${(itemLength + 1) != 1 ? `<svg title="Remove" class="ms-3 remove-item-btn" data-row-id="item-row-${itemIndex}" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 384 384" style="enable-background:new 0 0 512 512" xml:space="preserve"><g><path d="M64 341.333C64 364.907 83.093 384 106.667 384h170.667C300.907 384 320 364.907 320 341.333v-256H64v256zM266.667 21.333 245.333 0H138.667l-21.334 21.333H42.667V64h298.666V21.333z" fill="#f42c2c" opacity="1" data-original="#000000" class=""></path></g></svg>`
      : ''}
                  
                </td>
            </tr>
        `);

        $(`.item-select, .unit-select`).select2({
            closeOnSelect: false,
            placeholder: "Select",
            allowClear: true
        });
    }

    $(document).on('change','.item-select', function(event) {
      event.preventDefault();
      let loader = $(this);
      let itemId = $(this).val();
      populateUnitDropdown(loader, itemId);
    });

    // Event delegation for dynamic rows
    $('#items-table-body').on('click', '.remove-item-btn', function(event) {
        event.preventDefault();
        const rowId = $(this).data('row-id');
        $(`#${rowId}`).remove();
        if ($('#items-table-body tr').length === 0) {
            $("#items-section").css("display", "none");
            $('#vendor_id').val('').trigger('change'); // Optionally reset vendor selection
        }
    });

    $(document).on('change', '.unit-select', function(event) {
      event.preventDefault();
      let loader = $(this);
      let item = $(loader).closest("tr").find(".item-select").val();
      let itemName = $(loader).closest("tr").find(".item-select option:selected").text();

      if (item != null && $(this).val() != null) {

        let data = {
          item,
          unit_measure: $(this).val(),
        };
        $.get("/fetch-uom-base", data, function(response) {
          if(response.success == false) {
            $('.submit-button').prop("disabled",true);
            alert(`The UOM conversion for ${response?.itemBaseUom?.base_uom?.short_name} to ${$(loader).find("option:selected").text()} is not set.`)
          }else {
            $('.submit-button').prop("disabled",false);
          }
        });
      }
    })

    $(document).on('click', '.add-item-btn',function(event) {
      event.preventDefault();
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
    
    let anyError = <?php echo json_encode($errors->any()); ?>;
    if (anyError) {
      selectedEvent = $('#event_id :selected').val();
      kitchenId = $('#kitchen_id :selected').val();
      $.ajax({
        type: 'GET',
        url: "{{ route('fetchReturnFromKitchenItems') }}",
        data: { event_id: selectedEvent, kitchen_id: kitchenId },
        success: function(response) {
            fetchedItems = response.items;
            fetchedUnits = response.units;
            skipPopulateDetails = true;
            $('.item-select').each(function() {
              $(this).trigger("change");
            });
            skipPopulateDetails = false;
        },
        error: function() {
            $("#items-section").css("display", "none");
            $('#items-table-body').empty();
        }
      });
    }

    function populateUnitDropdown(loader, itemId) {
      let unit_measure = $(loader).closest("tr").find(".unit-select");
      let current_unit_measure = unit_measure.data('id');
      let baseUom = $(loader).closest("tr").find(".base-uom");
      $(this).closest(".row").find('.unit_id_loader').addClass("d-flex").removeClass("d-none");
      unit_measure.empty().append('<option value="" disabled selected>Select</option>'); // Keep the empty option
      let data = {
        id: itemId,
      };
      $.get("/fetch-unit", data, function(response) {
        response?.result?.forEach(function(item,i) {
          $(unit_measure).select2().append(new Option(item.short_form, item.id, (current_unit_measure ==  item.id) || (response?.result.length == 1 ? true : false) || (i ==  0 ? true : false), (current_unit_measure ==  item.id) || (response?.result.length == 1 ? true : false) || (i ==  0 ? true : false))).trigger('change');
        });
        $(loader).closest(".row").find('.unit_id_loader').addClass("d-none").removeClass("d-flex");
      });
      if (!skipPopulateDetails) {
        populateItemDetails(loader, itemId);
      }
    }

    function populateItemDetails(loader, itemId) {
      selectedEvent = $('#event_id :selected').val();
      kitchenId = $('#kitchen_id :selected').val();
      let baseUom = $(loader).closest("tr").find(".base-uom");
      let issuedQuantity = $(loader).closest("tr").find(".issued-quantity");
      let inventoryDetailId = $(loader).closest("tr").find(".inventory-detail-id");
      let data = {
        item_id: itemId,
        event_id: selectedEvent,
        kitchen_id: kitchenId,
      };
      $.get("/fetch-event-item-details-for-kitchen", data, function(response) {
        $(baseUom).val(response?.base_uom?.short_form);
        $(issuedQuantity).val(response?.issued_quantity);
        $(inventoryDetailId).val(response?.inventory_detail_id);
      });
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
