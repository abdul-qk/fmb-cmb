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
  <div id="kt_post">
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

        <div class="row text-capitalize">
          <div class="col-12 my-3">
            <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Event Details</h2>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Event Place</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{$result->event->place->name}}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Event Name</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{$result->event->name}}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Event Date</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{\Carbon\Carbon::parse($result->event->date)->isoFormat('Do MMM YYYY')}}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Event Time</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{\Carbon\Carbon::parse($result->event->start)->isoFormat('hh:mm A')}} -
              {{\Carbon\Carbon::parse($result->event->end)->isoFormat('hh:mm A')}}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Meal </label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{$result->event->meal}}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">serving </label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{$result->event->serving}}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Serving Persons </label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
              {{$result->event->serving_persons}}
            </h5>
          </div>
          <form class="form" method="POST" action="{{ route('menus.approve.store', $result->id) }}">
            @csrf
            @method('PUT')
            <!-- @foreach($result->recipes as $recipe)
            <input hidden name="place_id" value="{{$result->event->place->id}}" />
            <input hidden name="event_id" value="{{$result->event->id}}" />
            <input hidden name="menu_id" value="{{$menu_id}}" />
            <div class="col-12 my-3">
              <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">{{$recipe->dish->name}} - {{$recipe->dish->dishCategory->name}}</h2>
            </div>
            <div class="table-responsive">
              <table class="table table-striped w-50 table-bordered table-secondary">
                @foreach($recipe->recipeItems as $index => $recipeItem)
                <tr class="col-md-3 col-lg-3 mb-5">
                  <td style="vertical-align: middle;">
                    {{$recipeItem->item->name}}
                    <input hidden type="number" value="{{$recipeItem->item->id}}" class="form-control quantity-input" name="items[{{$index}}][item_id]">
                  </td>
                  <td>
                    <input hidden type="number" step="0.001" value="{{round( ( $result->event->serving_persons / $recipe->serving) * $recipeItem->item_quantity,1)}}" class="form-control quantity-input" name="items[{{$index}}][current_quantity]">
                    <input type="number" step="0.001" max="{{round( ( $result->event->serving_persons / $recipe->serving) * $recipeItem->item_quantity,1)}}" value="{{round( ( $result->event->serving_persons / $recipe->serving) * $recipeItem->item_quantity,1)}}" class="form-control quantity-input w-150px w-lg-100" name="items[{{$index}}][quantity]">
                  </td>
                  <td style="vertical-align: middle;">
                    {{$recipeItem->measurement->name}}
                    <input hidden type="number" value="{{$recipeItem->measurement->id}}" class="form-control quantity-input" name="items[{{$index}}][unit_id]">
                  </td>
                </tr>
                @endforeach
              </table>
            </div>
            @endforeach -->
            @php $recipeIndex = 1; @endphp
            @foreach($result->recipes as $recipe)
            <input hidden name="place_id" value="{{$result->event->place->id}}" />
            <input hidden name="event_id" value="{{$result->event->id}}" />
            <input hidden name="menu_id" value="{{$menu_id}}" />
            <div class="col-12 my-3">
              <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">{{$recipe->dish->name}} - {{$recipe->dish->dishCategory->name}}</h2>
            </div>
            <div class="table-responsive">
              <table class="table table-striped w-50 table-bordered table-secondary">
                @foreach($recipe->recipeItems as $index => $recipeItem)
                <tr class="col-md-3 col-lg-3 mb-5">
                  <td style="vertical-align: middle;">
                    {{$recipeItem->item->name}} 
                    <input hidden type="number" value="{{$recipeItem->item->id}}" class="form-control quantity-input" name="items[{{$recipeIndex}}][item_id]">
                    <input hidden type="number" value="{{$recipe->id}}" class="form-control quantity-input" name="items[{{$recipeIndex}}][recipe_id]">
                  </td>
                  <td>
                    <input disabled  type="number" step="0.001" max="{{round( ( $result->event->serving_persons / $recipe->serving) * $recipeItem->item_quantity,1)}}" value="{{round( ( $result->event->serving_persons / $recipe->serving) * $recipeItem->item_quantity,1)}}" class="form-control quantity-input w-150px w-lg-100">
                  </td>
                  <td>
                    <input hidden type="number" step="0.001" value="{{round( ( $result->event->serving_persons / $recipe->serving) * $recipeItem->item_quantity,1)}}" class="form-control quantity-input" name="items[{{$recipeIndex}}][current_quantity]">
                    <input type="number" step="0.001" max="{{round( ( $result->event->serving_persons / $recipe->serving) * $recipeItem->item_quantity,1)}}" value="{{round( ( $result->event->serving_persons / $recipe->serving) * $recipeItem->item_quantity,1)}}" class="form-control quantity-input w-150px w-lg-100" name="items[{{$recipeIndex}}][quantity]">
                  </td>
                  <td style="vertical-align: middle;">
                    {{$recipeItem->measurement->name}}
                    <input hidden type="number" value="{{$recipeItem->measurement->id}}" class="form-control quantity-input" name="items[{{$recipeIndex}}][unit_id]">
                  </td>
                </tr>
                @php $recipeIndex++; @endphp
                @endforeach
              </table>
            </div>
            @php $recipeIndex++; @endphp
            @endforeach
            <div class="col-md-12 mt-3">
              <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2" style="margin-right: 5px">
              
              <button type="submit" class="btn btn-primary hover-elevate-up w-100px submit-button">
                <span class="spinner-border spinner-border-custom d-none" role="status" aria-hidden="true"></span>
                <span class="button-text"> Approve </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
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