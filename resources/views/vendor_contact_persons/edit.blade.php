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
        <form class="form" method="POST" action="{{ route($update, $result->id) }}">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Vendor</label>
              <select class="form-select " id="vendor_id" name="vendor_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($vendors as $vendor)
                <option value="{{ $vendor->id }}" {{ old('vendor_id',$result->vendor_id) == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('vendor_id'))
              <span class="text-danger">{{ $errors->first('vendor_id') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Name</label>
              <input type="text" class="form-control" name="name" id="name" value="{{old('name',$result->name)}}">
              @if ($errors->has('name'))
              <span class="text-danger">{{ $errors->first('name') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Email</label>
              <input type="text" class="form-control" name="email" id="email" value="{{old('email',$result->email)}}">
              @if ($errors->has('email'))
              <span class="text-danger">{{ $errors->first('email') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Contact Number</label>
              <input type="text" class="form-control" name="contact_number" id="contact_number" value="{{old('contact_number',$result->contact_number)}}" placeholder="With country code">
              @if ($errors->has('contact_number'))
              <span class="text-danger">{{ $errors->first('contact_number') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Office Number</label>
              <input type="text" class="form-control" name="office_number" id="office_number" value="{{old('office_number',$result->office_number)}}" placeholder="With country code">
              @if ($errors->has('office_number'))
              <span class="text-danger">{{ $errors->first('office_number') }}</span>
              @endif
            </div>
            @if(!$result->primary)
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Primary Bank</label>
              <div class="form-check form-switch form-check-custom form-check-primary form-check-dark">
              <input class="form-check-input rounded-4 h-30px w-50px" type="checkbox" 
                {{ old('primary', $result->primary) == 1 ? 'checked' : '' }}  
                name="primary" id="primary" value="1" />
                <label class="form-check-label" for="primary"></label>
              </div>
            </div>
            @endif
            <div class="col-md-12 mt-3">
              <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2" style="margin-right: 5px">
              <input type="submit" value="Update" class="btn btn-primary hover-elevate-up w-100px">
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