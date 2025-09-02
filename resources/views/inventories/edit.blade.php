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
      @if(Session::has('error'))
      <div class="alert alert-danger d-flex align-items-center p-5">
        <i class="ki-duotone ki-shield-tick fs-2hx text-danger me-4"></i>
        <div class="d-flex flex-column">
          <span>{{ Session::get('error') }}</span>
        </div>
      </div>
      @endif
      <div class="bg-transparent border-0 card shadow-none pt-2">
        <form class="form" method="POST" action="{{ route($update, $result->id) }}">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Event</label>
              <select class="form-select" id="event_id" name="event_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
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
              <select class="form-select" name="kitchen_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
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
              <select class="form-select" id="received_by" name="received_by" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
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
            <div class="col-md-3 col-lg-3 mb-5 other-option" style="display: none;">
              <label class="form-label required">Name</label>
              <input disabled  type="text" class="form-control" name="worker_name"  value="{{ old('worker_name') }}">
            </div>
          </div>
          <div id="items-section" style="display: none;">
            <hr />
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>Base UOM</th>
                    <th class="requested">Requested Qty</th>
                    <th class="requested">Issued Qty</th>
                    <th class="not-requested">Available Qty</th>
                    <th>Issue UOM</th>
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
          </div>
          <div class="col-md-12 mt-3">
            <input type="reset" value="Reset" class="btn btn-dark w-100px mr-2 confirm-reset" style="margin-right: 5px">
           
            <button type="submit" class="btn btn-primary hover-elevate-up w-100px submit-button confirm-submit">
              <span class="spinner-border spinner-border-custom d-none" role="status" aria-hidden="true"></span>
              <span class="button-text"> Issue </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
  $(document).on("change",'#received_by', function() {
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

    if (selectedEvent) {
      $.ajax({
        type: 'GET',
        url: "{{ route('fetchEventItemList') }}", // Update this route to your events endpoint
        data: {
          event_id: selectedEvent,
          item_id: "{{$id}}",
        },
        success: function(response) {
          $('#items-table-body').empty();
          if (response.length == 0) {
            $('.requested').hide();
            $('.not-requested').show();
            $('#items-table-body').append(`
              <tr>
                    <input type="hidden" class="form-control w-150px w-lg-100" name="inventory_quantity" value="{{ $inventoryQuantity }}">
                    <td>
                        <input type="text" class="form-control w-150px w-lg-100" value="{{ $result->name }}" disabled>
                        <input type="hidden" class="form-control w-150px w-lg-100 item" name="item_id" value="{{ $result->id }}">
                    </td>
                    <td>
                        <input type="text" class="form-control w-150px w-lg-100" value="{{ $result->itemBase->baseUom->short_form }}" disabled>
                        <input type="hidden" class="form-control w-150px w-lg-100" name="unit_id" value="{{ $result->itemBase->baseUom->id }}">
                    </td>
                    <td>
                        <input type="number" step="0.001" class="form-control w-150px w-lg-100" value="{{ $inventoryQuantity }}" disabled>
                    </td>
                    <td>
                      <select class="form-select select2-unit" name="issue_unit" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                        @foreach($unitOptions as $unitOption)
                          <option value="{{ $unitOption['id'] }}">{{$unitOption['short_form']}}</option>
                        @endforeach
                      </select>
                    </td>
                    <td>
                        <input type="number" step="0.001" class="form-control w-150px w-lg-100" name="quantity"  value="{{ old('quantity', $inventoryQuantity) }}">
                        @if ($errors->has('quantity'))
                          <span class="text-danger">{{ $errors->first('quantity') }}</span>
                        @endif
                    </td>
                </tr>
                `);
          } else {
            $('.requested').show();
            $('.not-requested').hide();
            response.forEach(item => {
              $('#items-table-body').append(`
                    <tr>
                        <td>
                            <input type="text" class="form-control w-150px w-lg-100" value="${item.name}" disabled>
                            <input type="hidden" class="form-control w-150px w-lg-100 item" name="item_id" value="${item.id}">
                        </td>
                        <td>
                            <input type="text" class="form-control w-150px w-lg-100" value="${item.uom_short}" disabled>
                        </td>
                        <td>
                            <input type="number" step="0.001" class="form-control w-150px w-lg-100" value="${item.itemQuantity}" disabled>
                        </td>
                        <td>
                            <input type="number" class="form-control w-150px w-lg-100" value="${item.issued_quantity}" disabled>
                        </td>
                        <td>
                          <select class="form-select select2-unit" name="issue_unit" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                              <option value=""></option>
                              ${item?.unitOptions?.map(unit => `<option ${item.uom_id == unit.id ? 'selected' : ''} value="${unit.id}">${unit.short_form}</option>`).join('')}
                            </select>
                        </td>
                        <td>
                          <input type="number" step="0.001" class="form-control w-150px w-lg-100 quantity-input" name="quantity" value="${item.remaining_quantity}">
                        </td>
                        <input type="hidden" name="inventory_quantity" value="${item.remaining_quantity}">
                    </tr>
                `);
            });
            $(".select2-unit").select2();
            $(".select2-unit").select2("destroy").select2();
          }
        },
        error: function() {
          $("#items-section").css("display", "none");
          $('#items-table-body').empty();
        }
      });
    }
  });
  $(document).on('change', '.select2-unit', function() {
    console.log("sss")
       let currentThis = $(this);
      let item = $(this).closest("tr").find(".item");
      item = item?.val();

      if (item != null && $(this).val() != null) {

        let data = {
          item,
          unit_measure: $(this).val(),
        };
        $.get("/fetch-uom-base", data, function(response) {
          if (response.success == false) {
            $('.hover-elevate-up').prop("disabled", true);
            alert(`The UOM conversion for ${response?.itemBaseUom?.base_uom?.short_form} to ${$(currentThis).find("option:selected").text()} is not set.`)
          } else {
            $('.hover-elevate-up').prop("disabled", false);
          }
        });
      }
    })
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
  let anyError = <?php echo json_encode($errors->any()); ?>;
    if (anyError) {
      $('#event_id').trigger("change");
    }
    $('form').on('submit', function () {
      const button = $('.submit-button');
      $('.button-text').hide()
      button.prop('disabled', true);
      button.find('.spinner-border').removeClass('d-none'); // show spinner
    });
</script>
@endpush