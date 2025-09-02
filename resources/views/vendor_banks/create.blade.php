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
  <div  id="kt_post">
    <!--begin::Container-->
     <div class="container-fluid" id="kt_content_container">
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
      <div class="bg-transparent border-0 card shadow-none pt-2">
        <form class="form" method="POST" action="{{ route($store) }}">
          @csrf
          <div class="row">
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Vendor</label>
              <select class="form-select " id="vendor_id" name="vendor_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($vendors as $vendor)
                <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('vendor_id'))
              <span class="text-danger">{{ $errors->first('vendor_id') }}</span>
              @endif
            </div>

            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Title</label>
              <input type="text" class="form-control" name="bank_title" id="bank_title" value="{{old('bank_title')}}">
              @if ($errors->has('bank_title'))
              <span class="text-danger">{{ $errors->first('bank_title') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Account No</label>
              <input type="text" class="form-control" name="account_no" id="account_no" value="{{old('account_no')}}">
              @if ($errors->has('account_no'))
              <span class="text-danger">{{ $errors->first('account_no') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Bank</label>
              <input type="text" class="form-control" name="bank" id="bank" value="{{old('bank')}}">
              @if ($errors->has('bank'))
              <span class="text-danger">{{ $errors->first('bank') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Bank Branch</label>
              <input type="text" class="form-control" name="bank_branch" id="bank_branch" value="{{old('bank_branch')}}">
              @if ($errors->has('bank_branch'))
              <span class="text-danger">{{ $errors->first('bank_branch') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">NTN</label>
              <input type="text" class="form-control" name="ntn" id="ntn" value="{{old('ntn')}}">
              @if ($errors->has('ntn'))
              <span class="text-danger">{{ $errors->first('ntn') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Primary</label>
              <div class="form-check form-switch form-check-custom form-check-primary form-check-dark">
                <input class="form-check-input rounded-4 h-30px w-50px" type="checkbox" name="primary" id="primary" value="0" onchange="this.value = this.checked ? 1 : 0" />
                <label class="form-check-label" for="primary"></label>
              </div>
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Bank Address</label>
              <textarea type="text" class="form-control" name="bank_address" id="bank_address">{{old('bank_address')}}</textarea>
              @if ($errors->has('bank_address'))
              <span class="text-danger">{{ $errors->first('bank_address') }}</span>
              @endif
            </div>
            <div class="col-md-12 mt-3">
              <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2" style="margin-right: 5px">
              <input type="submit" value="Create" class="btn btn-primary hover-elevate-up w-100px">
            </div>
          </div>
        </form>
      </div>
      <!--end::Card-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
@endsection


@push('scripts')
<script>
  let anyError = <?php echo json_encode($errors->any()); ?>;
  console.log(<?php echo json_encode($errors->all()); ?>)

</script>
@endpush