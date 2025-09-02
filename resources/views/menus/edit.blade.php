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
        <form class="form" method="POST" action="{{ route($update, $menu->id) }}">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Event</label>
              <select class="form-select " name="event_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($events as $event)
                <option value="{{ $event->id }}" {{ old('event_id',$menu->event_id) == $event->id ? 'selected' : '' }}> {{ \Carbon\Carbon::parse($event->date)->isoFormat('Do MMM YYYY') }} - {{ $event->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('event_id'))
              <span class="text-danger">{{ $errors->first('event_id') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Dish</label>
              <select multiple class="form-select " name="dish_id[]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($results as $result)
                
                <option value="{{ $result->id }}" {{ old('dish_id', in_array($result->id, $selectedItems)) == $result->id  ? 'selected' : '' }}>{{ $result->dish->name .' - '. $result->chefUser->name }} - {{$result->serving}} pax</option>
                @endforeach
              </select>
              @if ($errors->has('dish_id'))
              <span class="text-danger">{{ $errors->first('dish_id') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Item Selections</label>
              <select class="form-select" name="item_quantity" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                <option value="recipe" {{ old('item_quantity',$menu->item_quantity) == 'recipe' ? 'selected' : '' }}>Recipe</option>
                <option value="chef-input" {{ old('item_quantity',$menu->item_quantity) == 'chef-input' ? 'selected' : '' }}>Chef Input</option>
              </select>
              @if ($errors->has('item_quantity'))
              <span class="text-danger">{{ $errors->first('item_quantity') }}</span>
              @endif
            </div>
            <div class="col-md-12 mt-3">
              <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2" style="margin-right: 5px">
              
              <button type="submit" class="btn btn-primary hover-elevate-up w-100px submit-button">
                <span class="spinner-border spinner-border-custom d-none" role="status" aria-hidden="true"></span>
                <span class="button-text"> Update </span>
              </button>
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
    $('form').on('submit', function () {
      const button = $('.submit-button');
      $('.button-text').hide()
      button.prop('disabled', true);
      button.find('.spinner-border').removeClass('d-none'); // show spinner
    });
  </script>
@endpush