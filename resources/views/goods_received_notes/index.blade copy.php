@push('styles')
<link href="assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
@endpush
@extends('layout.master')
@section('content')
<!--begin::Content-->
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
    <div id="kt_content_container">
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
      <!--begin::Card-->
      <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0">
        <div class="row mt-3 w-100">
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Date From</label>
              <!-- onfocus="(this.type='date')"
              onblur="(this.type='text')" -->
              <input
                placeholder="Date From"
                type="date"
                class="form-control"
                name="date_from"
                id="date_from"
                value="">
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Date To </label>
              <!-- onfocus="(this.type='date')"
              onblur="(this.type='text')" -->
              <input
                placeholder="Date To"
                type="date"
                class="form-control"
                name="date_to"
                id="date_to"
                value="">
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Vendor</label>
              <select class="form-select " id="stitching_type" name="stitching_type" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($vendors as $vendor)
                <option value="{{ $vendor->id }}" {{ old('stitching_type') == $vendor->name ? 'selected' : '' }}>{{ $vendor->id }} - {{ $vendor->name }}</option>
                @endforeach
              </select>
            </div>
            

          </div>
          <!--begin::Card title-->
          <div class="card-title">
            <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1">
              <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
              <input type="text" data-kt-table-filter="search" class="form-control form-control-solid ps-13" placeholder="Search" />
            </div>
            <!--end::Search-->
          </div>
          <!--begin::Card title-->
          <!--begin::Card toolbar-->
          <div class="card-toolbar">
            <!--begin::Toolbar-->
            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
              <!--begin::Add button-->
              @if(hasPermissionForModule('add', $currentModuleId))
              <a type="button" class="btn btn-primary" href="{{ route($create) }}">
                <i class="ki-outline ki-plus fs-2"></i>GRN
              </a>
              @endif
              <!--end::Add button-->
            </div>
            <!--end::Toolbar-->
          </div>
          <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body py-0">
          <!--begin::Table-->
          <div class="table-responsive">
            <table id="kt_datatable" class="table table-bordered gy-5">
              <thead>
                <tr class="fw-semibold fs-6 text-muted">
                  <th>Ref # </th>
                  <th>GRN Date </th>
                  <th>Vendor</th>
                  <!-- <th>Items</th> -->
                  <th>Subtotal</th>
                  <th>Discount</th>
                  <th>Total Amount</th>
                  <th>Bill</th>
                  <th>Action</th>
                  <!-- <th>Created</th>
                  <th>Last Modified</th> -->
                </tr>
              </thead>
              <tbody>
                @foreach($results as $key => $result)
                <tr>
                  <td> {{$result->id}}</td>
                  <td> {{ $result->grn_date ? \Carbon\Carbon::parse($result->grn_date)->isoFormat('Do MMM YYYY') : "-" }}                  </td>
                  <td> {{isset($result->vendor->name) ? $result->vendor->id .' - '. $result->vendor->name : '-'}}
                    <br />
                    {!! isset($result->vendor->contactPerson)
                    ? '<a href="tel:' . $result->vendor->contactPerson->contact_number . '">' . e($result->vendor->contactPerson->contact_number) . '</a>'
                    : '-' !!}
                  </td>
                  <!-- <td> {{$result->detail->pluck('item')->pluck('name')->implode(', ')}} </td> -->
                  <td> {{ number_format($result->discount + $result->amount) }} {{$result->currency->short_form}} </td>
                  <td> {{ number_format($result->discount) }} {{$result->currency->short_form}}</td>
                  <td>
                    
                    {{number_format($result->amount)}} {{$result->currency->short_form}}
                    <br/>
                    <br/>
                  </td>
                  <td>
                    @foreach ($result->imageData as $image)
                      @if (in_array(strtolower($image['type']), ['pdf', 'doc', 'docx']))
                        <a title="Image" target="_blank" href="{{ $image['url'] }}" class="">
                          <i class="fa-regular fa-file fs-3 text-primary"></i>
                        </a>
                      @else
                        <a title="Image" target="_blank" href="{{ $image['url'] }}" class="">
                          <i class="fa-regular fa-image fs-3 text-primary"></i>
                        </a>
                      @endif
                    @endforeach
                   {{ $result->bill_no}}
                  </td>
                  <td>
                    @if($result->status == 'pending')
                   
                    <a title="Edit" href="{{ route($edit, $result->id) }}" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3">
                      <i class="fa-regular fa-pen-to-square"></i>
                    </a>
                    @endif
                    <a title="Show" href="{{ route($show, $result->id) }}" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3">
                      <i class="fa-regular fa-eye"></i>
                    </a>
                   

                    @if($result->status == 'pending')
                    <!-- Delete Form (Hidden) -->
                    <form id="delete-form-{{ $result->id }}" action="{{ route($destroy, $result->id) }}" method="POST" style="display: none;">
                      @csrf
                      @method('DELETE')
                    </form>
                    <form id="reject-form-{{ $result->id }}" action="{{ route('open_purchase_orders.reject', $result->id) }}" method="POST" style="display: none;">
                      @csrf
                      @method('PUT')
                    </form>
                    <!-- Delete Button -->
                    <a title="Delete" href="#" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3" onclick="event.preventDefault(); confirmDelete({{ $result->id }});">
                      <i class="fa-regular fa-trash-can"></i>
                    </a>
                    <br />
                    <a title="Approve" href="{{ route('open_purchase_orders.approve', $result->id) }}" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm mt-3 me-3">
                      <i class="fas fa-check"></i>
                    </a>
                    <a title="Reject" href="#" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3 mt-3" onclick="event.preventDefault(); confirmReject({{ $result->id }});">
                      <i class="fas fa-times"></i>
                    </a>

                    @endif
                  </td>
                  <!-- <td>
                    {{ $result->createdBy ? $result->createdBy->name : '-' }}
                    <br />
                    {{ $result->created_at->isoFormat('Do MMM YYYY') }} <br>
                    {{ $result->created_at->isoFormat('hh:mm A') }}
                  </td>
                  <td>
                    {{ $result->updatedBy ? $result->updatedBy->name : '-' }}
                    <br>
                    {{ $result->updated_at ? $result->updated_at->isoFormat('Do MMM YYYY') : "-" }}<br/>
                    {{ $result->updated_at ? $result->updated_at->isoFormat('hh:mm A') : "-" }}
                  </td> -->

                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <!--end::Table-->
        </div>
        <!--end::Card body-->
      </div>
      <!--end::Card-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->

@push('scripts')
<script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
  $(document).ready(function() {
    const appTimezone = "{{ env('APP_TIMEZONE', 'UTC') }}";


    let currentModuleId = "{{$currentModuleId}}";
    var orderByColumns = {
      0: 'id',
      // 3: 'country_id',
      // 4: 'auctions',
      // 5: 'customer_type',
    };
    const urlParams = new URLSearchParams(window.location.search);

    const status = urlParams.get('status') ?? ""; 
    const balance = urlParams.get('balance') ?? "";
    const stitchingID = urlParams.get('id') ?? "";
    var table = $("#kt_datatable").DataTable({
      "processing": true, // Show loading indicator
      "serverSide": true, // Enable server-side processing
      "ajax": {
        "url": "/goods-received-notes-data",
        "data": function(d) {
          d.offset = d.start;
          d.limit = d.length;
          var columnIndex = d.order[0].column;
          d.orderBy = orderByColumns[columnIndex];
          d.orderType = d.order[0].dir;
          // Add custom filter data to the request
          d.date_from = $('#date_from').val() || '';
          d.date_to = $('#date_to').val() || '';
          d.stitching_type = $('#stitching_type').val() || '';
          d.balance = $('#balance').val() || '';
          d.delivery_date = $('#delivery_date').val() || '';
          d.url_balance = balance;
          d.url_status = status;
          d.url_id = stitchingID;
          d.textSearch = document.querySelector('[data-kt-table-filter="search"]').value;
        }
      },
      "scrollY": "400px",
      "scrollX": true,
      "scrollCollapse": true,
      "fixedHeader": true,
      "searching": true,
      "paging": true,
      "info": true,
      "ordering": true,
      "initComplete": function(settings, json) {
        // This will hide the default DataTables spinner once data is loaded
        $('.dataTables_processing').hide(); // Hides the processing spinner
      },
      "drawCallback": function(settings) {
        // Ensure the spinner is hidden after each draw (refresh)
        $('.dataTables_processing').hide(); // Hides the processing spinner
      },
      "columns": [{
          "data": "id",
          "render": function(data, type, row) {
            return ` ${data}`;
          },
          "createdCell": function(td) {
            $(td).css({
              "min-width": "64px",
              "text-align": "center"
            }); // Set the desired width
          }
        },
        
      ],
      "order": [
        [0, 'desc']
      ],
    });

    // Trigger filtering on change for each dropdown
    $('#date_from, #date_to, #stitching_type, #balance, #delivery_date').change(function() {
      table.draw(); // Reload the table with updated filters
    });


    table.ajax.reload();
    table.on('draw', function() {
      $('#loader2').css("display", "none")
      KTMenu.createInstances();
    });

    const filterSearch = document.querySelector('[data-kt-table-filter="search"]');
    filterSearch.addEventListener('keyup', function(e) {
      table.ajax.reload();
      $('#loader2').css("display", "flex")
      // table.search(e.target.value).draw();
    });
  });

  function confirmDelete(resultId) {
    if (confirm('Are you sure you want to delete this record?')) {
      var form = document.getElementById('delete-form-' + resultId);
      if (form) {
        form.submit();
      } else {
        console.error('Form not found for record ID:', resultId);
      }
    }
  }
</script>
@endpush