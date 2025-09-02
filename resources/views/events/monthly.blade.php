@php
function getOrdinalSuffix($i) {
if (!in_array(($i % 100), [11, 12, 13])) {
switch ($i % 10) {
case 1: return 'st';
case 2: return 'nd';
case 3: return 'rd';
}
}
return 'th';
}
@endphp
@extends('layout.master')
@push('styles')
<style>

  .table-responsive {
    position: relative;
  }
  .table-responsive.active::before {
    content: "";
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #00000033;
    position: absolute;
    z-index: 3;
  }
  .table-responsive.active .main-loader {
    position: absolute;
    top: 40px;
    left: 50%;
    width: 100px;
    height: 100px;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 3;
  }
 

  .table-responsive.active .main-loader .loader {
    --d: 22px;
    width: 4px;
    height: 4px;
    border-radius: 50%;
    color: var(--bs-primary);
    box-shadow:
      calc(1*var(--d)) calc(0*var(--d)) 0 0,
      calc(0.707*var(--d)) calc(0.707*var(--d)) 0 1px,
      calc(0*var(--d)) calc(1*var(--d)) 0 2px,
      calc(-0.707*var(--d)) calc(0.707*var(--d)) 0 3px,
      calc(-1*var(--d)) calc(0*var(--d)) 0 4px,
      calc(-0.707*var(--d)) calc(-0.707*var(--d))0 5px,
      calc(0*var(--d)) calc(-1*var(--d)) 0 6px;
    animation: l27 1s infinite steps(8);
  }

  .table-responsive th:nth-child(3),
  .table-responsive td:nth-child(3),
  .table-responsive th:nth-child(4),
  .table-responsive td:nth-child(4) {
    width: 40%;
    max-width: 40%;
  }

  @keyframes l27 {
    100% {
      transform: rotate(1turn)
    }
  }
</style>
@endpush
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
  <div id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid">
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
        <form class="form" method="POST" action="{{ route('events.monthly.store') }}">
          <div class="col-md-4 col-lg-3 my-5">
            <input type="month" class="form-control month-year" name="month_year"
              value="{{ old('month_year', \Carbon\Carbon::now()->format('Y-m')) }}">

          </div>
          <div class="position-relative table-responsive active">
            <table id="kt_datatable" class="table table-rounded table-bordered border gy-2 gs-2">

              <tr>
                <th style="vertical-align: middle;width:75px !important;max-width:75px!important;text-align:center;padding: 7px 15px !important;" >
                  <input
                    class="form-check-input all border border-1 border-white my-2"
                    type="checkbox"
                    name="all"
                    value="all"
                    {{ old('all') == 'all' ? 'checked' : '' }}
                    id="all">
                </th>
                <th style="vertical-align: middle;width:150px;max-width:150px"><b>Date</b></th>
                <th style="vertical-align: middle;"><b>Serving</b></th>
                <th style="vertical-align: middle;"><b>Event Name</b></th>
                <!-- <th><b>Description</b></th> -->
              </tr>
              @csrf
              <tbody class="table-body">
              
                <div class="main-loader">
                  <div class="loader"></div>
                </div>
                @foreach ($results as $i => $result)
                <tr>
                  <td style="vertical-align: middle;width:75px !important;max-width:75px!important;text-align:center;">
                    <input
                      class="form-check-input other-check"
                      type="checkbox"
                      name="items[{{ $i }}][check]"
                      value="on"
                      {{ old('items.' . $i . '.check') == 'on' ? 'checked' : '' }}
                      id="check_{{ $i }}">
                  </td>
                  <td style="vertical-align: middle;">
                    {{$result['date']}}{{ getOrdinalSuffix($result['date']) }} {{$currentMonth}}
                    <input hidden type="text" class="form-control" name="items[{{ $i }}][date]" id="date_{{ $i }}" value="{{ old('items.$i.date', $result['full_date']) }}" disabled>
                    @if ($errors->has("items.$i.date"))
                    <div class="alert alert-danger">
                      {{ $i + 1 }}{{ getOrdinalSuffix($result['date']) }} {{$currentMonth}} {{ $errors->first("items.$i.date") }}
                    </div>
                    @endif
                  </td>
                  <td style="vertical-align: middle;">
                    <select disabled class="form-select serving" id="serving_{{ $i }}" name="items[{{ $i }}][serving]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                      <option value=""></option>
                      @if(isset($result['serving_tiffin_id']))
                      <option value="tiffin" {{ old('items.' . $i . '.serving', isset($result['serving_tiffin_id']) ? 'tiffin' : '') == 'tiffin' ? 'selected' : '' }}>Tiffin</option>
                      @endif
                      @if(isset($result['serving_thaal_id']))
                      <option value="thaal" {{ old('items.' . $i . '.serving') == 'thaal' ? 'selected' : '' }}>Thaal</option>
                      @endif
                    </select>

                    @if(isset($result['serving_thaal_id']))
                    <input hidden disabled type="text" class="form-control thaal_id" name="items[{{ $i }}][thaal_id]" id="thaal_id_{{ $i }}" value="{{ old('items.$i.thaal_id', $result['serving_thaal_id']) }}">
                    @endif

                    @if(isset($result['serving_tiffin_id']))

                    <input hidden disabled type="text" class="form-control tiffin_id" name="items[{{ $i }}][tiffin_id]" id="tiffin_id_{{ $i }}" value="{{ old('items.$i.tiffin_id', $result['serving_tiffin_id']) }}">

                    <input hidden disabled type="text" class="form-control item_id" name="items[{{ $i }}][item_id]" id="item_id_{{ $i }}" value="{{ old("items.$i.item_id", implode(',', $result['item_id'])) }}">
                    @endif

                    @if ($errors->has("items.$i.serving"))
                    <span class="text-danger">{{ $errors->first("items.$i.serving") }}</span>
                    @endif
                  </td>
                  <td style="vertical-align: middle;">
                    <input disabled type="text" class="form-control event-name-{{ $i }} w-150px w-lg-100" name="items[{{ $i }}][name]" value="{{ old("items.$i.name", isset($result['serving_tiffin_id']) ? "Daily FMB - {$result['date']}" . getOrdinalSuffix($result['date']) . " {$currentMonth} {$currentYear}" : '') }}">
                    <span class="inner-text d-none"></span>
                    @if ($errors->has('items.' . $i . '.name'))
                    <span class="text-danger">{{ $errors->first('items.' . $i . '.name') }}</span>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="col-md-12 mt-3">
            <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2" style="margin-right: 5px">
            
            <button type="submit" class="btn btn-primary hover-elevate-up w-100px submit-button">
                <span class="spinner-border spinner-border-custom d-none" role="status" aria-hidden="true"></span>
                <span class="button-text"> Create </span>
              </button>
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
<script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
  $(document).ready(function() {
    $('.table-responsive').removeClass('active')
    $(document).on('change', '.form-check-input', function() {

      let tr = $(this).closest('tr');
      if ($(this).is(':checked')) {
        console.log(tr.find('.form-select').val())
        tr.find('.form-control, .form-select').removeAttr('disabled');

        if (tr.find('.form-select').val() == "tiffin") {
          console.log(tr.find('.thaal_id'))
          tr.find('.thaal_id').prop("disabled", true);
          tr.find('.tiffin_id').prop("disabled", false);
          tr.find('.item_id').prop("disabled", false);
        } else if (tr.find('.form-select').val() == "thaal") {
          tr.find('.thaal_id').prop("disabled", false);
          tr.find('.tiffin_id').prop("disabled", true);
          tr.find('.item_id').prop("disabled", true);
        }

      } else {
        tr.find('.form-control, .form-select').attr('disabled', true);
      }
    });
    $(document).on("change", ".serving", function() {
      let tr = $(this).closest("tr");
      let id = $(this).prop("id").split("_")[1];
      let eventName = $(`.event-name-${id}`).val();
      if ($(this).val() == "tiffin") {
        tr.find('.thaal_id').prop("disabled", true);
        tr.find('.tiffin_id').prop("disabled", false);
        tr.find('.item_id').prop("disabled", false);
        let text = tr.find('.inner-text').text()
        $(`.event-name-${id}`).val(text)
      } else if ($(this).val() == "thaal") {
        tr.find('.thaal_id').prop("disabled", false);
        tr.find('.tiffin_id').prop("disabled", true);
        tr.find('.item_id').prop("disabled", true);
        tr.find('.inner-text').text(eventName);
        $(`.event-name-${id}`).val("")
      }
    });
    let anyError = <?php echo json_encode($errors->any()); ?>;
    console.log(<?php echo json_encode($errors->all()); ?>)
    if (anyError) {
      $('.month-year, .form-check-input').trigger("change");
    }
    $(document).on('change', '.all', function() {
      if ($(this).is(':checked')) {
        $(".other-check").prop("checked", true)
      } else {
        $(".other-check").prop("checked", false)
      }
      $('.other-check').trigger("change");
    });


    $('.month-year').on('input', function() {
      let value = $(this).val(); // "2028-06"
      let [year, month] = value.split('-');
      let data = {
        value,
        month,
        year,
      };
      $('.table-responsive').addClass('active')
      $.get("/fetch-monthly-events-year", data, function(response) {
        let records = ``;
        response.forEach((item, index) => {
          records += `
                    <tr>
                      <td style="vertical-align: middle;width:75px !important;max-width:75px!important;text-align:center;">
                        <input
                          class="form-check-input other-check"
                          type="checkbox"
                          name="items[${index}][check]"
                          value="on"
                          id="check_${index}">
                      </td>
                      <td style="vertical-align: middle;">
                        ${item?.date}${item?.term} ${item?.month}
                        <input hidden type="text" class="form-control" name="items[${index}][date]" id="date_${index}" value=" ${item?.full_date}" disabled>
                        
                      </td>
                      <td style="vertical-align: middle;">
                        <select disabled class="form-select serving" id="serving_${index}" name="items[${index}][serving]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                          <option value=""></option>
                          ${item?.serving_tiffin_id ? 
                          `<option value="tiffin" selected>Tiffin</option>`
                          : ''}

                          ${item?.serving_thaal_id ? 
                          `<option value="thaal">Thaal</option>`
                          : ''}
                        </select>
    
                        ${item?.serving_thaal_id ? 
                        `<input hidden disabled type="text" class="form-control thaal_id" name="items[${index}][thaal_id]" id="thaal_id_${index}" value="${item?.serving_thaal_id}">`
                        :''}
                       
                        ${item?.serving_tiffin_id ? `
                        <input hidden disabled type="text" class="form-control tiffin_id" name="items[${index}][tiffin_id]" id="tiffin_id_${index}" value="${item?.serving_tiffin_id}">
                        <input hidden disabled type="text" class="form-control item_id" name="items[${index}][item_id]" id="item_id_${index}" value="${item?.item_id?.join(',') || ''}">
                        `: ""}
                      </td>
                      <td style="vertical-align: middle;">
                        <input disabled type="text" class="form-control event-name-${index} w-150px w-lg-100" name="items[${index}][name]" value="${item?.serving_tiffin_id ? `Daily FMB - ${item?.date}${item?.term} ${item?.month}`:""}">
                        <span class="inner-text d-none"></span>
                      </td>
                    </tr>`;
        })
        $('.table-body').html("");
        $('.table-body').append(records);
        $(`[data-control="select2"]`).select2();
        $('.table-responsive').removeClass('active')
      })
    });

    $('form').on('submit', function () {
      const button = $('.submit-button');
      $('.button-text').hide()
      button.prop('disabled', true);
      button.find('.spinner-border').removeClass('d-none'); // show spinner
    });
  });
</script>
@endpush