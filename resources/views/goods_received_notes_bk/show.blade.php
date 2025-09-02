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
  <div  id="kt_post">
     <div class="container-fluid" id="kt_content_container">
      @if(Session::has('error'))
      <div class="alert alert-danger d-flex align-items-center p-5">
        <i class="ki-duotone ki-shield-tick fs-2hx text-danger me-4"></i>
        <div class="d-flex flex-column">
          <span>{{ Session::get('error') }}</span>
        </div>
      </div>
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
      <div class="bg-transparent border-0 card shadow-none pt-2">
        <div class="row">
          @if(isset($result->vendor->id))
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Vendor</label>
              <input type="text" class="form-control" value="{{ $result->vendor->id .' - '. $result->vendor->name }}" disabled>
            </div>    
          @endif
          <div class="col-md-3 col-lg-3 mb-5">
          </div>
        </div>
        <div id="items-section" style="display: block;">
          <div class="table-responsive">
            @foreach ($result->detail as $index => $item)
            <div class="mb-5" id="item-row-{{ $index }}">
              <div class="col-12">
                <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">{{$item->item->name}}  ({{ $item->approvedDetail->quantity }} {{$item->unitMeasure->short_form}}) </h2>
              </div>
              @if($item->approvedDetail->inventories)
              <div class="table-responsive mb-3">
                <table class="table table-rounded table-bordered border gy-7 gs-7">
                  <tr>
                    <th class="table-sort-asc">No</th>
                    <th class="">Store</th>
                    <th>Qty</th>
                    <th>Received</th>
                    <th>Remaining</th>
                    <th>Last Received</th>
                  </tr>
                  @php
                    $counter = 1;
                  @endphp
                  @foreach ($item->approvedDetail->inventories->reverse() as $inner_index => $inner_item)
                  <tr>
                    <td>{{$counter}}</td>
                    <td>{{$inner_item->store->place->name }} - {{$inner_item->store->floor == 0 ? "Ground" : "Floor ". $inner_item->store->floor}}</td>
                    <td>{{$inner_item->quantity + $inner_item->remaining}}</td>
                    <td>{{$inner_item->quantity}}</td>
                    <td>{{$inner_item->remaining}}</td>
                    <td>{{$inner_item->created_at->isoFormat('Do MMM YYYY, hh:mm A')}}</td>
                  </tr>
                  @php
                    $counter++;
                  @endphp
                  @endforeach
                </table>
              </div>
              @endif
            </div>
            @endforeach
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
  $(document).ready(function() {
    $(document).on("change", '.form-check-input', function() {
      const isChecked = $(this).is(':checked');
      const row = $(this).closest('tr');

      if (!isChecked) {
        row.find('.quantity-input, .input-hidden').prop('disabled', true);
      } else {
        row.find('.quantity-input, .input-hidden').prop('disabled', false);
      }
    });

  });

  let anyError = <?php echo json_encode($errors->any()); ?>;
  console.log(<?php echo json_encode($errors->all()); ?>)

</script>
@endpush