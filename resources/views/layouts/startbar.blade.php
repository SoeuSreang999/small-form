<div class="startbar d-print-none">
    <div class="brand">
        <a href="{{ url('/') }}" class="logo">
            <span>
                <img src="{{ asset('backend/images/logo-sm.png') }}" alt="logo-small" class="logo-sm">
            </span>
            <span class="">
                <img src="{{ asset('backend/images/logo-light.png') }}" alt="logo-large" class="logo-lg logo-light">
                <img src="{{ asset('backend/images/logo-dark.png') }}" alt="logo-large" class="logo-lg logo-dark">
            </span>
        </a>
    </div>
    <div class="startbar-menu" >
        <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
            <div class="d-flex align-items-start flex-column w-100">
                <ul class="navbar-nav mb-auto w-100">
                    <li class="menu-label mt-2">
                        <span>@lang('menu.menu')</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('forms.list') }}">
                            <i class="las la-tasks menu-icon"></i>
                            <span>@lang('menu.forms')</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#sidebarClass" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarClass"> 
                            <i class="las la-bars menu-icon"></i>                                    
                            <span>@lang('menu.classes')</span>
                        </a>
                        <div class="collapse " id="sidebarClass">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('classes.subject.index') }}" class="nav-link ">@lang('menu.subjects')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('classes.class.index') }}" class="nav-link ">@lang('menu.classes')</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#sidebarSetups" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarSetups"> 
                            <i class="las la-sliders-h menu-icon"></i>
                            <span>@lang('menu.settings')</span>
                        </a>
                        <div class="collapse " id="sidebarSetups">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('setups.users.index') }}" class="nav-link ">@lang('menu.users')</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>