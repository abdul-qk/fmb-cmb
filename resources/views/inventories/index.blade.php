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
        <div class="card-header border-0 justify-content-end">
          <div class="card-toolbar">
            <!--begin::Toolbar-->
            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
              <!--begin::Add button-->
              @if(hasPermissionForModule('add', $currentModuleId))
              <a type="button" class="btn btn-primary me-2" href="/goods-received-notes/create">
                <i class="ki-outline ki-plus fs-2"></i>GRN
              </a>
              <a type="button" class="btn btn-primary me-2" href="{{ route('inventories.create') }}">
                <i class="ki-outline ki-plus fs-2 "></i>Issue to Kitchen
              </a>
              <a type="button" class="btn btn-primary me-2" href="/issued-to-kitchens/create">
                <i class="ki-outline ki-plus fs-2"></i>Return from Kitchen
              </a>
              <a type="button" class="btn btn-primary me-2" href="{{ route('supplier-return.create') }}">
                <i class="ki-outline ki-plus fs-2"></i>Supplier Return
              </a>
              <a type="button" class="btn btn-primary me-2" href="{{ route('adjustment.create') }}">
                <i class="ki-outline ki-plus fs-2"></i>Adjustment
              </a>
              <!-- <a type="button" class="btn btn-primary me-2" href="{{ route('inventories.return') }}">
                <i class="ki-outline ki-plus fs-2"></i>Supplier Return
              </a>
              <a type="button" class="btn btn-primary me-2" href="{{ route('inventories.add','new') }}">
                <i class="ki-outline ki-plus fs-2"></i>Add Inventory
              </a>
              <a type="button" class="btn btn-primary " href="{{ route($create) }}">
                <i class="ki-outline ki-plus fs-2 "></i>Multi-Item GIN</a> -->
               
              @endif
              <!--end::Add button-->
            </div>
            <!--end::Toolbar-->
          </div>
        </div>
        <div class="card-header border-0">
          <!--begin::Card title-->
          <div class="card-title">
            <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1">
              <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
              <input type="text" data-kt-table-filter="search" class="form-control form-control-solid ps-13" placeholder="Search" />
            </div>
            <!--end::Search-->
          </div>
          <!--begin::Card toolbar-->
          <div class="card-toolbar" >
            <div class="dt-pagination"></div>
          
          </div>
          <!--end::Card toolbar-->
          <div class="card-toolbar" data-kt-user-table-toolbar="base">
            <!--begin::Add button-->
              <div class="dt-buttons-container"></div>
            <!--end::Add button-->
          </div>
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body py-0">
          <!--begin::Table-->
          <div class="table-responsive">
            <table id="kt_datatable" class="table table-bordered gy-5">
              <thead>
                <tr class="fw-semibold fs-6 text-muted">
                  <th>Place</th>
                  <th>Item Category</th>
                  <th>Item</th>
                  <th>UOM</th>
                  <!-- <th>Total Qty</th>
                  <th>Issued Qty</th> -->
                  <th>Available Qty</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($results as $key => $result)
                <tr>
                  <td> {!! $result['stores'] !!} </td>
                  <td> {{$result['itemCategoryName']}} </td>
                  <td> {{$result['name']}} </td>
                  <td> {{$result['uom']}} </td>
                  <!-- <td> {{$result['total_quantity']}} </td>
                  <td> {{$result['issued_quantity']}} </td> -->
                  <td> {{$result['remaining_quantity']}} </td>
                  <td>
                  @if(hasPermissionForModule('view', $currentModuleId))
                  <!-- @if($result['issued_quantity'] > 0) -->
                  <a title="Show" href="{{ route($show, $result['id']) }}" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3">
                    <i class="fa-regular fa-eye"></i>
                  </a>
                  @endif
                  @if(hasPermissionForModule('edit', $currentModuleId))

                      <!-- @endif -->
                      @if($result['remaining_quantity'] > 0)
                        <a title="Transfer" href="{{ route('inventories.transfer', $result['id']) }}" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3">
                          <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="14" height="14" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M23.5 15a.5.5 0 0 1-.5-.5c0-3.033-2.467-5.5-5.5-5.5h-8v3.5a.5.5 0 0 1-.854.354l-5.5-5.5a.5.5 0 0 1 0-.707l5.5-5.5A.5.5 0 0 1 9.5 1.5V5h7c4.136 0 7.5 3.364 7.5 7.5v2a.5.5 0 0 1-.5.5zM4.207 7 8.5 11.293V8.5A.5.5 0 0 1 9 8h8.5a6.496 6.496 0 0 1 5.23 2.645C21.93 7.962 19.439 6 16.5 6H9a.5.5 0 0 1-.5-.5V2.707z" fill="currentColor" opacity="1" data-original="currentColor" class=""></path><path d="M15 23a.5.5 0 0 1-.5-.5V19h-7C3.364 19 0 15.636 0 11.5v-2a.5.5 0 0 1 1 0C1 12.533 3.467 15 6.5 15h8v-3.5a.5.5 0 0 1 .854-.354l5.5 5.5a.5.5 0 0 1 0 .707l-5.5 5.5A.5.5 0 0 1 15 23zM1.27 13.355C2.07 16.038 4.561 18 7.5 18H15a.5.5 0 0 1 .5.5v2.793L19.793 17 15.5 12.707V15.5a.5.5 0 0 1-.5.5H6.5a6.496 6.496 0 0 1-5.23-2.645z" fill="currentColor" opacity="1" data-original="currentColor" class=""></path></g></svg>
                        </a>
                        <a title="Issue Goods" href="{{ route($edit, $result['id']) }}" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3">
                          <i class="bi bi-journal"></i>
                        </a>
                      @endif
                  @endif
                  
                  @if($result['supplier_return'] == null && $result['inventory_detail'] == null) 
                    <a title="Edit" href="{{ route('inventories.edit-inventory', $result['id']) }}" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3">
                      <i class="fa-regular fa-pen-to-square"></i>
                    </a>
                  @endif
                  </td>
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
      "paging": true,
      "scrollY": "400px",
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
              return `FMB_Inventory_${dateTime}`;
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
              return `FMB_Inventory_${dateTime}`;
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
</script>
@endpush