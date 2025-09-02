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
        <div class="d-flex justify-content-between border-bottom pb-2 border-bottom-3 border-primary my-3" style="margin: 1rem 2.25rem 0;">
          <h2>
          Filters 
          </h2>
          <div>
            <svg class="close-icon cursor-pointer" style="display: none;" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 682.667 682.667" xml:space="preserve">
              <g>
                <defs>
                  <clipPath id="a" clipPathUnits="userSpaceOnUse">
                    <path d="M0 512h512V0H0Z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)"></path>
                  </clipPath>
                </defs>
                <g clip-path="url(#a)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)">
                  <path d="M0 0h157.333M-28.961 236h215.255a120 120 0 0 0 84.853-35.147l8.372-8.373a119.994 119.994 0 0 0 35.148-84.852v-215.256a119.994 119.994 0 0 0-35.148-84.852l-8.372-8.373A120 120 0 0 0 186.294-236H-28.961a120.001 120.001 0 0 0-84.853 35.147l-8.373 8.373a119.996 119.996 0 0 0-35.146 84.852v215.256a119.996 119.996 0 0 0 35.146 84.852l8.373 8.373A120.001 120.001 0 0 0-28.961 236Z" style="stroke-width:40;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(177.333 256)" fill="none" stroke="var(--bs-primary)" stroke-width="40" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="var(--bs-primary)" class=""></path>
                </g>
              </g>
            </svg>
            <svg class="open-icon cursor-pointer" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 24 24" xml:space="preserve">
              <g>
                <path d="M18 2c2.206 0 4 1.794 4 4v12c0 2.206-1.794 4-4 4H6c-2.206 0-4-1.794-4-4V6c0-2.206 1.794-4 4-4zm0-2H6a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h12a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)" class=""></path>
                <path d="M12 18a1 1 0 0 1-1-1V7a1 1 0 0 1 2 0v10a1 1 0 0 1-1 1z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)" class=""></path>
                <path d="M6 12a1 1 0 0 1 1-1h10a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1z" fill="var(--bs-primary)" opacity="1" data-original="var(--bs-primary)" class=""></path>
              </g>
            </svg>
          </div>
        </div>
        <div class="event-detail" @if(count(request()->query()) <= 1) style="display: none;" @endif>
          <form class="form mb-0" method="GET" action="{{ route($indexRoute) }}" style="padding:0rem 2.25rem 0">
            @csrf
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
                  value="{{ request('date_from') ? request('date_from') : $date_from }}">
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
                  value="{{ request('date_to') ? request('date_to') : $date_to }}">
              </div>
              <div class="col-md-4 col-lg-3 mb-5">
                <label class="form-label">Vendor</label>
                <select class="form-select " id="stitching_type" name="stitching_type" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                  <option value=""></option>
                  @foreach($vendors as $vendor)
                  <option value="{{ $vendor->id }}" {{ old('stitching_type',request('stitching_type')) == $vendor->id ? 'selected' : '' }}>{{ $vendor->id }} - {{ $vendor->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2 mb-5">
                <label class="form-label" style="visibility: hidden;">hidden</label>
                <div>
                  <button type="submit" class="btn btn-sm btn-primary hover-elevate-up" style="margin-top: 6px;">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" x="0" y="0" viewBox="0 0 612.01 612.01" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M606.209 578.714 448.198 423.228C489.576 378.272 515 318.817 515 253.393 514.98 113.439 399.704 0 257.493 0S.006 113.439.006 253.393s115.276 253.393 257.487 253.393c61.445 0 117.801-21.253 162.068-56.586l158.624 156.099c7.729 7.614 20.277 7.614 28.006 0a19.291 19.291 0 0 0 .018-27.585zM257.493 467.8c-120.326 0-217.869-95.993-217.869-214.407S137.167 38.986 257.493 38.986c120.327 0 217.869 95.993 217.869 214.407S377.82 467.8 257.493 467.8z" fill="white" opacity="1" data-original="white" class=""></path></g></svg>
                  </button>
                  <a class="btn btn-sm btn-dark hover-elevate-up ms-2" href="{{ route($indexRoute) }}" style="margin-top: 6px;">
                  <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M21 12a9 9 0 1 1-3.84-7.36l-.11-.32A1 1 0 0 1 19 3.68l1 3a1 1 0 0 1-.14.9A1 1 0 0 1 19 8h-3a1 1 0 0 1-1-1 1 1 0 0 1 .71-.94A7 7 0 1 0 19 12a1 1 0 0 1 2 0z" data-name="Layer 114" fill="white" opacity="1" data-original="white" class=""></path></g></svg>
                  </a>
                </div>
            </div>
          </form>
        </div>
        </div>
       
        <div class="card-header border-0 mt-0">
          <!--begin::Card title-->
          <div class="card-title mt-0">
            <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1">
              <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
              <input type="text" data-kt-table-filter="search" class="form-control form-control-solid ps-13" placeholder="Search" />
            </div>
            <!--end::Search-->
          </div>
          <!--begin::Card title-->
          <!--begin::Card toolbar-->
          <div class="card-toolbar align-items-center">
            <div class="dt-pagination"></div>
          </div>
          <div class="d-flex align-items-center">
            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                <!--begin::Add button-->
                @if(hasPermissionForModule('add', $currentModuleId))
                <a type="button" class="btn btn-primary" href="{{ route($create) }}">
                  <i class="ki-outline ki-plus fs-2"></i>GRN
                </a>
                @endif
            </div>
          </div>
          <div class="card-toolbar">
            <!--end::Toolbar-->
            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
              <!--begin::Add button-->
              <div class="dt-buttons-container"></div>
              <!--end::Add button-->
            </div>
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
                  <td> {{ $result->grn_date ? \Carbon\Carbon::parse($result->grn_date)->isoFormat('Do MMM YYYY') : "-" }} </td>
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
                    <br />
                    <br />
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
@endsection
@push('scripts')
<script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
  $(document).ready(function() {
    var table = $("#kt_datatable").DataTable({
      "searching": true,
      "pageLength": 20,

      "paging": true,
      "scrollY": "1500px",
      "scrollX": true,
      "scrollCollapse": true,
      "fixedHeader": true,
      "info": true,
      "ordering": true,
      "order": [],
      "dom": 'Brtip', // Add Buttons layout
      "buttons": [
        {
            extend: 'excelHtml5',
            text: 'Export to Excel',
            titleAttr: 'Download Excel',
            title: '',
            filename: function() {
              const now = new Date();
              const yyyy = now.getFullYear();
              const mm = String(now.getMonth() + 1).padStart(2, '0');
              const dd = String(now.getDate()).padStart(2, '0');
              const hh = String(now.getHours()).padStart(2, '0');
              const min = String(now.getMinutes()).padStart(2, '0');
              const ss = String(now.getSeconds()).padStart(2, '0');
              const dateTime = `${yyyy}-${mm}-${dd}_${hh}-${min}-${ss}`;
              return `FMB_GRN_${dateTime}`;
            },
            exportOptions: {
              columns: ':not(:last-child)' // Exclude last column
            },
        },
        {
            extend: 'pdfHtml5',
            text: 'Export to PDF',
            titleAttr: 'Download PDF',
            title: '',
            orientation: 'portrait',
            className: 'ms-3', 
            filename: function() {
              const now = new Date();
              const yyyy = now.getFullYear();
              const mm = String(now.getMonth() + 1).padStart(2, '0');
              const dd = String(now.getDate()).padStart(2, '0');
              const hh = String(now.getHours()).padStart(2, '0');
              const min = String(now.getMinutes()).padStart(2, '0');
              const ss = String(now.getSeconds()).padStart(2, '0');
              const dateTime = `${yyyy}-${mm}-${dd}_${hh}-${min}-${ss}`;
              return `FMB_GRN_${dateTime}`;
            },
            exportOptions: {
              columns: ':not(:last-child)' // Exclude last column
            },
            customize: function (doc) {
              for (let i = 0; i < doc.content.length; i++) {
                if (doc.content[i].table) {
                  const tableNode = doc.content[i];
                  tableNode.table.widths = Array(tableNode.table.body[0].length).fill('*');
                  tableNode.table.body.forEach(row => {
                    row.forEach(cell => {
                      cell.alignment = 'center';
                    });
                  });
                  break;
                }
              }
            }
        }
      ],
      "initComplete": function () {
        // Move buttons next to the custom search field
        var buttons = $(".dt-buttons").detach();
        $(".dt-buttons-container").append(buttons);

        var pagination = $("#kt_datatable_paginate").detach();
        $(".dt-pagination").append(pagination);
      }
    });
    $('input[data-kt-table-filter="search"]').on('keyup', function() {
      table.search(this.value).draw();
    });
  });
  $('.open-icon').on('click', function() {
    $(this).hide();
    $('.close-icon').show();
    $('.event-detail').slideDown(300); // 300ms animation
  });

  $('.close-icon').on('click', function() {
    $(this).hide();
    $('.open-icon').show();
    $('.event-detail').slideUp(300);
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