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
      <div class="bg-transparent border-0 card shadow-none pt-2">
        <form class="form" method="POST" action="{{ route($update, $result->id) }}">
          @csrf
          @method('PUT')
          <div id="items-section" style="display: block;">
            <div class="row">
                <div class="col-md-3 col-lg-3 mb-5">
                    <label class="form-label">Item</label>
                    <input type="text" class="form-control" value="{{ $result->item->name }}" disabled />
                    <input type="hidden" class="form-control" name="item_id" value="{{ $result->item->id }}" />
                </div>
                <div class="col-md-3 col-lg-3 mb-5">
                    <label class="form-label">UOM</label>
                    <input type="text" class="form-control" value="{{ $result->item->itemBase->baseUom->name }}" disabled />
                </div>
                @if(isset($result->return))
                <div class="col-md-3 col-lg-3 mb-5">
                  <label class="form-label required">Return By </label>
                  <select class="form-select" id="return_by" name="return_by" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option value=""></option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('return_by', $result->return->return_by) == $user->id ? 'selected' : '' }}>{{$user->name}}</option>
                    @endforeach
                  </select>
                  @if ($errors->has('return_by'))
                    <span class="text-danger">{{ $errors->first('return_by') }}</span>
                  @endif
                </div>
                @else
                <div class="col-md-3 col-lg-3 mb-5">
                  <label class="form-label required">Return By</label>
                  <select class="form-select" id="return_by" name="return_by" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option value=""></option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('return_by') == $user->id ? 'selected' : '' }}>{{$user->name}}</option>
                    @endforeach
                    <option value="0">Other</option>
                  </select>
                  @if ($errors->has('return_by'))
                    <span class="text-danger">{{ $errors->first('return_by') }}</span>
                  @endif
                </div>
                <div class="col-md-3 col-lg-3 mb-5 other-option" style="display: none;">
                  <label class="form-label required">Name</label>
                  <input disabled  type="text" class="form-control" name="worker_name"  value="{{ old('worker_name') }}">
                </div>
                @endif
            </div>
            <hr />
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                        <th>Event</th>
                        <th>Place</th>
                        <th>Received By</th>
                        <th>Issued Qty</th>
                        <th>Return Qty</th>
                        <!-- <th>Reason</th> -->
                        </tr>
                    </thead>
                    <tbody id="items-table-body">
                        <input type="hidden" class="form-control" name="issued_quantity" value="{{ ($result->quantity - $result->returns->sum('quantity')) }}">
                        <tr>
                            <td>
                                <input type="text" class="form-control w-150px w-lg-100" value="{{ $result->event->name }}" disabled>
                            </td>
                            <td>
                                <input type="text" class="form-control w-150px w-lg-100" value="{{ $result->kitchen->floor_name }}" disabled>
                            </td>
                            <td>
                                <input type="text" class="form-control w-150px w-lg-100" value="{{ $result->receivedBy->name }}" disabled>
                            </td>
                            <td>
                                <input step="0.001" type="number" class="form-control w-150px w-lg-100" value="{{ ($result->quantity - $result->returns->sum('quantity')) }}" disabled>
                            </td>
                            <td>
                                <input type="number" step="0.001" class="form-control" name="returned_quantity" value="{{ old('returned_quantity', ($result->quantity - $result->returns->sum('quantity'))) }}">
                                @if ($errors->has('returned_quantity'))
                                    <span class="text-danger">{{ $errors->first('returned_quantity') }}</span>
                                @endif
                            </td>
                            <!-- <td class="d-none">
                                <input type="text" class="form-control w-150px w-lg-100" name="reason" value="{{ old('reason') }}">
                            </td> -->
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12 mt-3">
              <input type="reset" value="Reset" class="btn btn-dark w-100px mr-2 confirm-reset" style="margin-right: 5px">
              <input type="submit" value="Return" class="btn btn-primary hover-elevate-up w-100px confirm-submit">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script type="text/javascript">
    $(document).ready(function() { 
      $(document).ready(function() {
      $('.confirm-submit').on('click', function(e) {
      if (!confirm('Are you sure you want to proceed?')) {
        e.preventDefault(); // Stop form submission
      }
    });

        // Handle Reset confirmation
    $('.confirm-reset').on('click', function(e) {
      if (confirm('Are you sure you want to reset the above selection?')) {
        // Reload the page if user confirms
        location.reload();
      } else {
        e.preventDefault(); // Stay on the page if user cancels
      }
    });
      $('#return_by').on("change", function() {
        var selectedText = $(this).find(":selected").text().trim().toLowerCase(); // Trim to remove extra spaces
        if (selectedText === "other") {
          $('.other-option input').prop('required', true).prop('disabled', false);
          $('.other-option').show();
        } else {
          $('.other-option').hide();
          $('.other-option input').prop('required', false).prop('disabled', true);
        }
      });
    });
  </script>
@endpush