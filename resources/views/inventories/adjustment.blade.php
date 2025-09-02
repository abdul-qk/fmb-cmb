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
        <form class="form" method="POST" action="{{ route('inventories.store.adjustment') }}">
          @csrf
            <div class="row">
                <div id="items-section" style="display: block;">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                <th class="text-center">
                                    <input type="checkbox" class="form-check-input border border-1 border-white my-2" id="select-all" checked>
                                </th>
                                <th style="vertical-align: middle;">Item</th>
                                <th style="vertical-align: middle;">UOM</th>
                                <th style="vertical-align: middle;">Available Qty</th>
                                <th style="vertical-align: middle;">Adjust UOM</th>
                                <th style="vertical-align: middle;">Adjust Qty</th>
                                <th style="vertical-align: middle;">Reason</th>
                                </tr>
                            </thead>
                            <tbody id="items-table-body">
                                @foreach($results as $index => $item)
                                    @if(is_null(old('selected_items')) || in_array($item->id, old('selected_items', [])))
                                        <tr>
                                            <td style="vertical-align: middle;text-align: center;">
                                                <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" class="select-item form-check-input" checked>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control w-150px w-lg-100" value="{{ $item->name }}" disabled>
                                                <input type="text" hidden class="form-control w-150px w-lg-100 hidden-items" name="items[{{$item->id}}][item_id]" value="{{ $item->id }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control w-150px w-lg-100" value="{{ $item?->itemBase?->baseUom?->short_form }}" disabled>
                                                <input type="hidden" class="form-control w-150px w-lg-100 hidden-items" name="items[{{$item->id}}][base_unit_id]" value="{{ $item?->itemBase?->baseUom?->id }}">
                                            </td>
                                            <td>
                                                <input type="number" min="0" step="0.001" class="form-control w-150px w-lg-100" value="{{ $item->detail->available_quantity }}" disabled>
                                                <input type="hidden" step="0.001" class="form-control w-150px w-lg-100 hidden-items" name="items[{{$item->id}}][available_quantity]" value="{{ $item->detail->available_quantity }}">
                                            </td>
                                            <td>
                                                <select class="form-select hidden-items" name="items[{{$item->id}}][unit_id]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                                                    <option value=""></option>
                                                    @foreach($item->itemBase?->unitMeasure ?? [] as $unitMeasure)
                                                        <option value="{{ $unitMeasure->id }}" {{ old('items.' . $item->id . '.unit_id', $item?->itemBase?->baseUom?->id) == $unitMeasure->id ? 'selected' : '' }}>{{$unitMeasure->short_form}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input required type="number" min="0" step="0.001" class="form-control w-150px w-lg-100 quantity-input" name="items[{{$item->id}}][quantity]" value="{{ old('items.' . $item->id . '.quantity', $item->detail->available_quantity) }}">
                                                @if ($errors->has("quantity.{$item['id']}"))
                                                    <span class="text-danger">{{ $errors->first("quantity.{$item['id']}") }}</span>
                                                @endif
                                            </td>
                                            <td>
                                              <input type="text" class="form-control reason w-150px w-lg-100" name="items[{{$item->id}}][reason]" value="{{old('items.'.$item->id.'.reason')}}">
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-12 mt-3">
                    <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2  confirm-reset" style="margin-right: 5px">
                    
                    <button type="submit" class="btn btn-primary hover-elevate-up w-100px submit-button confirm-submit">
                      <span class="spinner-border spinner-border-custom d-none" role="status" aria-hidden="true"></span>
                      <span class="button-text"> Add </span>
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
  <script type="text/javascript">
   
    $(document).on('change', '#select-all', function() {
      const isChecked = $(this).is(':checked');
      $('.select-item').prop('checked', isChecked);
      $(".quantity-input").prop('disabled',!isChecked)
      $(".hidden-items").prop('disabled',!isChecked)
      $(".reason").prop('disabled',!isChecked)
    }); 

    $('form').on('submit', function () {
      const button = $('.submit-button');
      $('.button-text').hide()
      button.prop('disabled', true);
      button.find('.spinner-border').removeClass('d-none'); // show spinner
    });
    $(document).on('change', '.select-item', function() {
      const allChecked = $('.select-item:checked').length === $('.select-item').length;
      $('#select-all').prop('checked', allChecked);
      const isChecked = $(this).is(':checked');
      $(this).closest("tr").find(".hidden-items").prop('disabled',!isChecked);
      $(this).closest("tr").find(".quantity-input").prop('disabled',!isChecked);
      $(this).closest("tr").find(".reason").prop('disabled',!isChecked);
    });
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
  </script>
@endpush