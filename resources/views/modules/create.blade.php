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
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Parent</label>
              <select class="form-select form-select-solid email-block" id="parent_id" name="parent_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($modules as $module)
                  <option value="{{ $module->id }}" {{ old('parent_id') == $module->id ? 'selected' : '' }}>{{ $module->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('parent_id'))
              <span class="text-danger">{{ $errors->first('parent_id') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Name</label>
              <input type="text" class="form-control" name="name" id="name" value="{{old('name')}}">
              @if ($errors->has('name'))
              <span class="text-danger">{{ $errors->first('name') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Icon</label>
              <input type="text" class="form-control" name="icon" id="icon" value="{{old('icon')}}">
              @if ($errors->has('icon'))
              <span class="text-danger">{{ $errors->first('icon') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Display Order</label>
              <input type="number" class="form-control" name="display_order" id="display_order" value="{{old('display_order', $moduleDisplayOrder)}}">
              @if ($errors->has('display_order'))
              <span class="text-danger">{{ $errors->first('display_order') }}</span>
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
  <script type="text/javascript">
  $(document).ready(function() {
    $('#parent_id').change(function() {
      var parent_id = $(this).val();
      $.ajax({
        type: 'GET',
        url: "{{ route('fetchModuleDisplayOrder') }}",
        data: { parent_id: parent_id },
        success: function(response) {
          $('#display_order').val(response);
        },
        error: function(xhr) {
          alert('Not able to fetch display order!');
        }
      });
    });
  });
  </script>
@endpush