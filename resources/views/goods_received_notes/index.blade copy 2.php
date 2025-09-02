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
              <select class="form-select " id="vendor_id" name="vendor_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($vendors as $vendor)
                <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->name ? 'selected' : '' }}>{{ $vendor->id }} - {{ $vendor->name }}</option>
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
@endsection
@push('scripts')
<script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
  $(document).ready(function() {
    const appTimezone = "{{ env('APP_TIMEZONE', 'UTC') }}";


    let currentModuleId = "{{$currentModuleId}}";
    var orderByColumns = {
      0: 'id',
      1: 'grn_date',
      2: 'vendor_id',
      3: 'amount',
      4: 'discount',
      5: 'amount',
      6: 'bill_no',
      7: 'id',
    };
    const urlParams = new URLSearchParams(window.location.search);


    function getOrdinalSuffix(day) {
      if (day > 3 && day < 21) return 'th'; // handles 11th–13th
      switch (day % 10) {
        case 1:
          return 'st';
        case 2:
          return 'nd';
        case 3:
          return 'rd';
        default:
          return 'th';
      }
    }

    function formatDateWithOrdinal(dateStr, appTimezone = 'Asia/Karachi') {
      if (!dateStr) return '';

      const date = new Date(dateStr);
      const options = {
        timeZone: appTimezone,
        month: 'short',
        year: 'numeric'
      };
      const day = new Intl.DateTimeFormat('en-GB', {
        timeZone: appTimezone,
        day: 'numeric'
      }).format(date);
      const suffix = getOrdinalSuffix(parseInt(day));
      const rest = new Intl.DateTimeFormat('en-GB', options).format(date);

      return `${day}${suffix} ${rest}`;
    }

    function renderImageTd(row) {
      let html = '';

      if (Array.isArray(row.imageData)) {
        row.imageData.forEach(image => {
          const fileType = image.type.toLowerCase();
          const isDoc = ['pdf', 'doc', 'docx'].includes(fileType);
          const iconClass = isDoc ? 'fa-file' : 'fa-image';

          html += `
        <a title="Image" target="_blank" href="${image.url}" class="me-2">
          <i class="fa-regular ${iconClass} fs-3 text-primary"></i>
        </a>
      `;
        });
      }
      html += ` ${row.bill_no ?? ''}`;
      return html;
    }

    function renderActions(row) {
      let html = '';

      const showPermission = @json(hasPermissionForModule('show', $currentModuleId));
      const editPermission = @json(hasPermissionForModule('edit', $currentModuleId));
      const deletePermission = @json(hasPermissionForModule('delete', $currentModuleId));

      const showUrl = "{{ route($show, ':id') }}".replace(':id', row.id);
      const editUrl = "{{ route($edit, ':id') }}".replace(':id', row.id);
      const deleteUrl = "{{ route($destroy, ':id') }}".replace(':id', row.id);
      const rejectUrl = "{{ route('open_purchase_orders.reject', ':id') }}".replace(':id', row.id);
      const approveUrl = "{{ route('open_purchase_orders.approve', ':id') }}".replace(':id', row.id);

      // Edit & Delete buttons if status is "pending"

      if (row.status === 'pending') {
        if (editPermission) {
          html += `
          <a title="Edit" href="${editUrl}" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3">
            <i class="fa-regular fa-pen-to-square"></i>
            </a>
          `;
        }
      }

      // Always show the "Show" button
      if (showPermission) {
        html += `
          <a title="Show" href="${showUrl}" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3">
            <i class="fa-regular fa-eye"></i>
          </a>
        `;
      }

      // Delete, Approve, and Reject buttons (also if status is "pending")
      if (row.status === 'pending') {
        if (deletePermission) {
          html += `
          <a title="Delete" href="#" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3"
            onclick="event.preventDefault(); confirmDelete(${row.id});">
            <i class="fa-regular fa-trash-can"></i>
          </a>`;
        }
        html += `
          <form id="delete-form-${row.id}" action="${deleteUrl}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
          </form>
          
          <form id="reject-form-${row.id}" action="${rejectUrl}" method="POST" style="display: none;">
             @csrf
            @method('PUT')
          </form>

          <br />
          <a title="Approve" href="${approveUrl}" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm mt-3 me-3">
            <i class="fas fa-check"></i>
          </a>
          <a title="Reject" href="#" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3 mt-3"
            onclick="event.preventDefault(); confirmReject(${row.id});">
            <i class="fas fa-times"></i>
          </a>
        `;
      }

      return html;
    }

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
          d.vendor_id = $('#vendor_id').val() || '';

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

      "columns": [{
          "data": "id",
          "render": function(data, type, row) {
            return ` ${data}`;
          },
        },
        { 
            "data": "grn_date",
            "type": "date",
            "orderable": true,
            "render": function(data, type, row) {
                if (type === 'display') {
                    // Custom logic for date formatting and highlighting
                    var date = new Date(data);
                    var day = ("0" + date.getDate()).slice(-2); // Add leading zero for day
                    var month = ("0" + (date.getMonth() + 1)).slice(-2); // Add leading zero for month
                    var year = date.getFullYear();

                    // Custom Logic: Check if the date is today or in the past
                    var today = new Date();
                    var isPast = date < today;
                    var isToday = date.toDateString() === today.toDateString();
                    
                    // Custom formatting: Highlight today's date with a special class
                    const dateObj = new Date(data);
            const timestamp = dateObj.getTime();
            const formatted = dateObj.toLocaleDateString('en-GB', {
              day: 'numeric',
              month: 'short',
              year: 'numeric'
            });

              return `<span data-order="${timestamp}">${formatDateWithOrdinal(data) }</span>`;
            // return `${data}`;
                }
                return data; // Return the raw date for sorting (YYYY-MM-DD)
            },
            "createdCell": function(td, cellData, rowData, row, col) {
                // Set the raw date (ISO format) for sorting
                $(td).attr('data-order', rowData.grn_date);

                // Additional custom logic can go here (e.g., add attributes to the cell)
                $(td).addClass('custom-date-cell');
            }
        },
        
        {
          "data": "vendor_id",
          "render": function(data, type, row) {
            return `${row?.vendor?.id} - ${row?.vendor?.name} 
          ${row?.vendor?.contact_person?.contact_number != null
            ? `<br/><a href="tel:${row.vendor.contact_person.contact_number}">${row.vendor.contact_person.contact_number}</a>` 
            : ''}`;
          }
        },
        {
          "data": "amount",
          "render": function(data, type, row) {
            return `${row?.discount + row?.amount} ${row?.currency?.short_form}`;
          }
        },
        {
          "data": "discount",
          "render": function(data, type, row) {
            return `${row?.discount} ${row?.currency?.short_form}`;
          }
        },
        {
          "data": "amount",
          "render": function(data, type, row) {
            return `${row?.amount} ${row?.currency?.short_form}`;
          }
        },
        {
          "data": "bill_no",
          "render": function(data, type, row) {
            return renderImageTd(row);
          }
        },
        {
          data: "id",
          render: function(data, type, row) {
            return renderActions(row);
          }
        }

      ],
      "order": [
        [0, 'desc']
      ],
    });

    $('#date_from, #date_to, #vendor_id').change(function() {
      table.draw();
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