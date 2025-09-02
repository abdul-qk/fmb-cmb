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
  <div class="" id="kt_post">
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
                    <i class="ki-outline ki-plus fs-2"></i>Add {{ $moduleNameSingular }}</a>
                  @endif
                  <!--end::Add button-->
                </div>
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
                  <th>Ref #</th>
                  <th>Vendor</th>
                  <th>Returned By</th>
                  <th>Returned At</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($results as $key => $result)
                <tr>
                  <td>{{( $result->id )}}</td>
                  <td>{{( $result->vendor->id )}} - {{( $result->vendor->name )}}</td>
                  <td>{{( $result->returnBy->name )}}</td>
                  <td> {{ $result->created_at->isoFormat('Do MMM YYYY, hh:mm A') }}</td>
                  <td>
                    @if(hasPermissionForModule('show', $currentModuleId))
                      <a title="Show" href="{{ route($show, $result->id) }}" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3">
                        <i class="fa-regular fa-eye"></i>
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
          return `FMB_Supplier_Return_${dateTime}`;
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
          return `FMB_Supplier_Return_${dateTime}`;
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
      }
    });
    $('input[data-kt-table-filter="search"]').on('keyup', function() {
      table.search(this.value).draw();
    });
  });
</script>
@endpush