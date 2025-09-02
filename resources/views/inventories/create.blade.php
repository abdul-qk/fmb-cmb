@push('styles')
<style>
  .table-1 th:not(:first-child),
  .table-1 td:not(:first-child) {
    width: 16.6%;
  }
  .table-2 th:not(:last-child),
  .table-2 td:not(:last-child) {
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
  <div id="kt_post">
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
      <!--begin::Card-->
      <div class="bg-transparent border-0 card shadow-none pt-2">
        <form class="form" method="POST" action="{{ route($store) }}">
          @csrf
          <div class="row">
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Event</label>
              <select required class="form-select" name="event_id" id="event_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
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
              <label class="form-label required">Store</label>
              <select required class="form-select" name="store_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($stores as $store)
                <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>{{ $store->place->name }} - {{$store->floor_name}}</option>
                @endforeach
              </select>
              @if ($errors->has('store_id'))
              <span class="text-danger">{{ $errors->first('store_id') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Kitchen</label>
              <select required class="form-select" name="kitchen_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
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
              <label class="form-label required">Received By</label>
              <select required class="form-select" id="received_by" name="received_by" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ old('received_by') == $user->id ? 'selected' : '' }}>{{$user->name}}</option>
                @endforeach
                <option value="0">Other</option>
              </select>
              @if ($errors->has('received_by'))
              <span class="text-danger">{{ $errors->first('received_by') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Note</label>
              <textarea class="form-control" name="note" id="note">{{old('note')}}</textarea>
            </div>
            <div class="col-md-3 col-lg-3 mb-5 other-option" style="display: none;">
              <label class="form-label required">Name</label>
              <input disabled  type="text" class="form-control" name="worker_name"  value="{{ old('worker_name') }}">
            </div>
            <div id="items-section" style="display: none;">
              <hr />
              <div class="table-responsive">
                <table class="table table-bordered table-1">
                  <thead>
                    <tr>
                      <th class="text-center">
                        <input type="checkbox" class="form-check-input border border-1 border-white my-2" id="select-all" checked>
                      </th>
                      <th style="vertical-align: middle;">Item</th>
                      <th style="vertical-align: middle;">Requested UOM</th>
                      <th style="vertical-align: middle;">Requested Qty</th>
                      <th style="vertical-align: middle;">Issued Qty</th>
                      <th style="vertical-align: middle;">Issue UOM</th>
                      <th style="vertical-align: middle;">
                        <div class="d-flex justify-content-between">
                          Issue Qty
                        </div>
                      </th>
                    </tr>
                  </thead>
                  <tbody id="items-table-body">
                  </tbody>
                </table>
              </div>
              <div class="row more-ingredient">
                <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Add Items</h2>
                <div class="table-responsive">
                  <table class="table table-bordered table-2">
                    <thead>
                      <tr>
                        <th>Item</th>
                        <th>Base UOM</th>
                        <th>Available Qty</th>
                        <th>Issue UOM</th>
                        <th>Issue Qty</th>
                        <th title="Add More" style="min-width:45px;width:45px" class="text-center">
                          <svg class="add-items d-none" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class="cursor-pointer">
                            <g>
                              <path d="M18 2c2.206 0 4 1.794 4 4v12c0 2.206-1.794 4-4 4H6c-2.206 0-4-1.794-4-4V6c0-2.206 1.794-4 4-4zm0-2H6a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h12a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6z" fill="#fff" opacity="1" data-original="#000000" class=""></path>
                              <path d="M12 18a1 1 0 0 1-1-1V7a1 1 0 0 1 2 0v10a1 1 0 0 1-1 1z" fill="#fff" opacity="1" data-original="#000000" class=""></path>
                              <path d="M6 12a1 1 0 0 1 1-1h10a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1z" fill="#fff" opacity="1" data-original="#000000" class=""></path>
                            </g>
                          </svg>
                        </th>
                      </tr>
                    </thead>
                    <tbody id="issue-table-body">
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
                          <select class="form-select unit-select select2-unit" data-id="{{ old("items.$index.unit_id") }}" name="items[{{ $index }}][unit_id]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                            <option value=""></option>
                          </select>
                        </td>
                        <td>
                          <svg class="add-items " xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class="cursor-pointer">
                            <g>
                              <path d="M18 2c2.206 0 4 1.794 4 4v12c0 2.206-1.794 4-4 4H6c-2.206 0-4-1.794-4-4V6c0-2.206 1.794-4 4-4zm0-2H6a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h12a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6z" fill="#fff" opacity="1" data-original="#000000" class=""></path>
                              <path d="M12 18a1 1 0 0 1-1-1V7a1 1 0 0 1 2 0v10a1 1 0 0 1-1 1z" fill="#fff" opacity="1" data-original="#000000" class=""></path>
                              <path d="M6 12a1 1 0 0 1 1-1h10a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1z" fill="#fff" opacity="1" data-original="#000000" class=""></path>
                            </g>
                          </svg>
                          @if($index != 0)
                          <svg title="Remove" class="remove-item-btn cursor-pointer ms-3" data-row-id="item-row-{{ $index }}" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 384 384" style="enable-background:new 0 0 512 512" xml:space="preserve">
                            <g>
                              <path d="M64 341.333C64 364.907 83.093 384 106.667 384h170.667C300.907 384 320 364.907 320 341.333v-256H64v256zM266.667 21.333 245.333 0H138.667l-21.334 21.333H42.667V64h298.666V21.333z" fill="#f42c2c" opacity="1" data-original="#000000" class=""></path>
                            </g>
                          </svg>
                          @endif
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="col-md-12 mt-3">
              
              <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2 confirm-reset" style="margin-right: 5px">
              <!-- <input type="submit" value="Create" class="btn btn-primary hover-elevate-up w-100px confirm-submit"> -->
              
              <button type="submit" class="btn btn-primary hover-elevate-up w-100px submit-button confirm-submit">
                <span class="spinner-border spinner-border-custom d-none" role="status" aria-hidden="true"></span>
                <span class="button-text"> Create </span>
              </button>
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
  let itemIndex = {{count(old('items', [])) }};
  let fetchedItems = [];
  let fetchedUnits = [];
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
    $(document).on("click", '.remove-btn', function() {
      let IssueItem = $("select.item").val()
      $(this).closest(`tr`).remove();

      IssueItem.length == 0
    });
    $(document).on('click','.add-items', function() {
      let totalItems = "{{$itemsCount}}";
      let itemValues = $('.item').map(function() {
        if ($(this).val() !== '') {
          return $(this).val();
        }
      }).get();

      let filledCount = itemValues.length;

      let filledSelectCount = $('select.item').map(function() {
        if ($(this).val() !== '') {
          return $(this).val();
        }
      }).get();

      let innerItem =  $('#issue-table-body .remaining_quantity').length;
      
      filledSelectCount = filledSelectCount.length

      let totalCount = $('select.item').length;

      if (totalCount > filledSelectCount) {
        alert("Item is not selected")
        return;
      }
      if (totalItems == filledCount) {
        alert("No more item to select")
        return;
      }

      let ingredient = filledCount
      $("#total_ingredient").val(++ingredient);
      let data = {
        id: ingredient,
        items: itemValues,
        innerItem:(innerItem + 1)
      };
      $.get("/fetch-get-item", data, function(response) {
        var moreIngredient = response?.currentIngredient;
        // $('.more-ingredient').prepend(moreIngredient);
        $('.more-ingredient table tbody').append(moreIngredient);
        $('select').select2();
      })
    });
    let baseUomValue = '';
    $(document).on('change', '.item', function() {
      let loader = $(this);
      if (loader.val() == "" ) {
        loader.parent().find(".form-check-input").attr("checked",false)
      }else {
        loader.parent().find(".form-check-input").attr("checked",true)
      }
      
      let unit_measure = $(loader).closest("tr").find(".unit_measure");
      let item_quantity = $(loader).closest("tr").find(".item_quantity");
      let remaining_quantity = $(loader).closest("tr").find(".remaining_quantity");
      let select_item = $(loader).closest("tr").find(".selected_items");
      let other = $(loader).closest("tr").find(".other");
      let baseUom = $(this).closest("tr").find(".base-uom");
      let availableQuantity = $(this).closest("tr").find(".available-quantity");


      item_quantity.prop('name', `quantity[${$(this).val()}]`)
      remaining_quantity.prop('name', `remaining_quantity[${$(this).val()}]`)
      select_item.val($(this).val())
      other.prop('name', `other[${$(this).val()}]`)

      $(this).closest("tr").find('.unit_id_loader').addClass("d-flex").removeClass("d-none");

      unit_measure.empty().append('<option value="" disabled selected>Select</option>'); // Keep the empty option

      let data = {
        id: $(this).val(),
      };
     
      // $.get("/fetch-item-details", data, function(response) {
      //   baseUomValue = response?.item_base?.base_uom?.short_form ?? '-';
      //   $(baseUom).val(baseUomValue);
      //   $(availableQuantity).val(response?.detail?.available_quantity ?? 0);
      // });
      // $.get("/fetch-unit", data, function(response2) {
      //   $(unit_measure).empty();
      //   response2?.result?.forEach(function(item) {
      //     const isSelected = item.short_form === baseUomValue ?? "";
      //     console.log(item.short_form, baseUomValue,response2)
      //     $(unit_measure).append(new Option(item.short_form, item.id, isSelected, isSelected));
      //   });
      //   $(unit_measure).trigger('change');
      //   $(loader).closest("tr").find('.unit_id_loader').addClass("d-none").removeClass("d-flex");
      // });
      
      async function fetchData() {
        $(unit_measure).select2({
          allowClear: true
        });
        const response = await $.get("/fetch-item-details", data);
        baseUomValue = response?.item_base?.base_uom?.short_form ?? '-';
        console.log({ baseUomValue });

        $(baseUom).val(baseUomValue);
        $(availableQuantity).val(response?.detail?.available_quantity ?? 0);

        const response2 = await $.get("/fetch-unit", data);
        $(unit_measure).empty();
        response2?.result?.forEach((item, i) => {
          const isSelected = item.short_form === baseUomValue;
          console.log(`Is Selected: ${isSelected}`, item.short_form, baseUomValue);

          const option = new Option(item.short_form, item.id, false, isSelected);
          $(unit_measure).append(option);

        });
        $(unit_measure).trigger('change');
        $(loader).closest("tr").find('.unit_id_loader').addClass("d-none").removeClass("d-flex");
      }
      fetchData();
    });

    $(document).on('change', '.select2-unit', function() {
       let currentThis = $(this);
      let item = $(this).closest("tr").find(".item");
      item = item?.val();

      if (item != null && $(this).val() != null) {

        let data = {
          item,
          unit_measure: $(this).val(),
        };
        $.get("/fetch-uom-base", data, function(response) {
          if ($(currentThis).find("option:selected").text() == baseUomValue) {
            $('.hover-elevate-up').prop("disabled", false);
            return false;
          }
          if (response.success == false) {
            $('.hover-elevate-up').prop("disabled", true);
            alert(`The UOM conversion for ${response?.itemBaseUom?.base_uom?.short_form} to ${$(currentThis).find("option:selected").text()} is not set.`)
          } else {
            $('.hover-elevate-up').prop("disabled", false);
          }
        });
      }
    })
    $(document).on('change', '.unit_measure', function() {
      let currentThis = $(this);
      let item = $(this).closest("tr").find(".item");
      item = item?.val();
      $(this).prop('name', `unit_id[${item}]`);
      if (item != null && $(this).val() != null) {

        let data = {
          item,
          unit_measure: $(this).val(),
        };
        $.get("/fetch-uom-base", data, function(response) {
          if ($(currentThis).find("option:selected").text() == baseUomValue) {
            $('.hover-elevate-up').prop("disabled", false);
            return false;
          }
          if (response.success == false) {
            $('.hover-elevate-up').prop("disabled", true);
            alert(`The UOM conversion for ${response?.itemBaseUom?.base_uom?.short_form} to ${$(currentThis).find("option:selected").text()} is not set.`)
          } else {
            $('.hover-elevate-up').prop("disabled", false);
          }
        });
      }
    })

    $('#received_by').on("change", function() {
      var selectedText = $(this).find(":selected").text().trim().toLowerCase(); // Trim to remove extra spaces
      if (selectedText === "other") {
        $('.other-option input').prop('required', true).prop('disabled', false);
        $('.other-option').show();
      } else {
        $('.other-option').hide();
        $('.other-option input').prop('required', false).prop('disabled', true);
      }
    });


    $('#event_id').on("change", function() {
      const selectedEvent = $(this).val();
      const store_id = $('#store_id').val();
      $("#items-section").css("display", selectedEvent ? "block" : "none");
      $('#items-table-body').empty();
      $('#issue-table-body').empty();

      if (selectedEvent) {
        $.ajax({
          type: 'GET',
          url: "{{ route('fetchEventItemList') }}", // Update this route to your events endpoint
          data: {
            event_id: selectedEvent,
          },
          success: function(response) {
            $('#items-table-body').empty();
            $('#issue-table-body').empty();
            setTimeout(() => {
              $('.add-items').click()
            
            }, 0);
            if (response?.allCompleted) {
              alert("No Received Items Available");
            }
            if (response?.length > 0) {
              response.forEach(item => {
                $('#items-table-body').append(`
                    <tr>
                        <td style="vertical-align: middle;text-align: center;">
                            <input type="checkbox" name="selected_items[]" value="${item.id}" class="select-item form-check-input" checked>
                        </td>
                        <td>
                            <input type="text" class="form-control w-150px w-lg-100 item_name" value="${item.name}" disabled>
                            <input hidden type="text" class="form-control w-150px w-lg-100 item" value="${item.id}" disabled>
                            <input hidden type="text" name="other[${item.id}]" value="0" class="select-item">
                        </td>
                        <td>
                            <input type="text" class="form-control w-150px w-lg-100" value="${item.uom_short}" disabled>
                            <input hidden type="text" class="form-control w-150px w-lg-100" name="unit_id[${item.id}]" value="${item.uom_id}">
                        </td>
                        <td>
                            <input type="number" step="0.001" class="form-control w-150px w-lg-100" value="${item.itemQuantity}" disabled>
                        </td>
                        <td>
                            <input type="number" step="0.001" class="form-control w-150px w-lg-100" value="${item.issued_quantity}" disabled>
                        </td>
                        <td>
                          <select class="form-select select2-unit" name="issue_unit[${item.id}]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                              <option value=""></option>
                              ${item?.unitOptions?.map(unit => `<option ${item.uom_id == unit.id ? 'selected' : ''} value="${unit.id}">${unit.short_form}</option>`).join('')}
                            </select>
                        </td>
                        <td>
                          
                          <input type="number" step="0.001" class="form-control w-150px w-lg-100 quantity-input" name="quantity[${item.id}]" value="${item.remaining_quantity < 0 ? 0 : item.remaining_quantity}">
                          <span class="text-danger" id="error-${item.id}"></span>
                        </td>
                        <input type="hidden" name="remaining_quantity[${item.id}]" value="${item.remaining_quantity}">
                    </tr>
                `);
              });
            } else {
              $('#items-table-body').append(`<tr>
                        <td colspan="7" style="vertical-align: middle;text-align: center;">No Record Found </td></tr>`);
            }
          },
          error: function() {
            // $("#items-section").css("display", "none");
            $('#items-table-body').empty();
            $('#issue-table-body').empty();
          }
        });
      }
    });
    $(document).on('change', '#select-all', function() {
      const isChecked = $(this).is(':checked');
      $('.select-item').each(function () {
        const $row = $(this).closest('tr');
        const $quantityInput = $row.find('.quantity-input');
        const quantity = parseFloat($quantityInput.val()) || 0;
        // if (quantity < 1) {
        //   return;
        // }
        $(this).prop('checked', isChecked);
        $quantityInput.prop('disabled', !isChecked);
        const $select2 = $row.find('.select2-unit');
        $select2.prop('disabled', !isChecked);
        $select2.parent().find('.form-control').toggleClass('disable', !isChecked);
        $select2.select2('destroy').select2();
      });
    });

    $(document).on('change', '.select-item', function() {
      const allChecked = $('.select-item:checked').length === $('.select-item').length;
      $('#select-all').prop('checked', allChecked);
      const isChecked = $(this).is(':checked');
      $(this).closest("tr").find(".quantity-input").prop('disabled', !isChecked);

      $(this).closest("tr").find(".select2-unit").prop("disabled", !isChecked);
      $(this).closest("tr").find(".select2-unit").parent().find(".form-control").toggleClass("disable",!isChecked);
    
    // Destroy and reinitialize Select2 to reflect the disabled state
    $(this).closest("tr").find(".select2-unit").select2("destroy").select2();
    });

    let anyError = <?php echo json_encode($errors->any()); ?>;
    if (anyError) {
      $('#event_id, #received_by').trigger('change');
    }
    $('form').on('submit', function () {
      const button = $('.submit-button');
      $('.button-text').hide()
      button.prop('disabled', true);
      button.find('.spinner-border').removeClass('d-none'); // show spinner
    });
  })
</script>
@endpush