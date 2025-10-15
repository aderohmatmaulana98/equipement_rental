
<aside class="pe-app-sidebar" id="sidebar">
        <div class="pe-app-sidebar-logo px-6 d-flex align-items-center position-relative">
            <!--begin::Brand Image-->
            <a href="index.html" class="d-flex align-items-end logo-main">
                <img height="35" width="34" class="logo-dark" alt="Dark Logo" src="{{ asset('assets/images/logo-md.png')}}">
                <img height="35" width="34" class="logo-light" alt="Light Logo" src="{{ asset('assets/images/logo-md-light.png')}}">
                <h3 class="text-body-emphasis fw-bolder mb-0 ms-1">Urbix</h3>
            </a>
            <button type="button" id="sidebarDefaultArrow" class="btn btn-sm p-0 fs-16 text-body-emphasis ms-auto float-end d-none icon-hover-btn d-none"><i class="ri-arrow-right-line fs-5"></i></button>
            <!--end::Brand Image-->
        </div>
        <nav class="pe-app-sidebar-menu nav nav-pills" data-simplebar id="sidebar-simplebar">
            <div class="d-flex align-items-start flex-column w-100">
                <ul class="pe-main-menu list-unstyled">
                    <!-- Main Menu -->

                    <li class="pe-menu-title">Main</li>
                    @if (auth()->user()->role_id == 1)
                        <li class="pe-slide pe-has-sub">
                            <a href="{{ route('pemilik.dashboard') }}" class="pe-nav-link" >
                                <i class="ri-dashboard-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Dashboard</span>
                            </a>                        
                        </li>
                    @elseif (auth()->user()->role_id == 2)
                        <li  class="pe-slide pe-has-sub">
                            <a href="{{ route('admin.dashboard') }}" class="pe-nav-link" >
                                <i class="ri-dashboard-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Dashboard</span>
                            </a>                        
                        </li>
                    @elseif (auth()->user()->role_id == 4)
                        <li class="pe-slide pe-has-sub">
                            <a href="{{ route('warehouse.dashboard') }}" class="pe-nav-link" >
                                <i class="ri-dashboard-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Dashboard</span>
                            </a>                        
                        </li>
                    @else
                        <li class="pe-slide pe-has-sub">
                            <a href="{{ route('user.dashboard') }}" class="pe-nav-link" >
                                <i class="ri-dashboard-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Dashboard</span>
                            </a>                        
                        </li>
                    @endif
                    
                    {{-- for admin  --}}
                    @if (auth()->user()->role_id == 2)
                    <li  class="pe-slide pe-has-sub">
                        <a href="{{ route('barang.index') }}" class="pe-nav-link" >
                            <i class="ri-database-2-line pe-nav-icon"></i>
                            <span class="pe-nav-content">Barang</span>
                        </a>                        
                    </li>
                    <li  class="pe-slide pe-has-sub">
                        <a href="{{ route('jenis_barang.index') }}" class="pe-nav-link" >
                            <i class="ri-folder-5-line pe-nav-icon"></i>
                            <span class="pe-nav-content">Jenis Barang</span>
                        </a>                        
                    </li>

                    <li class="pe-slide pe-has-sub">
                        <a href="#collapseApplications" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseApplications">
                            <i class="ri-apps-2-line pe-nav-icon"></i>
                            <span class="pe-nav-content">Manajemen User</span>
                            <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                        </a>
                        <ul class="pe-slide-menu collapse" id="collapseApplications">
                            <li class="pe-slide-item">
                                <a href="{{ route('admin.admin') }}" class="pe-nav-link">
                                    User Admin
                                </a>
                            </li>
                            <li class="pe-slide-item">
                                <a href="{{ route('customer.customer') }}" class="pe-nav-link">
                                    User Customer
                                </a>
                            </li>                   
                        </ul>
                    </li>
                    @endif

                    {{-- for warehouse --}}

                    @if (auth()->user()->role_id == 4)
                    <li  class="pe-slide pe-has-sub">
                        <a href="{{ route('warehouse.list_barang') }}" class="pe-nav-link" >
                            <i class="ri-database-2-line pe-nav-icon"></i>
                            <span class="pe-nav-content">Persediaan Barang</span>
                        </a>                        
                    </li>

                    <li class="pe-slide pe-has-sub">
                        <a href="#collapseApplications" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseApplications">
                            <i class="ri-apps-2-line pe-nav-icon"></i>
                            <span class="pe-nav-content">Penyewaan</span>
                            <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                        </a>
                        <ul class="pe-slide-menu collapse" id="collapseApplications">
                            <li class="pe-slide-item">
                                <a href="{{ route('admin.admin') }}" class="pe-nav-link">
                                    Sewa
                                </a>
                            </li>
                            <li class="pe-slide-item">
                                <a href="{{ route('customer.customer') }}" class="pe-nav-link">
                                    Pengembalian
                                </a>
                            </li>                   
                        </ul>
                    </li>
                    @endif  
                    
                    {{-- for Customer --}}

                    @if (auth()->user()->role_id == 3)
                    <li  class="pe-slide pe-has-sub">
                        <a href="{{ route('user.list_barang') }}" class="pe-nav-link" >
                            <i class="ri-database-2-line pe-nav-icon"></i>
                            <span class="pe-nav-content">Persediaan Barang</span>
                        </a>                        
                    </li>
                    <li  class="pe-slide pe-has-sub">
                        <a href="{{ route('sewa.index') }}" class="pe-nav-link" >
                            <i class="ri-shopping-cart-2-line pe-nav-icon"></i>
                            <span class="pe-nav-content">Sewa</span>
                        </a>                        
                    </li>
                    
                    @endif 


                    <li  class="pe-slide pe-has-sub">
                        <a href="{{ route('logout') }}" class="pe-nav-link" >
                            <i class="ri-arrow-left-line pe-nav-icon"></i>
                            <span class="pe-nav-content">Logout</span>
                        </a>                        
                    </li>
                </ul>
            </div>
        </nav>
</aside>    