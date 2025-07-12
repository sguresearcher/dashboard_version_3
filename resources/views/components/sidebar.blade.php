@auth
    {{-- Sidebar untuk Superadmin --}}
    @if (auth()->user()->role === 'superadmin')
    <div id="sidebar" class="app-sidebar">
        <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
            <div class="menu">
                <div class="menu-header">Navigation</div>

                <div class="menu-item active">
                    <a href="/superadmin" class="menu-link">
                        <span class="menu-icon"><i class="bi bi-cpu"></i></span>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a href="/superadmin/monitor" class="menu-link">
                        <span class="menu-icon"><i class="bi bi-display"></i></span>
                        <span class="menu-text">Monitor</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar untuk Tenant --}}
    @elseif (auth()->user()->role === 'tenant')
    <div id="sidebar" class="app-sidebar">
        <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
            <div class="menu">
                <div class="menu-header">Navigation</div>

                <div class="menu-item active">
                    <a href="/home" class="menu-link">
                        <span class="menu-icon"><i class="bi bi-cpu"></i></span>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </div>

                <div class="menu-item has-sub">
                    <a href="#" class="menu-link">
                        <span class="menu-icon"><i class="bi bi-bezier"></i></span>
                        <span class="menu-text">Sensor</span>
                        <span class="menu-caret"><b class="caret"></b></span>
                    </a>
                    <div class="menu-submenu">
                        <div class="menu-item"><a href="/sensor/conpot" class="menu-link"><span class="menu-text">Conpot</span></a></div>
                        <div class="menu-item"><a href="/sensor/honeytrap" class="menu-link"><span class="menu-text">Honeytrap</span></a></div>
                        <div class="menu-item"><a href="/sensor/cowrie" class="menu-link"><span class="menu-text">Cowrie</span></a></div>
                        <div class="menu-item"><a href="/sensor/dionaea" class="menu-link"><span class="menu-text">Dionaea</span></a></div>
                        <div class="menu-item"><a href="/sensor/rdpy" class="menu-link"><span class="menu-text">Rdpy</span></a></div>
                        <div class="menu-item"><a href="/sensor/dionaea_ews" class="menu-link"><span class="menu-text">Dionaea Ews</span></a></div>
                        <div class="menu-item"><a href="/sensor/elasticpot" class="menu-link"><span class="menu-text">Elasticpot</span></a></div>
                    </div>
                </div>

                <div class="menu-item">
                    <a href="/monitor" class="menu-link">
                        <span class="menu-icon"><i class="bi bi-display"></i></span>
                        <span class="menu-text">Monitor</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
@endauth

@guest
    {{-- Sidebar untuk Guest --}}
    <div id="sidebar" class="app-sidebar">
        <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
            <div class="menu">
                <div class="menu-header">Navigation</div>
                <div class="menu-item active">
                    <a href="/" class="menu-link">
                        <span class="menu-icon"><i class="bi bi-cpu"></i></span>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endguest
