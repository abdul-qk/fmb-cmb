@php
use App\Helpers\DateHelper;
@endphp
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
  <div  id="kt_post">
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
                  <th>Place</th>
                  <th>Event Date</th>
                  <th>Event</th>
                  <th>Dish</th>
                  <th>Chef</th>
                  <th>Created</th>
                  <th>Last Modified</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($results as $key => $result)
                <tr>
                  <td> {{ isset($result->event->place->name) ? $result->event->place->name : '-' }}</td>
                  <td> {{ \Carbon\Carbon::parse($result->event->date)->isoFormat('Do MMM YYYY') }} <br/>
                    {{\Carbon\Carbon::parse($result->event->start)->isoFormat('hh:mm A')}} -
                    {{\Carbon\Carbon::parse($result->event->end)->isoFormat('hh:mm A')}}
                  </td>
                  <td> {{ isset($result->event->name) ? $result->event->name : '-' }} </td>
                  <td> {!! isset($result['recipes']) ? collect($result['recipes'])->map(function ($recipe) {
                      return $recipe['dish']->name;
                    })->join('<br /> ') : '-' !!}</td>
                  <td> {!! isset($result['recipes']) ? collect($result['recipes'])->map(function ($recipe) {
                      return $recipe['chefUser']->name;
                    })->join('<br /> ') : '-' !!}
                  </td>
                  
                  <td>
                    {{ $result->createdBy ? $result->createdBy->name : '-' }}
                    <br />
                    {{ $result->created_at->isoFormat('Do MMM YYYY') }} <br>
                    {{ $result->created_at->isoFormat('hh:mm A') }}
                  </td>
                  <td>
                    {{ $result->updatedBy ? $result->updatedBy->name : '-' }}
                    <br />
                    @if($result->updated_at)
                    {{ $result->updated_at->isoFormat('Do MMM YYYY') }} <br>
                    {{ $result->updated_at->isoFormat('hh:mm A') }}
                    @else
                    -
                    @endif
                  </td>
                  <td>
                    @if(hasPermissionForModule('show', $currentModuleId))
                    <a title="Show" href="{{ route($show, $result->id) }}" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3 mb-3">
                      <i class="fa-regular fa-eye"></i>
                    </a>
                    @endif
                    @if(hasPermissionForModule('edit', $currentModuleId))
                      @if($result->purchaseOrder == null && $result->item_quantity != "chef-input" ) 
                        <a title="Approve" href="{{ route('menus.approve', $result->id) }}" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3 mb-3">
                          <i class="bi bi-journal"></i>
                        </a>
                        <a title="Edit" href="{{ route($edit, $result->id) }}" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3 mb-3">
                          <i class="fa-regular fa-pen-to-square"></i>
                        </a>
                        <br/>
                      @endif
                    @endif
                    @if(hasPermissionForModule('delete', $currentModuleId))
                    <!-- Delete Form (Hidden) -->
                    <form id="delete-form-{{ $result->id }}" action="{{ route($destroy, $result->id) }}" method="POST" style="display: none;">
                      @csrf
                      @method('DELETE')
                    </form>
                    
                    <!-- Delete Button -->
                    <a title="Delete" href="#" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3 mb-3" onclick="event.preventDefault(); confirmDelete({{ $result->id }});">
                      <i class="fa-regular fa-trash-can"></i>
                    </a>
                    @endif
                    
                    @if($result->item_quantity == "chef-input" && $result->purchaseOrder == null) 
                      <a title="Chef Input" href="{{ route('menus.chef-input', $result->id) }}" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3 mb-3">
                       <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M117.133 452a7.499 7.499 0 0 0-7.5 7.5v45c0 4.143 3.357 7.5 7.5 7.5s7.5-3.357 7.5-7.5v-45a7.5 7.5 0 0 0-7.5-7.5zm98.658 25a7.5 7.5 0 1 0 0 15 7.5 7.5 0 0 0 0-15zm45 0a7.5 7.5 0 1 0 0 15 7.5 7.5 0 0 0 0-15zm-45-45a7.5 7.5 0 1 0 0 15 7.5 7.5 0 0 0 0-15zm45 0a7.5 7.5 0 1 0 0 15 7.5 7.5 0 0 0 0-15zM454.87 247.5h-90c-12.406 0-22.5 10.094-22.5 22.5v72c0 12.406 10.094 22.5 22.5 22.5h9c4.136 0 7.5 3.364 7.5 7.5v12.959a66.067 66.067 0 0 0-15.339-7.508l-84.4-28.399c-5.727-1.931-8.555-7.347-8.131-13.124 28.896-16.937 48.885-47.495 51.083-82.798 14.037-.383 25.341-11.913 25.341-26.041 0-14.058-11.191-25.544-25.131-26.036.013-1.469.084-12.72-.09-13.92a1116.258 1116.258 0 0 1-3.864-29.711c-1.273-10.993-2.59-22.356-3.909-30.053 16.592-12.948 25.142-33.189 22.884-54.221a59.313 59.313 0 0 0-6.438-21.229 7.5 7.5 0 0 0-13.295 6.945 44.377 44.377 0 0 1 4.818 15.884c1.709 15.922-4.801 31.234-17.412 40.961-4.247 3.274-6.313 8.604-5.392 13.911 1.266 7.28 2.576 18.591 3.844 29.528.807 6.967 1.632 14.083 2.463 20.319a691.264 691.264 0 0 0-83.693-5.517 693.077 693.077 0 0 0-90.563 5.541l6.268-48.229c.883-5.477-1.469-11.025-6.025-14.182-12.932-8.958-20.228-23.688-19.517-39.404 1.034-22.878 19.767-41.835 42.645-43.157a45.404 45.404 0 0 1 13.445 1.227c5.338 1.308 10.878-.494 14.45-4.707C194.056 20.847 206.662 15 219.998 15c13.335 0 25.941 5.847 34.586 16.04 3.571 4.212 9.106 6.016 14.451 4.707a45.523 45.523 0 0 1 16.417-.959 44.719 44.719 0 0 1 13.196 3.73 7.498 7.498 0 0 0 9.94-3.698 7.498 7.498 0 0 0-3.698-9.94 59.615 59.615 0 0 0-17.619-4.98 60.422 60.422 0 0 0-21.456 1.193C254.321 7.684 237.641 0 219.998 0c-17.644 0-34.323 7.683-45.817 21.093a60.214 60.214 0 0 0-17.529-1.547c-14.844.857-28.824 7.205-39.368 17.874C106.737 48.092 100.559 62.148 99.888 77c-.94 20.784 8.649 40.274 25.667 52.209-.106.596-7.569 58.157-7.592 58.721-.013.33-.026 12.781-.015 13.123-13.94.492-25.131 11.979-25.131 26.036 0 14.127 11.302 25.656 25.337 26.041a102.607 102.607 0 0 0 7.055 31.64c1.546 3.896 5.968 5.699 9.737 4.204a7.498 7.498 0 0 0 4.204-9.737c-4.115-10.37-6.202-21.339-6.202-32.602v-51.833c30.327-4.013 61.129-5.994 91.689-5.851 28.373.137 56.983 2.119 85.156 5.847v51.837c0 48.756-39.666 88.422-88.423 88.422-24.644 0-48.357-10.403-65.062-28.542a7.5 7.5 0 0 0-11.034 10.162 103.34 103.34 0 0 0 26.715 20.793v.27c0 5.13-3.268 9.676-8.132 11.312l-84.401 28.4a65.778 65.778 0 0 0-44.827 62.388v64.66c0 4.143 3.357 7.5 7.5 7.5s7.5-3.357 7.5-7.5v-64.66a50.791 50.791 0 0 1 34.612-48.172l70.763-23.811 35.747 27.697c-13.223 7.805-21.572 22.194-21.572 37.724V504.5c0 4.143 3.357 7.5 7.5 7.5s7.5-3.357 7.5-7.5v-71.223c0-11.119 6.506-21.353 16.574-26.069l86.39-40.475 74.097 24.933a51.007 51.007 0 0 1 20.128 12.656v12.392h-.062c-10.521 0-19.081 8.561-19.081 19.082v57.122c0 10.521 8.56 19.082 19.081 19.082h41.258c19.27 0 34.946-15.678 34.946-34.947v-25.392c0-13.583-7.795-25.374-19.143-31.154V372c0-4.136 3.364-7.5 7.5-7.5h9c12.406 0 22.5-10.094 22.5-22.5v-72c.003-12.406-10.091-22.5-22.497-22.5zm-130.077-31.432c5.665.473 10.131 5.235 10.131 11.021s-4.466 10.548-10.131 11.021zm-206.846 22.041c-5.665-.473-10.131-5.235-10.131-11.021s4.466-10.548 10.131-11.021zm87.888 150.154-33.983-26.33a26.894 26.894 0 0 0 14.425-18.048c11.161 4.033 23.031 6.172 35.094 6.172 13.281 0 25.982-2.523 37.657-7.104a26.888 26.888 0 0 0 9.66 15.862zm236.678 63.398v25.392c0 10.999-8.948 19.947-19.946 19.947h-41.258a4.086 4.086 0 0 1-4.081-4.082v-57.122a4.086 4.086 0 0 1 4.081-4.082h41.258c10.997 0 19.946 8.948 19.946 19.947zM462.37 342c0 4.136-3.364 7.5-7.5 7.5h-9c-12.406 0-22.5 10.094-22.5 22.5v44.734c-.724-.032-26.208-.017-27-.021V372c0-12.406-10.094-22.5-22.5-22.5h-9c-4.136 0-7.5-3.364-7.5-7.5v-72c0-4.136 3.364-7.5 7.5-7.5h90c4.136 0 7.5 3.364 7.5 7.5zM212.498 100.624v35.958c0 4.143 3.357 7.5 7.5 7.5s7.5-3.357 7.5-7.5v-35.958c0-4.143-3.357-7.5-7.5-7.5s-7.5 3.357-7.5 7.5zm45.444-2.88-14.956 35.958a7.5 7.5 0 0 0 4.045 9.805c3.738 1.555 8.199-.172 9.805-4.045l14.956-35.958a7.5 7.5 0 1 0-13.85-5.76zm14.679 144.799v-15.479c0-4.143-3.357-7.5-7.5-7.5s-7.5 3.357-7.5 7.5v15.479c0 4.143 3.357 7.5 7.5 7.5s7.5-3.357 7.5-7.5zM379.87 277.5a7.499 7.499 0 0 0-7.5 7.5v42c0 4.143 3.357 7.5 7.5 7.5s7.5-3.357 7.5-7.5v-42c0-4.143-3.357-7.5-7.5-7.5zM172.248 93.699a7.5 7.5 0 0 0-4.045 9.805l14.956 35.958c1.606 3.872 6.058 5.603 9.805 4.045a7.5 7.5 0 0 0 4.045-9.805l-14.956-35.958a7.5 7.5 0 0 0-9.805-4.045zM409.87 277.5a7.499 7.499 0 0 0-7.5 7.5v42c0 4.143 3.357 7.5 7.5 7.5s7.5-3.357 7.5-7.5v-42c0-4.143-3.357-7.5-7.5-7.5zm30 0a7.499 7.499 0 0 0-7.5 7.5v42c0 4.143 3.357 7.5 7.5 7.5s7.5-3.357 7.5-7.5v-42c0-4.143-3.357-7.5-7.5-7.5zm-217.393 16.345c7.604-.272 13.312-3.249 16.761-5.698a7.498 7.498 0 0 0 1.773-10.457 7.499 7.499 0 0 0-10.457-1.773c-1.457 1.034-4.514 2.792-8.615 2.938-4.661.166-8.105-1.772-9.759-2.943a7.499 7.499 0 0 0-10.455 1.785 7.499 7.499 0 0 0 1.785 10.455c2.86 2.025 10.757 5.987 18.967 5.693zm-37.358-51.302v-15.479c0-4.143-3.357-7.5-7.5-7.5s-7.5 3.357-7.5 7.5v15.479c0 4.143 3.357 7.5 7.5 7.5s7.5-3.357 7.5-7.5z" fill="currentColor" opacity="1" data-original="currentColor" class=""></path></g></svg>
                      </a>
                    @endif
                    @if($result->item_quantity == "chef-input" && $result->purchaseOrder != null) 
                      <a title="Edit Chef Input" href="{{ route('menus.edit-chef-input', $result->id) }}" class="btn btn-icon btn-color-muted btn-bg-light btn-active-color-primary btn-sm me-3 mb-3">
                       <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M500.168 113.547c-9.174-17.964-23.8-30.223-42.283-35.451-24.959-7.059-51.918-.482-71.593 9.267a87.774 87.774 0 0 0-142.538-43.58 87.781 87.781 0 0 0-143.436 47.3c-33.007-.248-57.594 8.83-77.039 28.274-18.81 18.808-24.245 44.029-14.538 67.464 11.625 28.065 41.459 46.111 78.781 48.117v68.454c0 7.508 5.36 18.451 51.707 26.751 30.021 5.375 69.847 8.336 112.145 8.336s82.124-2.961 112.144-8.336c46.347-8.3 51.707-19.243 51.707-26.751v-69.474c27.447-6.229 68.992-28.906 85.342-60.609 9.902-19.199 9.764-39.864-.399-59.762zM401.225 287.23c-7.719-3.692-19.577-7.343-37.707-10.589-30.02-5.376-69.847-8.337-112.144-8.337s-82.125 2.961-112.145 8.337c-18.13 3.246-29.988 6.9-37.707 10.589v-52.54c17.388-2.146 33.645-11.881 50.737-22.123 29.341-17.581 57.054-34.186 92.431-16.071 61.726 31.608 108.382 40.22 140.895 40.219 5.637 0 10.848-.259 15.64-.7zm-44.892 29.941c-28.638 4.712-65.914 7.308-104.959 7.308s-76.322-2.6-104.96-7.308c-31.078-5.113-42.159-11.193-44.561-13.779 2.4-2.587 13.483-8.667 44.561-13.78 28.638-4.713 65.914-7.308 104.96-7.308s76.321 2.6 104.959 7.308c31.078 5.113 42.159 11.193 44.562 13.78-2.403 2.586-13.484 8.666-44.562 13.779zm131.791-150.279c-15.235 29.54-57.518 50.866-80.92 54.314-29.6 4.362-80.71 1.45-156.133-37.171-13.046-6.681-25.287-9.37-36.857-9.37-25.938 0-48.5 13.518-69.151 25.893-17.656 10.58-34.334 20.573-50.541 20.573-41.6 0-64.9-20.49-72.847-39.667-7.465-18.024-3.165-37.541 11.5-52.21 17.9-17.895 40.3-25.314 72.6-24.043 35.149 2.123 56.149 21.034 65.541 32.122A7 7 0 0 0 182 128.285c-17.048-20.129-40.793-32.836-67.5-36.3a74.183 74.183 0 0 1 71.556-55.843 73.224 73.224 0 0 1 52.7 22.167 7 7 0 0 0 10 0 73.217 73.217 0 0 1 52.7-22.167 74.183 74.183 0 0 1 72.219 58.741c-14.508 10.922-28.762 31.149-28.762 53.468a7 7 0 1 0 14 0c0-21.9 17.077-38.565 26.152-44.355 16.552-10.07 44.528-19.354 69.011-12.43 14.851 4.2 26.164 13.738 33.626 28.348 8.137 15.937 8.28 31.742.422 46.978zm-23.068 189.576-79.014 21.172c-1.139.306-1.952.227-2.414-.235a1.994 1.994 0 0 1-.486-1.817c.169-.63.832-1.105 1.972-1.41l79.014-21.172a15.86 15.86 0 0 0-7.563-30.8l-107.611 24.05a22.6 22.6 0 0 0-17.511 24.722l-84.8 22.722-175.162-46.936a12.147 12.147 0 0 0-11.756 20.3l46.418 46.665a6.991 6.991 0 0 0 1.575 1.18c-26.245 5.58-28.213 6.107-28.972 6.31a34.911 34.911 0 1 0 18.072 67.442c5.639-1.512 93.38-29.251 131.328-41.261l57.63 15.442a7 7 0 0 0 8.573-4.95l1.672-6.241 113.314 30.362a21.715 21.715 0 0 0 5.626.746 21.6 21.6 0 0 0 5.555-42.471l-101.333-27.153 22-5.895a22.587 22.587 0 0 0 20.706 13.712 22.678 22.678 0 0 0 6.8-1.051l105.212-33a15.851 15.851 0 0 0-8.846-30.436zM334.844 384.56l2.471 9.223-45.178 12.106-18.445-4.943zm-241.65 90.578a20.911 20.911 0 0 1-10.825-40.4c1.6-.429 12.479-2.83 54.726-11.739l65.919 17.663C149.2 457.681 97.08 474.1 93.194 475.138zm189.444-27.631-167.9-44.988L74.5 362.067l215.554 57.758zm138.869 9.844a7.6 7.6 0 0 1-8.548 11.139l-113.314-30.362 3.933-14.68 113.315 30.363a7.545 7.545 0 0 1 4.614 3.54zm48.206-83.8-105.212 33a8.593 8.593 0 0 1-10.874-5.976l-8.048-30.036a8.594 8.594 0 0 1 6.427-10.612l107.612-24.046a1.844 1.844 0 0 1 2.2 1.342 1.851 1.851 0 0 1-1.309 2.268l-79.014 21.172A16.63 16.63 0 0 0 371 368.537a15.617 15.617 0 0 0-1.362 11.643c1.981 7.395 9.676 13.758 20.029 10.983l79.014-21.172a1.854 1.854 0 0 1 2.268 1.309 1.847 1.847 0 0 1-1.236 2.246z" fill="currentColor" opacity="1" data-original="currentColor" class=""></path></g></svg>
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
    });
    $('input[data-kt-table-filter="search"]').on('keyup', function() {
      table.search(this.value).draw();
    });
  });

  function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this Item?')) {
      var form = document.getElementById('delete-form-' + id);
      if (form) {
        form.submit();
      } else {
        console.error('Form not found for Item ID:', id);
      }
    }
  }
</script>
@endpush