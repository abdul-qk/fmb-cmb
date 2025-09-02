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
            <div class="col-12 mb-3">
              <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Vendor Detail</h2>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Name</label>
              <input type="text" class="form-control" name="name" id="name" value="{{old('name',$result->name)}}">
              @if ($errors->has('name'))
              <span class="text-danger">{{ $errors->first('name') }}</span>
              @endif
            </div>
           
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">City</label>
              <select class="form-select " id="city_id" name="city_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($cities as $city)
                <option value="{{ $city->id }}" {{ old('city_id',$result->city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('city_id'))
              <span class="text-danger">{{ $errors->first('city_id') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Address</label>
              <textarea class="form-control" name="address" id="address">{{old('address', $result->address)}}</textarea>
              @if ($errors->has('address'))
              <span class="text-danger">{{ $errors->first('address') }}</span>
              @endif
            </div>
            <div class="col-12 mb-3">
              <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Contact Detail</h2>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Email</label>
              <input type="text" class="form-control" name="email" id="email" value="{{old('email',$vendorContact?->email)}}">
              @if ($errors->has('email'))
              <span class="text-danger">{{ $errors->first('email') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Contact Number</label>
              <input type="text" class="form-control" hidden name="contact_id" id="contact_id" value="{{old('contact_id', $vendorContact?->id )}}">
              <input type="text" class="form-control" name="contact_number" id="contact_number" value="{{old('contact_number', $vendorContact?->contact_number )}}">
              @if ($errors->has('contact_number'))
              <span class="text-danger">{{ $errors->first('contact_number') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Office Number</label>
              <input type="text" class="form-control" name="office_number" id="office_number" value="{{old('office_number',$vendorContact?->office_number)}}">
              @if ($errors->has('office_number'))
              <span class="text-danger">{{ $errors->first('office_number') }}</span>
              @endif
            </div>
            <div class="col-12 mb-3">
              <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Bank Detail</h2>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Bank Title</label>
              <input type="text" class="form-control" name="bank_title" id="bank_title" value="{{old('bank_title',$vendorBank?->bank_title)}}">
              @if ($errors->has('bank_title'))
              <span class="text-danger">{{ $errors->first('bank_title') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Account No</label>
              <input type="text" class="form-control" name="account_no" id="account_no" value="{{old('account_no',$vendorBank?->account_no)}}">
              @if ($errors->has('account_no'))
              <span class="text-danger">{{ $errors->first('account_no') }}</span>
              @endif
            </div>
            
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Bank</label>
              <input type="text" class="form-control" name="bank" id="bank" value="{{old('bank',$vendorBank?->bank)}}">
              @if ($errors->has('bank'))
              <span class="text-danger">{{ $errors->first('bank') }}</span>
              @endif
            </div>
            
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Bank Branch</label>
              <input type="text" class="form-control" name="bank_branch" id="bank_branch" value="{{old('bank_branch',$vendorBank?->bank_branch)}}">
              @if ($errors->has('bank_branch'))
              <span class="text-danger">{{ $errors->first('bank_branch') }}</span>
              @endif
            </div>
           
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Bank Address</label>
              <textarea class="form-control" name="bank_address" id="bank_address">{{old('bank_address', $vendorBank?->bank_address)}}</textarea>
              @if ($errors->has('bank_address'))
              <span class="text-danger">{{ $errors->first('bank_address') }}</span>
              @endif
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">NTN</label>
              <input hidden type="text" class="form-control" name="bank_id" id="bank_id" value="{{old('ntn',$vendorBank?->id)}}">
              <input type="text" class="form-control" name="ntn" id="ntn" value="{{old('ntn',$vendorBank?->ntn)}}">
              @if ($errors->has('ntn'))
              <span class="text-danger">{{ $errors->first('ntn') }}</span>
              @endif
            </div>
            <div class="col-12 mb-3">
              <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Vendor Items</h2>
            </div>
            <div class="col-md-12 mb-5">
              <label class="form-label">Items</label>
              @php
                $oldSelectedItems = old('items', $selectedItems ?? []);
              @endphp

              <select class="form-select" id="items" name="items[]" data-control="select2" multiple="multiple" data-close-on-select="false" data-placeholder="Select items" data-allow-clear="true">
                @foreach($items as $item)
                  <option value="{{ $item->id }}" {{ in_array($item->id, $oldSelectedItems) ? 'selected' : '' }}>
                    {{ $item->name }}
                  </option>
                @endforeach
              </select>
              @if ($errors->has('items'))
                  <span class="text-danger">{{ $errors->first('items') }}</span>
              @endif
            </div>
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