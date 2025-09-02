<div id="kt_header" style="" class="header align-items-stretch">
    <!--begin::Container-->
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <!--begin::Aside mobile toggle-->
        <div class="d-flex align-items-center d-lg-none ms-n4 me-1" title="Show aside menu">
            <div class="btn btn-icon btn-active-color-white" id="kt_aside_mobile_toggle">
                <i class="ki-outline ki-burger-menu fs-1 text-white"></i>
            </div>
        </div>
        <!--end::Aside mobile toggle-->
        <!--begin::Mobile logo-->
        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
            <a href="/dashboard" class="d-lg-none bg-white p-1" style="border-radius: 4px;overflow:hidden;">
              <img alt="Logo" src="/assets/media/logos/logo.webp" class="h-25px" />
            </a>
        </div>
        <!--end::Mobile logo-->
        <!--begin::Wrapper-->
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
            <!--begin::Navbar-->
            <div class="d-flex align-items-stretch" id="kt_header_nav">
                <!--begin::Menu wrapper-->
                <div class="header-menu align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_header_menu_mobile_toggle" data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
                    <!--begin::Menu-->
                    <div class="menu menu-rounded menu-column menu-lg-row menu-root-here-bg-desktop menu-active-bg menu-state-primary menu-title-gray-800 menu-arrow-gray-500 align-items-stretch my-5 my-lg-0 px-2 px-lg-0 fw-semibold fs-6" id="#kt_header_menu" data-kt-menu="true">
                        <!--begin:Menu item-->
                        <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start" class="menu-item here show menu-here-bg menu-lg-down-accordion me-0 me-lg-2">
                            <!--begin:Menu link-->
                            <span class="menu-link py-3">
                                <span class="menu-title">{{ $title }}</span>
                                <span class="menu-arrow d-lg-none"></span>
                            </span>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Menu wrapper-->
            </div>
            <!--end::Navbar-->
            <!--begin::Toolbar wrapper-->
            <div class="topbar d-flex align-items-stretch flex-shrink-0">
                <!--begin::Theme mode-->
                <!-- <div class="d-flex align-items-center">
                    <a href="#" class="topbar-item px-3 px-lg-4" data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <i class="ki-outline ki-night-day theme-light-show fs-1"></i>
                        <i class="ki-outline ki-moon theme-dark-show fs-1"></i>
                    </a>
                   
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px" data-kt-menu="true" data-kt-element="theme-mode-menu">
                        
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-outline ki-night-day fs-2"></i>
                                </span>
                                <span class="menu-title">Light</span>
                            </a>
                        </div>
                        
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-outline ki-moon fs-2"></i>
                                </span>
                                <span class="menu-title">Dark</span>
                            </a>
                        </div>
                       
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-outline ki-screen fs-2"></i>
                                </span>
                                <span class="menu-title">System</span>
                            </a>
                        </div>
                    </div>
                </div> -->
                <!--end::Theme mode-->
                <!--begin::User-->
                <div class="d-flex align-items-stretch" id="kt_header_user_menu_toggle">
                    <!--begin::Menu wrapper-->
                    <div class="topbar-item cursor-pointer symbol px-3 px-lg-5 me-n3 me-lg-n5 symbol-30px symbol-md-35px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end" data-kt-menu-flip="bottom">
                    <i class="ki-outline ki-user fs-1 text-white d-lg-none"></i> 
                    <div class="bg-white p-2 rounded-4 overflow-hidden d-none d-lg-block" style="width: 45px;height: 45px;">
                      @if(session('user_image'))
                          <img width="100%" height="auto" src="{{ asset('storage/uploads/users/FMB_' . Auth::id() . '_' . ucwords(str_replace(' ', '', Auth::user()->name)) . '/user-images/'.session('user_image') ) }}" alt="User Image" />
                        @else
                          <img width="100%" height="auto" src="{{ asset('/assets/media/avatars/blank.png') }}" alt="Default User Image" />
                        @endif
                      </div>  
                    </div>
                    <!--begin::User account menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <!--begin::Avatar-->
                                <div class="symbol symbol-50px me-5">
                                  @if(session('user_image'))
                                    <img src="{{ asset('/storage/uploads/users/FMB_' . Auth::id() . '_' . ucwords(str_replace(' ', '', Auth::user()->name)) . '/user-images/'.session('user_image') ) }}" alt="User Image" />
                                  @else
                                    <img src="{{ asset('/assets/media/avatars/blank.png') }}" alt="Default User Image" />
                                  @endif
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fw-bold d-flex align-items-center fs-5">{{Auth::user()->name}}
                                  </div>
                                    <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">{{Auth::user()->roles->first()->name}}</a>
                                    <!-- <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">{{Auth::user()->email}}</a> -->
                                </div>
                            </div>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5">
                          @if(session('has_user'))
                            <a href="{{ route('profiles.edit', Auth::id()) }}" class="menu-link px-5">My Profile</a>
                          @else
                            <a href="{{ route('profiles.create') }}" class="menu-link px-5">My Profile</a>
                          @endif
                        </div>
                        <!-- <div class="separator my-2"></div>
                        <div class="menu-item px-5 my-1">
                            <a href="#" class="menu-link px-5">Account Settings</a>
                        </div> -->
                        <div class="menu-item px-5">
                            <a href="{{ URL::to('logout') }}" class="menu-link px-5">Sign Out</a>
                        </div>
                    </div>
                </div>
                <!--end::User -->
                <!--begin::Heaeder menu toggle-->
                <!-- <div class="d-flex align-items-stretch d-lg-none px-3 me-n3" title="Show header menu">
                    <div class="topbar-item" id="kt_header_user_menu_toggle">
                      <i class="ki-outline ki-user fs-1 text-white"></i>
                    </div>
                </div> -->
                <!--end::Heaeder menu toggle-->
            </div>
            <!--end::Toolbar wrapper-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Container-->
</div>