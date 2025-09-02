@push('styles')
<style>
  th,
  td {
    width: 16.6%;
  }
  .doc-file .position-absolute {
    opacity: 0;
    transition: 300ms all;
  }

  .doc-file:hover .position-absolute {
    opacity: 1;
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
      <div class="bg-transparent border-0 card shadow-none pt-2">
       
        <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">#{{$result->id}}</h2>
        <div class="row">
          <div class="col-md-2 col-lg-2 mb-5">
            <label class="form-label">Vendor</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{ $result->vendor->name }}
            </h5>
          </div>
          <div class="col-md-2 col-lg-2 mb-5">
            <label class="form-label">Store</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
            @php
              $selectedStore = $stores->firstWhere('id', $result->store_id);
            @endphp

            @if ($selectedStore)
              {{ $selectedStore->place->name }} - {{ $selectedStore->floor_name }}
            @endif
            </h5>
          </div>
          <div class="col-md-2 col-lg-2 mb-5">
            <label class="form-label">Currency</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
            @php
              $selectedCurrency = $currencies->firstWhere('id', $result->currency->id);
            @endphp

            @if ($selectedCurrency)
              {{ $selectedCurrency->short_form }}
            @endif
            </h5>
          </div>
          <div class="col-md-2 col-lg-2 mb-5">
            <label class="form-label">GRN Date</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
            {{ \Carbon\Carbon::parse($result->grn_date)->isoFormat('Do MMM YYYY') }}
            </h5>
          </div>
          <div class="col-md-2 col-lg-2 mb-5">
            <label class="form-label">Bill No</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
            {{ $result->bill_no }}
            </h5>
          </div>
          
          @if(!empty($result->imageData))
          <div class="col-md-2 col-lg-2 mb-5">
            <label class="form-label">Bill</label>

            <div style=" word-break: break-word;height: 43px; display: flex; align-items: center;font-size: 1.1rem;">
            
              @foreach ($result->imageData as $image)
                @if (in_array(strtolower($image['type']), ['pdf', 'doc', 'docx']))
                <span class="doc-file position-relative">
                  <i onclick="deleteDoc('upload-bill', '{{ $image['user_id'] . '=' . $image['image'] }}')" title="Delete" style="background: white;filter: drop-shadow(2px 4px 6px #00000078);padding: 2px;top: -5px;right: 6px;border-radius: 50px;font-size: 11px;" class="cursor-pointer text-danger bi bi-trash position-absolute"></i>
                    <a title="Image" target="_blank" href="{{ $image['url'] }}" class="me-4">
                      <i class="fa-regular fa-file fs-1 text-primary"></i>
                    </a>
                </span>
                @else
                <span class="doc-file position-relative">
                  <i onclick="deleteDoc('upload-bill', '{{ $image['user_id'] . '=' . $image['image'] }}')" title="Delete" style="background: white;filter: drop-shadow(2px 4px 6px #00000078);padding: 2px;top: -5px;right: 6px;border-radius: 50px;font-size: 11px;" class="cursor-pointer text-danger bi bi-trash position-absolute"></i>
                  <a title="Image" target="_blank" href="{{ $image['url'] }}" class="me-4">
                    <i class="fa-regular fa-image fs-1 text-primary"></i>
                  </a>
                </span>
                @endif
              @endforeach
              
            </div>
          </div>
          @endif
          <form class="form" method="POST" action="{{ route('goods-received-notes.upload', $result->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" class="form-control" name="purchase_id" value="{{$result->id}}">
            <div class="col-md-4 mb-3">
              <div class="d-flex" style="gap: 10px;">
                <input type="file" class="form-control w-75" name="upload_bill[]" multiple id="uploadBill" >
                <input type="submit" id="submitBtn" disabled value="Upload" class="w-25 btn submit-button btn-primary hover-elevate-up">
              </div>
            </div>
          </form>
        </div>
        <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Filter</h2>
        <div class="row">
          <div class="col-md-3 col-lg-2 mb-5">
            <label class="form-label required">Item Category</label>
            <select class="form-select" id="item_category" name="item_category" data-control="select2" data-close-on-select="false" data-placeholder="Select in item category filter" data-allow-clear="true">
              <option value=""></option>
              @foreach($itemCategories as $itemCategory)
              <option value="{{ $itemCategory->id }}" {{ old('item_category') == $itemCategory->id ? 'selected' : '' }}>{{ $itemCategory->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div id="items-section" style="display: block;">
          <hr />
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Qty</th>
                  <th>Unit</th>
                  <th>Per Unit Price</th>
                  <th>Discount</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody id="items-table-body">
                @foreach ($result->detail->reverse() as $index => $item)
                <tr id="item-row-{{ $index }}" class="item-category-{{$item->item->category_id}}">
                  <td>{{ $item->item->name }}</td>
                  <td>{{ $item->quantity }}</td>
                  <td>{{ $item->unitMeasure->short_form }}

                  </td>
                  <td>{{ $item->unit_price }}</td>
                  <td>
                    <div class="d-flex w-100">
                      {{ $item->per_item_discount }}
                      ({{ $item->discount_option == 'v' ? 'v' : '%' }})
                    </div>
                  </td>
                  <td>{{ $item->total }}</td>
                </tr>
                @endforeach
                <tr id="not-found" style="display: none;">
                  <td class="fw-bold text-center" colspan="7"> Not Found</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Amount</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
            {{ $result->discount + $result->amount }}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Discount</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
            {{ $result->discount }}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Total Amount</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
            {{ $result->amount }}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Description</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
            {{ $result->description }}
            </h5>
          </div>
        </div>

        <div class="row">
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Created By: <b class="text-primary"> {{$result->createdBy ? $result->createdBy->name : '-'}}</b> </label>
            
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{ $result->created_at->isoFormat('Do MMM YYYY') }}  {{ $result->created_at->isoFormat('hh:mm A') }}
            </h5>
          </div>
          @if(isset($result->updatedBy))
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Last Modified: <b class="text-primary"> {{$result->updatedBy ? $result->updatedBy->name : '-'}}</b> </label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{ $result->updated_at ? $result->updated_at->isoFormat('Do MMM YYYY') : '' }} {{ $result->updated_at ? $result->updated_at->isoFormat('hh:mm A') : '' }}
            </h5>
            
          </div>
          @endif
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
    $('#uploadBill').on('change', function () {
        const hasFiles = $(this).get(0).files.length > 0;
        if (hasFiles) {
            $('#submitBtn').prop('disabled', false);
        } else {
            $('#submitBtn').prop('disabled', true);
        }
    });
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
  function deleteDoc(path, docId) {
    console.log({path, docId})
    // Display a confirmation prompt
    const confirmation = confirm("Are you sure you want to delete this document?");

    if (confirmation) {
      let data = {
        path,
        docId,
      };
      $.get("/fetch-file-delete", data, function(response) {
        if (response.message == "Deleted successfully.") {
          window.location.reload();
        }
      });
    }
  }
</script>
@endpush