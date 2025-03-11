@auth
@if (auth()->user()->role == 'superadmin')
<div id="sidebar" class="app-sidebar">
    <!-- BEGIN scrollbar -->
    <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
        <!-- BEGIN menu -->
        <div class="menu">
            <div class="menu-header">Navigation</div>
            <div class="menu-item active">
                <a href="/superadmin" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-cpu"></i></span>
                    <span class="menu-text">Dashboard</span>
                </a>
            </div>
            <div class="menu-item has-sub">
                <a href="#" class="menu-link">
                    <span class="menu-icon">
                        <i class="bi bi-bezier"></i>
                    </span>
                    <span class="menu-text">Sensor</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu">
                    <div class="menu-item">
                        <a href="/superadmin/sensor/conpot" class="menu-link">
                            <span class="menu-text">Conpot</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a href="/superadmin/sensor/honeytrap" class="menu-link">
                            <span class="menu-text">Honeytrap</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a href="/superadmin/sensor/cowrie" class="menu-link">
                            <span class="menu-text">Cowrie</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END scrollbar -->
</div>
@else
<div id="sidebar" class="app-sidebar">
    <!-- BEGIN scrollbar -->
    <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
        <!-- BEGIN menu -->
        <div class="menu">
            <div class="menu-header">Navigation</div>
            <div class="menu-item active">
                <a href="/" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-cpu"></i></span>
                    <span class="menu-text">Dashboard</span>
                </a>
            </div>
            <div class="menu-item has-sub">
                <a href="#" class="menu-link">
                    <span class="menu-icon">
                        <i class="bi bi-bezier"></i>
                    </span>
                    <span class="menu-text">Sensor</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu">
                    <div class="menu-item">
                        <a href="/sensor/conpot" class="menu-link">
                            <span class="menu-text">Conpot</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a href="/sensor/honeytrap" class="menu-link">
                            <span class="menu-text">Honeytrap</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a href="/sensor/cowrie" class="menu-link">
                            <span class="menu-text">Cowrie</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END scrollbar -->
</div>
@endif
@endauth
@guest
<div id="sidebar" class="app-sidebar">
    <!-- BEGIN scrollbar -->
    <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
        <!-- BEGIN menu -->
        <div class="menu">
            <div class="menu-header">Navigation</div>
            <div class="menu-item active">
                <a href="index-2.html" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-cpu"></i></span>
                    <span class="menu-text">Dashboard</span>
                </a>
            </div>
            <div class="menu-item has-sub">
                <a href="#" class="menu-link">
                    <span class="menu-icon">
                        <i class="bi bi-bezier"></i>
                    </span>
                    <span class="menu-text">Sensor</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu">
                    <div class="menu-item">
                        <a href="/sensor/conpot" class="menu-link">
                            <span class="menu-text">Conpot</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a href="/sensor/honeytrap" class="menu-link">
                            <span class="menu-text">Honeytrap</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a href="/sensor/cowrie" class="menu-link">
                            <span class="menu-text">Cowrie</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END scrollbar -->
</div>
@endguest