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
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-4 col-lg-3 mb-5">
                            <label class="form-label required">Name</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{old('name')}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-5">
                            @include('roles.add-permission')
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2" style="margin-right: 5px">
                        <input type="submit" value="Create" class="btn btn-primary hover-elevate-up w-100px">
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