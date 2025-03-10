<div id="header" class="app-header">
			
    <!-- BEGIN desktop-toggler -->
    <div class="desktop-toggler">
        <button type="button" class="menu-toggler" data-toggle-class="app-sidebar-collapsed" data-dismiss-class="app-sidebar-toggled" data-toggle-target=".app">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
    </div>
    <!-- BEGIN desktop-toggler -->
    
    <!-- BEGIN mobile-toggler -->
    <div class="mobile-toggler">
        <button type="button" class="menu-toggler" data-toggle-class="app-sidebar-mobile-toggled" data-toggle-target=".app">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
    </div>
    <!-- END mobile-toggler -->
    
    
    
    <!-- BEGIN brand -->
    <div class="brand">
        <a href="#" class="brand-logo">
            <img src="{{ asset('assets/img/logo.png') }}" alt="">
        </a>
    </div>
    <!-- END brand -->
    
    <!-- BEGIN menu -->
    <div class="menu">
        <div class="menu-item dropdown dropdown-mobile-full">
            <a href="#" data-bs-toggle="dropdown" data-bs-display="static" class="menu-link">
                @auth
                <div class="menu-text d-sm-block d-none w-170px">{{ auth()->user()->name }}</div>
                @endauth
                @guest
                <div class="menu-text d-sm-block d-none w-170px">GUEST</div>
                @endguest
            </a>
            <div class="dropdown-menu dropdown-menu-end me-lg-3 fs-11px mt-1">
               @auth
               @if (auth()->user()->role == 'superadmin')
               <a class="dropdown-item d-flex align-items-center" href="/superadmin/setting">SETTINGS <i class="bi bi-gear ms-auto text-theme fs-16px my-n1"></i></a>
               @else
               <a class="dropdown-item d-flex align-items-center" href="/setting">SETTINGS <i class="bi bi-gear ms-auto text-theme fs-16px my-n1"></i></a>
               @endif
               <div class="dropdown-divider"></div>
               <form action="/logout" method="POST">
               @csrf
               <button type="submit" class="dropdown-item d-flex align-items-center">LOGOUT <i class="bi bi-toggle-off ms-auto text-theme fs-16px my-n1"></i></button>
               </form>
               @endauth
               @guest
               <a class="dropdown-item d-flex align-items-center" href="/login">LOGIN <i class="bi bi-toggle-off ms-auto text-theme fs-16px my-n1"></i></a>
               @endguest
            </div>
        </div>
    </div>
    <!-- END menu -->
    
</div>