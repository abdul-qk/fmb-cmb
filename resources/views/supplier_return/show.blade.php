@push('styles')
<style>
  th,
  td {
    width: 16.6%;
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
    <div id="kt_content_container" class="container-fluid">
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
      <!--begin::Card-->
      <div class="bg-transparent border-0 card shadow-none pt-2">
      <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">#{{$results->id}}</h2>
        <div class="row">
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Vendor</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{$results->vendor->name}}
            </h5>
          </div>

          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Returned By</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{$results->returnBy->name}}
            </h5>
          </div>
          <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Filter</h2>
          <div class="row">
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Item Category</label>
              <select class="form-select" id="item_category" name="item_category" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($itemCategories as $itemCategory)
                <option value="{{ $itemCategory->id }}" {{ old('item_category') == $itemCategory->id ? 'selected' : '' }}>{{ $itemCategory->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div id="items-section">
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr >
                    <th style="vertical-align: middle;">Item</th>
                    <th style="vertical-align: middle;">Base UOM</th>
                    <th style="vertical-align: middle;">Available Qty</th>
                    <th style="vertical-align: middle;">Return UOM</th>
                    <th style="vertical-align: middle;">Return Qty</th>
                    <th style="vertical-align: middle;">Reason</th>
                  </tr>
                </thead>
                <tbody id="items-table-body">
                  @foreach ($results->supplierReturn as $index => $data)
                  <tr class="item-category-{{$data->item->category_id}}" id="item-row-{{ $index }}">
                    <td>{{$data->item->name}}</td>
                    <td>{{$data->unitMeasure->short_form}}</td>
                    <td>{{$data->item->detail->available_quantity}}</td>
                    <td>{{$data->unitMeasure->short_form}}</td>
                    <td>{{$data->quantity}}</td>
                    <td>{{$data->reason}}</td>
                  </tr>
                  @endforeach
                  <tr id="not-found" style="display: none;">
                    <td class="fw-bold text-center" colspan="7"> Not Found</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!--end::Card-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
@endsection
@push('scripts')
<script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
  $(document).ready(function() {
    $(document).on("change", "#item_category", function() {
      let selectedCategory = $(this).val();
      let $rows = $("#items-table-body tr");
      let $filteredRows = $(".item-category-" + selectedCategory);
      let $notFound = $("#not-found");

      if (!selectedCategory) {
        $rows.show();
        $notFound.hide();
      } else {
        $rows.hide();
        $filteredRows.show();
        $notFound.toggle($filteredRows.length === 0);
      }
    });
  });
</script>
@endpush