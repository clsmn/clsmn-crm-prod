<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ access()->user()->picture }}" class="img-circle" alt="User Image" />
            </div><!--pull-left-->
            <div class="pull-left info">
                <p>{{ access()->user()->name }}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> {{ trans('strings.backend.general.status.online') }}</a>
            </div><!--pull-left-->
        </div><!--user-panel-->

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="{{ active_class(Active::checkUriPattern('admin/dashboard')) }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="fa fa-dashboard"></i>
                    <span>{{ trans('menus.backend.sidebar.dashboard') }}</span>
                </a>
            </li>

            @permissions(['manage-users', 'manage-roles'])
            <li class="{{ active_class(Active::checkUriPattern('lead/performance')) }}">
                <a href="{{ route('admin.lead.performance') }}">
                    <i class="fa fa-bolt"></i>
                    <span>Lead Performance</span>
                </a>
            </li>
            <li class="{{ active_class(Active::checkUriPattern('admin/access/*')) }} treeview">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>{{ trans('menus.backend.access.title') }}</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu {{ active_class(Active::checkUriPattern('admin/access/*'), 'menu-open') }}" style="display: none; {{ active_class(Active::checkUriPattern('admin/access/*'), 'display: block;') }}">
                    @permission('manage-users')
                    <li class="{{ active_class(Active::checkUriPattern('admin/access/user*')) }}">
                        <a href="{{ route('admin.access.user.index') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>{{ trans('labels.backend.access.users.management') }}</span>
                        </a>
                    </li>
                    @endauth

                    @permission('manage-roles')
                    <li class="{{ active_class(Active::checkUriPattern('admin/access/role*')) }}">
                        <a href="{{ route('admin.access.role.index') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>{{ trans('labels.backend.access.roles.management') }}</span>
                        </a>
                    </li>
                    @endauth
                </ul>
            </li>
            @endauth

            <li class="{{ active_class(Active::checkUriPattern('admin/lead')) }}">
                <a href="{{ route('admin.lead.index') }}">
                    <i class="fa fa-phone"></i>
                    <span>{{ trans('menus.backend.sidebar.lead') }}</span>
                </a>
            </li>

            @role('Manager')
            
            <li class="{{ active_class(Active::checkUriPattern('admin/assigned/leads')) }}">
                <a href="{{ route('admin.lead.assigned') }}">
                    <i class="fa fa-microphone"></i>
                    <span>{{ trans('menus.backend.sidebar.lead_assigned') }}</span>
                </a>
            </li>
            <li class="{{ active_class(Active::checkUriPattern('admin/lead/call_history')) }}">
                <a href="{{ route('admin.lead.call_history') }}">
                    <i class="fa fa-history"></i>
                    <span>{{ trans('menus.backend.sidebar.call_history') }}</span>
                </a>
            </li>

            <li class="{{ active_class(Active::checkUriPattern('admin/workforce')) }}">
                <a href="{{ route('admin.workforce.index') }}">
                    <i class="fa fa-users"></i>
                    <span>{{ trans('menus.backend.sidebar.workforce') }}</span>
                </a>
            </li>

            <li class="{{ active_class(Active::checkUriPattern('admin/data/bank')) }}">
                <a href="{{ route('admin.data.bank.index') }}">
                    <i class="fa fa-database"></i>
                    <span>{{ trans('menus.backend.sidebar.data_bank') }}</span>
                </a>
            </li>
            @endauth

            <li class="{{ active_class(Active::checkUriPattern('admin/data/bank/create')) }}">
                <a href="{{ route('admin.data.bank.create') }}">
                    <i class="fa fa-plus"></i>
                    <span>Create Lead</span>
                </a>
            </li>
            
            @role('Manager')
            <li class="{{ active_class(Active::checkUriPattern('admin/search')) }}">
                <a href="{{ route('admin.search.index') }}">
                    <i class="fa fa-search"></i>
                    <span>{{ trans('menus.backend.sidebar.search') }}</span>
                </a>
            </li>
            <li class="{{ active_class(Active::checkUriPattern('otp')) }}">
                <a href="{{ route('admin.otp') }}">
                    <i class="fa fa-key"></i>
                    <span>OTP</span>
                </a>
            </li>
            @endauth

        </ul><!-- /.sidebar-menu -->
    </section><!-- /.sidebar -->
</aside>