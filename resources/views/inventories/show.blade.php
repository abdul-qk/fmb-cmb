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
      <div class="bg-transparent border-0 card shadow-none pt-2">
        <div class="row">
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Item</label>
           
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
            {{ $item->name }}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">UOM</label>
            <h5 class="border-primary" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
            {{ $item->itemBase->baseUom->name }}
            </h5>
          </div>
        </div>
        <div class="table-responsive">
        
          <table class="table table-rounded table-bordered border gy-4 gs-4">
            <tr>
              <th>Event</th>
              <th>Kitchen</th>
              <th>Qty</th>
              <th>Action</th>
              <th>Performed By</th>
              <th>Performed Date</th>
            </tr>
            @foreach ($results['data'] as $index => $result)
            <tr>
              <td>{{ $result['event']['name'] ?? $result['detail']['event']['name'] ?? '-' }}</td>

              <td>{{ $result['kitchen']['floor_name'] ?? $result['detail']['kitchen']['floor_name'] ?? '-' }}</td>

              <td>{{ $result['quantity'] }}</td>

              <td class="text-capitalize">
                @if(($result['type'] ?? '') == 'transfer')
                  {{$result['type'] ?? ''}}
                @endif
                {{ $result['action'] ?? '-' }}
                 
                {!!isset($result['vendor']) ? "<br/>" . $result['vendor']["name"] :''!!}
                
                @if(($result['type'] ?? '') == 'transfer')
                <br/>
                <br/>
                <b>{{($result['action'] ?? '-') == 'issued' ? "From: ": "to: "  }}</b>{{$result['place_name']}}
                @endif
                
                {!! isset($result['reason']) ? '<br/>' . $result['reason'] : '' !!}




              </td>

              <td>
                <!-- {{ $result['created_by']['name'] ?? $result['detail']['created_by']['name'] ?? '-' }} <br /> -->
                {{ $result['received_by']['name'] ?? $result['detail']['received_by']['name'] ?? $result['created_by']['name'] ?? '-' }}
              </td>
              <td>
                {{ \Carbon\Carbon::parse($result['created_at'])->isoFormat('Do MMM YYYY') }} <br />
                {{ \Carbon\Carbon::parse($result['created_at'])->isoFormat('hh:mm A') }}
              </td>


             
            </tr>
            @endforeach
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection