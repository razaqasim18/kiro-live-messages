<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">@lang('translation.Menu')</li>

                <li>
                    <a href="{{ route('admin.dashboard') }}">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard">@lang('translation.Dashboards')</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.user.index') }}">
                        <i data-feather="users"></i>
                        <span data-key="t-user">@lang('translation.Users')</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.messages.index') }}">
                        <i data-feather="mail"></i>
                        <span data-key="t-message">@lang('translation.Messages')</span>
                    </a>
                </li>


                <li>
                    <a href="{{ route('admin.images.index') }}">
                        <i data-feather="image"></i>
                        <span data-key="t-image">@lang('translation.Images')</span>
                    </a>
                </li>
                {{-- <li class="menu-title" data-key="t-apps">@lang('translation.Apps')</li> --}}

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="package"></i>
                        <span data-key="t-package">@lang('translation.Package')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.package.add') }}" key="t-package">@lang('translation.Package_add')</a></li>
                        <li><a href="{{ route('admin.package.index') }}" data-key="t-package">@lang('translation.Package_list')</a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="gift"></i>
                        <span data-key="t-gift">@lang('translation.Gift')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.gift.add') }}" key="t-gift">@lang('translation.Gift_add')</a></li>
                        <li><a href="{{ route('admin.gift.index') }}" data-key="t-gift">@lang('translation.Gift_list')</a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('admin.report.index') }}">
                        <i data-feather="server"></i>
                        <span data-key="t-image">@lang('translation.Report')</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.setting.index') }}">
                        <i data-feather="settings"></i>
                        <span data-key="t-setting">@lang('translation.Settings')</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
