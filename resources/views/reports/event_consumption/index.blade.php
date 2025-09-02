@push('styles')
<style>
  th {
    padding-right: .75rem !important;
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
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center me-3 flex-wrap lh-1">
        <!--begin::Separator-->
        <span class="h-20px border-gray-200 border-start mx-4"></span>
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
    <div id="kt_content_container">
      <div class="">
        <div class="card">
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
          <form class="form mb-0" method="GET" action="{{ route($indexRoute) }}" style="padding:1rem 2.25rem 0">
              @csrf
              <div class="row">
                  <div class="col-md-2 mb-5">
                      <label class="form-label">Event</label>
                      <select class="form-select" name="event_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                          <option value=""></option>
                          @foreach($events as $event)
                          <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>{{$event->name}}</option>
                          @endforeach
                      </select>
                  </div>
                  <div class="col-md-2 mb-5">
                      <label class="form-label">Event Date From</label>
                      <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') ? request('date_from') : $dateFrom }}">
                  </div>
                  <div class="col-md-2 mb-5">
                    <label class="form-label">Event Date To</label>
                    <input type="date" class="form-control" name="date_to" id="date_to" value="{{ request('date_to') ? request('date_to') : $dateTo }}">
                  </div>
                  <div class="col-md-2 mb-5">
                    <label class="form-label">Host Name</label>
                    <input type="text" class="form-control" name="host_name" id="host_name" value="{{ request('host_name') ? request('host_name') : '' }}">
                  </div>
                  <div class="col-md-2 mb-5">
                    <label class="form-label">Host ITS #</label>
                    <input type="text" class="form-control" name="host_its_no" id="host_its_no" value="{{ request('host_its_no') ? request('host_its_no') : '' }}">
                  </div>
                  <div class="col-md-2 mb-5">
                    <label class="form-label">Host Sabeel #</label>
                    <input type="text" class="form-control" name="host_sabeel_no" id="host_sabeel_no" value="{{ request('host_sabeel_no') ? request('host_sabeel_no') : '' }}">
                  </div>
                  <div class="col-md-3 mb-5">
                      <label class="form-label">Menu</label>
                      <select class="form-select" name="dish_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                          <option value=""></option>
                          @foreach($dishes as $dish)
                          <option value="{{ $dish->id }}" {{ request('dish_id') == $dish->id ? 'selected' : '' }}>{{$dish->name}}</option>
                          @endforeach
                      </select>
                  </div>
                  <div class="col-md-2 mb-5">
                      <label class="form-label">Meal Time</label>
                      <select class="form-select" name="meal_time" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                          <option value=""></option>
                          @foreach($mealTimes as $mealTime)
                          <option value="{{ $mealTime }}" {{ request('meal_time') == $mealTime ? 'selected' : '' }}>{{ucfirst($mealTime)}}</option>
                          @endforeach
                      </select>
                  </div>
                  <div class="col-md-2 mb-5">
                      <label class="form-label">Meal Type</label>
                      <select class="form-select" name="meal_type" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                          <option value=""></option>
                          @foreach($mealTypes as $mealType)
                          <option value="{{ $mealType }}" {{ request('meal_type') == $mealType ? 'selected' : '' }}>{{ucfirst($mealType)}}</option>
                          @endforeach
                      </select>
                  </div>
                  <div class="col-md-2 mb-5">
                      <label class="form-label">Event Status</label>
                      <select class="form-select" name="event_status" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                          <option value=""></option>
                          @foreach($eventStatuses as $eventStatus)
                          <option value="{{ $eventStatus }}" {{ request('event_status') == $eventStatus ? 'selected' : '' }}>{{$eventStatus}}</option>
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
                  
              </div>
              
          </form>
        </div>
          <div class="card-header border-0">
              <!--begin::Card title-->
              <div class="card-title">
                  <!--begin::Search-->
                  <div class="d-flex align-items-center position-relative my-1">
                  <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                  <input type="text" data-kt-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Search" />
                  </div>
                  <!--end::Search-->
              </div>
              <div class="card-toolbar">
                <div class="dt-pagination"></div>
              </div>
              <!--begin::Card toolbar-->
              <div class="card-toolbar">
                  <!--begin::Toolbar-->
                  <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                      <!--begin::Add button-->
                      <div class="dt-buttons-container"></div>
                      <!--end::Add button-->
                  </div>
                  <!--end::Toolbar-->
              </div>
              <!--end::Card toolbar-->
          </div>
          <div class="card-body py-0">
          <div class="table-responsive">
          @php
            $maxIssues = collect($results)->max(function($result) {
              return count($result['issued_quantities']);
            });
            $maxReturns = collect($results)->max(function($result) {
              return count($result['returned_quantities']);
            });
          @endphp
          <table id="kt_datatable" class="table table-bordered gy-5">
              <thead>
                  <tr class="fw-semibold fs-6 text-muted">
                    <th>Item Category</th>
                    <th>Item</th>
                    <th>UOM</th>
                    <th>Requested</th>
                    <th>Consumed</th>
                    <th>Abnormal</th>
                    @for ($i = 0; $i < $maxIssues; $i++)
                      <th>Issue {{ $i + 1 }}</th>
                    @endfor
                    @for ($i = 0; $i < $maxReturns; $i++)
                      <th>Return {{ $i + 1 }}</th>
                    @endfor
                  </tr>
              </thead>
              <tbody>
                  @foreach($results as $key => $result)
                  <tr>
                      <td> {{ $result['category_name'] }} </td>
                      <td> {{ $result['item_name'] }} </td>
                      <td> {{ $result['base_uom'] }} </td>
                      <td> {{ $result['requested_quantity'] }} </td>
                      <td> {{ ($result['issued_quantity'] - $result['returned_quantity']) }} </td>
                      <td> 
                        @php
                          $abnormal = $result['requested_quantity'] - ($result['issued_quantity'] - $result['returned_quantity']);
                        @endphp
                        <span class="{{ $abnormal > 0 ? 'text-danger opacity-75' : '' }}">
                            {{ $abnormal }}
                        </span>
                      </td>
                      @for ($i = 0; $i < $maxIssues; $i++)
                        <td>{{ $result['issued_quantities'][$i] ?? '-' }}</td>
                      @endfor
                      @for ($i = 0; $i < $maxReturns; $i++)
                        <td>{{ $result['returned_quantities'][$i] ?? '-' }}</td>
                      @endfor
                  </tr>
                  @endforeach
              </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
  $(document).ready(function() {
    var table = $("#kt_datatable").DataTable({
      "searching": true,
      "pageLength": 20,
      "paging": true,
      "scrollY": "400px",
      "scrollX": true,
      "scrollCollapse": true,
      "fixedHeader": true,
      "info": true,
      "ordering": true,
      "order": [ [0, "asc"] ],
      "dom": 'Brtip', // Add Buttons layout
      // "dom": '<"top"p>Brt<"bottom"ip><"clear">',
      "buttons": [
        {
          extend: 'excelHtml5',
          text: 'Export to Excel',
          titleAttr: 'Download Excel',
          title: '',
          filename: function() {
            var dateFrom = $("#date_from").val();
            var dateTo = $("#date_to").val();
            return `FMB_Event_Consumption_Report_${dateFrom}_${dateTo}`;
          }
        },
        {
          extend: 'pdfHtml5',
          text: 'Export to PDF',
          titleAttr: 'Download PDF',
          title: '',
          orientation: 'landscape',
          pageSize: 'A3',
          className: 'ms-3',
          filename: function() {
              var dateFrom = $("#date_from").val();
              var dateTo = $("#date_to").val();
              return `FMB_Event_Consumption_Report_${dateFrom}_${dateTo}`;
          },
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
  });
</script>
@endpush