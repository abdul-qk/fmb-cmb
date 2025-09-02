@extends('layout.master')
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Toolbar-->
  <div class="toolbar" id="kt_toolbar">
    <!--begin::Container-->
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <!--begin::Page title-->
      <div data-kt-swapper-mode="prepend" class="page-title d-flex align-items-center me-3 flex-wrap lh-1">
          
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
  <div  id="kt_post">
     <div class="container-fluid" id="kt_content_container">
      <div class="bg-transparent border-0 card shadow-none pt-2">
        <div class="row">
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Vendor</label>
            <input type="text" class="form-control" value="{{ $result->vendor->name }}" disabled>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Events</label>
            <input type="text" class="form-control" value="{{ implode(',', $result->events->pluck('name')->toArray()) }}" disabled>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Place</label>
            <input type="text" class="form-control" value="{{ $result->place->name }}" disabled>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Currency</label>
            <input type="text" class="form-control" value="{{ $result->currency->short_form }}" disabled>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label required">Discount</label>
            <input disabled type="number" class="form-control" min="1" name="discount" id="discount" value="{{old('discount',$result->discount)}}">
          </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label for="amount">Total Amount</label>
              <input type="hidden" name="amount" id="amount-hidden" value="{{ old('amount', $result->amount) }}">
              <input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount', $result->amount) }}" disabled>
            </div>
            @if ($pdfUrl != '')
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">PO Document</label>
              <div class="flex-wrap mb-5 d-flex align-items-end" style="gap: 10px; height: 40px;">
                <span class="doc-file position-relative">
                  <a title="PO Document" class="d-inline-flex align-items-end" style="display: inline-block;" href="{{ url($pdfUrl) }}" target="_blank">
                    <i class="bi bi-file-earmark-text text-primary fs-2x"></i>
                  </a>
                </span>
              </div>
            </div>
            @endif
            @if ($approvedPdfUrl != '')
            <div class="col-md-2">
              <label class="form-label">Approved PO Document</label>
              <div class="flex-wrap mb-5 d-flex align-items-end" style="gap: 10px; height: 40px;">
                <span class="doc-file position-relative">
                  <a title="PO Document Approved" class="d-inline-flex align-items-end" style="display: inline-block;" href="{{ url($approvedPdfUrl) }}" target="_blank">
                    <i class="bi bi-file-earmark-text text-success fs-2x"></i>
                  </a>
                </span>
              </div>
            </div>
            @endif
        </div>
        
        <div id="items-section" style="display: block;">
          <hr />
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Event</th>
                  <th>Item</th>
                  <th>Qty</th>
                  <th>Per Unit Price</th>
                  <th>Unit</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody id="items-table-body">
                @foreach ($result->detail as $index => $item)
                <tr id="item-row-{{ $index }}">
                    <td>
                      <input type="text" class="form-control w-150px w-lg-100" value="{{ $item->event->name }}" disabled>
                    </td>
                    <td>
                      <input type="text" class="form-control w-150px w-lg-100" value="{{ $item->item->name }}" disabled>
                    </td>
                    <td>
                        <input type="number" class="form-control w-150px w-lg-100" value="{{ $item->quantity }}" disabled>
                    </td>
                    <td>
                        <input type="number" class="form-control w-150px w-lg-100" value="{{ $item->unit_price }}" disabled>
                    </td>
                    <td>
                      <input type="text" class="form-control w-150px w-lg-100" value="{{ $item->unitMeasure->short_form }}" disabled>
                    </td>
                    <td>
                        <input type="number" class="form-control w-150px w-lg-100" value="{{ $item->total }}" disabled>
                    </td>
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
@endsection
