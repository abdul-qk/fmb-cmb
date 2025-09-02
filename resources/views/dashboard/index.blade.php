@extends('layout.master')
@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Toolbar-->
  <div class="toolbar" id="kt_toolbar">
    <!--begin::Container-->
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    
      <div data-kt-swapper-mode="prepend"  class="page-title d-flex align-items-center me-3 flex-wrap lh-1">
       
        <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">{{$title}}</h1>
        <span class="h-20px border-gray-200 border-start mx-4"></span>
        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
          <li class="breadcrumb-item text-muted">
            <a href="/dashboard" class="text-muted text-hover-primary">Home</a>
          </li>
          <li class="breadcrumb-item">
            <span class="bullet bg-gray-300 w-5px h-2px"></span>
          </li>
          <li class="breadcrumb-item text-muted">Dashboard</li>
        </ul>
        
      </div>
    </div>
    <!--end::Container-->
  </div>
  <!--end::Toolbar-->
  <!--begin::Post-->
  <div  id="kt_post">
    <!--begin::Container-->
     <div id="kt_content_container">
      <!--begin::Row-->
      <!-- <div class="row g-5 g-xl-10 m-0 mt-0">
        <div class="col-lg-12 col-xl-12 mb-5 mb-xl-0"> -->
          <div class="card h-md-100 border-0 shadow-none px-3">
            <h3 class="card-title align-items-start flex-column px-5 border-bottom-2 border-primary border-bottom py-3">
              <span class="card-label fw-bold text-gray-900">{{ \Carbon\Carbon::now()->format('F Y') }} Events</span>
              <!-- <span class="text-muted mt-1 fw-semibold fs-7">Total 424,567 deliveries</span>
              <span class="text-muted mt-1 fw-semibold fs-7">Total 424,567 deliveries</span> -->
            </h3>

            <div class="card-body pt-7 px-0 dashboard">
              <ul class="nav nav-stretch nav-pills nav-pills-custom nav-pills-active-custom d-flex justify-content-between mb-3 px-5" style="column-gap: 20px;row-gap: 5px;">
                @foreach ($totalDays as $day)
                <li class="nav-item p-0 ms-0 me-0">
                  <!--begin::Date-->
                  <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px py-4 px-3 btn-active-primary {{ $day['events'] != null  ? 'event-day' :'' }} {{ $day['day']  == \Carbon\Carbon::today()->format('d') ? 'active':''}}" data-bs-toggle="tab" href="#kt_timeline_widget_3_tab_content_{{$day['day']}}">
                    <span class="fs-7 fw-semibold">{{$day['weekday']}}</span>
                    <span class="fs-6 fw-bold">{{$day['day']}}</span>
                  </a>
                  <!--end::Date-->
                </li>
                @endforeach
              </ul>
              <hr class="border-primary border-2 opacity-100" />
              <div class="tab-content px-9">
                @foreach ($totalDays as $day)
                <div class="tab-pane fade {{ $day['day']  == \Carbon\Carbon::today()->format('d') ? 'active show':''}}" id="kt_timeline_widget_3_tab_content_{{$day['day']}}">
                  <div class="row">
                    @if(isset($day['events']))
                    @if($day['events'] == null)
                    <div class="col-sm-12 text-center pt-4">
                      <h5 class="text-capitalize text-primary">No events.</h5>
                    </div>
                    @endif
                    @foreach ($day['events'] as $event)
                    <div class="col-sm-12 col-lg-6 mb-1  mb-3">
                      <div class="w-100 event-list shadow-sm p-3 h-100">
                        <div class="row">
                          <div class="col-sm-6 mb-3 mb-sm-0 mb border-end border-primary border-right-2 border-end-dotted">
                            <div class="d-flex align-items-center">
                              <!--begin::Bullet-->
                              <div class="flex-grow-1 me-5">
                                <div class="text-primary fw-semibold fs-2"> {{$event['name']}}</div>
                                <div class="text-gray-800 fw-semibold fs-6">
                                  <i class="bi bi-clock text-primary fs-fluid"></i>
                                  {{ \Carbon\Carbon::createFromFormat('H:i:s', $event['start'])->format('g:i') }}<span class="fw-semibold fs-7"> {{\Carbon\Carbon::createFromFormat('H:i:s', $event['start'])->format('A')}}</span> - {{ \Carbon\Carbon::createFromFormat('H:i:s', $event['end'])->format('g:i') }} <span class="fw-semibold fs-7">{{\Carbon\Carbon::createFromFormat('H:i:s', $event['end'])->format('A')}}</span>
                                </div>
                                <div class="text-gray-700 fw-bold fs-6 text-capitalize"><b><i class="fa-solid fa-utensils fs-fluid text-primary"></i> </b> {{$event['meal']}}</div>
                                <div class="text-gray-700 fw-bold fs-6 text-capitalize"><b>
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="13" height="13" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                                      <g>
                                        <path d="M288.183 32.134C288.183 14.415 273.768 0 256.05 0c-42.57 1.616-42.559 62.658 0 64.267 17.718 0 32.133-14.415 32.133-32.133zM357.713 303.134l-76.227 60.979a14.998 14.998 0 0 1-9.37 3.287H160.073c-8.077 0-15.027-6.207-15.406-14.275-.406-8.614 6.458-15.725 14.983-15.725h63.2c18.488.075 33.367-15.826 32.055-34.267H90.227c-14.776 15.619-24.199 36.188-25.7 58.908L4.443 422.127c-5.858 5.857-5.858 15.355 0 21.213l64.267 64.267c5.86 5.86 15.356 5.856 21.213 0l60.546-60.546 139.37-15.485a15 15 0 0 0 7.714-3.195l110.487-88.388c11.099-8.873 16.119-23.195 13.321-36.858h-63.648zM256.05 64.267c-97.144 0-180.021 62.204-210.929 148.867h421.857C436.071 126.47 353.194 64.267 256.05 64.267zM497.05 243.134h-482c-8.284 0-15 6.716-15 15s6.716 15 15 15h482c19.723-.665 20.01-29.217 0-30z" fill="#b98027" opacity="1" data-original="#b98027" class=""></path>
                                      </g>
                                    </svg>

                                  </b> {{$event['serving']}}</div>
                              </div>
                            </div>
                            <div class="row">
                              @if(isset($event['no_of_thaal']))
                              <div class="col-sm-12 mt-3">
                                <strong class="text-capitalize text-gray-700">Thaal:</strong> 8 Persons <br>
                                <strong class="text-capitalize text-gray-700">No of Thaal:</strong> {{$event['no_of_thaal']}}
                              </div>
                              @endif
                              @if(isset($event['serving_items']))
                              @foreach ($event['serving_items'] as $serving_item)
                              <div class="col-sm-12 mt-3">
                                <strong class="text-capitalize text-gray-700">{{$serving_item['tiffin_size']['name']}} Tiffin:</strong> {{ $serving_item['tiffin_size']['person_no'] }} Person{{$serving_item['tiffin_size']['person_no'] == 1 ? '' : 's'}} <br>
                                <strong class="text-capitalize text-gray-700">No of Tiffin:</strong> {{ $serving_item['count'] }}
                              </div>
                              @endforeach
                              @endif

                            </div>
                          </div>
                          <div class="col-sm-6">
                            
                              @if (isset($day['events']) && isset($event['menus'][0]['recipes']))
                                  <span class="text-primary fw-semibold fs-2">Menu</span>
                                  <br />
                                  @foreach ($event['menus'][0]['recipes'] as $recipe)
                                      <span class="text-gray-700 fw-bold fs-6 text-capitalize d-block">{{$recipe['dish']['name']}}</span>
                                      @if (!$loop->last)
                                          <div class="separator separator-dashed my-1"></div>
                                      @endif
                                  @endforeach
                              @endif
                          </div>
                        </div>
                      </div>

                    </div>
                    @endforeach
                    @endif
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </div>
        <!-- </div> -->

        <!-- <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
          <div class="card card-flush">

            <h3 class="card-title align-items-start flex-column px-5 border-bottom-2 border-primary border-bottom py-3">
              <span class="card-title text-gray-800 card-label fw-bold ">Menu</span>
            </h3>
            <div class="card-body pt-5">
              @ if($ dishNames == null)
              <div class="col-sm-12 text-center">
                <h5 class="text-capitalize text-primary">No Menu.</h5>
              </div>
              @ endif
              @ foreach ($dishNames as $count => $name)
              <div class="d-flex flex-stack">
                <div class="text-gray-700 fw-semibold fs-6 me-2 text-capitalize">{$name}</div>
              </div>
              @ if (!$loop->last)
              <div class="separator separator-dashed my-3"></div>
              @ endif
              @ endforeach
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">

          <div class="card card-flush">
            <h3 class="card-title align-items-start flex-column px-5 border-bottom-2 border-primary border-bottom py-3">
              <span class="card-title text-gray-800 card-label fw-bold ">Serving</span>
            </h3>
            <div class="card-body pt-5">
              @ if(!$totalThaal && $groupedServings->isEmpty())
              <div class="col-sm-12 text-center">
                <h5 class="text-capitalize text-primary">No Serving.</h5>
              </div>
              @ endif
              @ if($totalThaal)
              <div class="d-flex flex-stack">
                <div class="text-gray-700 fw-semibold fs-6 me-2 text-capitalize">Thaal</div>
                <div class="d-flex align-items-senter">
                  <span class="text-gray-900 fw-bolder fs-6">{$totalThaal}</span>
                </div>
              </div>
              @ if($groupedServings)
              <div class="separator separator-dashed my-3"></div>
              @ endif
              @ endif
              @ foreach ($groupedServings as $name=> $count)
              <div class="d-flex flex-stack">
                <div class="text-gray-700 fw-semibold fs-6 me-2 text-capitalize">$ name</div>
                <div class="d-flex align-items-senter">
                  <span class="text-gray-900 fw-bolder fs-6">$ count</span>
                </div>
              </div>

              @ if (!$loop->last)
              <div class="separator separator-dashed my-3"></div>
              @ endif
              @ endforeach
            </div>
          </div>
        </div> -->
      <!-- </div> -->
    </div>
  </div>
</div>
@endsection