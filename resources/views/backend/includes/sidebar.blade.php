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
            <li class="{{ active_class(Active::checkUriPattern('lead/upload')) }}">
                <a href="{{ route('admin.lead.upload') }}">
                    <i class="fa fa-upload"></i>
                    <span>Uploaded Data</span>
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
            @roles(['Manager','Executive'])
            <li class="{{ active_class(Active::checkUriPattern('admin/lead')) }}">
                <a href="{{ route('admin.lead.index') }}">
                    <i class="fa fa-phone"></i>
                    <span>{{ trans('menus.backend.sidebar.lead') }}</span>
                </a>
            </li>
            @endauth
            @role('Manager')
            
            <li class="{{ active_class(Active::checkUriPattern('admin/assigned/leads')) }}">
                <a href="{{ route('admin.lead.assigned') }}">
                    <i class="fa fa-microphone"></i>
                    <span>{{ trans('menus.backend.sidebar.lead_assigned') }}</span>
                </a>
            </li>
            <li class="{{ active_class(Active::checkUriPattern('admin/lead/expiringPlan')) }}">
                <a href="{{ route('admin.reports.expiringPlan') }}">
                    <i class="fa fa-refresh"></i>
                    <span>Re-pitch Next Annual Kit</span>
                </a>
            </li>
            <li class="{{ active_class(Active::checkUriPattern('admin/lead/expiringPlanReSale')) }}">
                <a href="{{ route('admin.reports.expiringPlanReSale') }}">
                    <i class="fa fa-money"></i>
                    <span>Re-pitch Re-sale</span>
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

           <!--  <li class="{{ active_class(Active::checkUriPattern('admin/workforce')) }}">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>{{ trans('menus.backend.sidebar.workforce') }}</span>
                </a>
            </li> -->

            <li class="{{ active_class(Active::checkUriPattern('admin/data/bank')) }}">
                <a href="{{ route('admin.data.bank.index') }}">
                    <i class="fa fa-database"></i>
                    <span>{{ trans('menus.backend.sidebar.data_bank') }}</span>
                </a>
            </li>
            @endauth
            @roles(['Manager','Executive'])
            <li class="{{ active_class(Active::checkUriPattern('admin/data/bank/create')) }}">
                <a href="{{ route('admin.data.bank.create') }}">
                    <i class="fa fa-plus"></i>
                    <span>Create Lead</span>
                </a>
            </li>
            @endauth
            @roles(['Manager','Delight Team'])
            <li class="{{ active_class(Active::checkUriPattern('admin/sales/delight')) }}">
                <a href="{{ route('admin.sales.index') }}">
                    <i class="fa fa-line-chart"></i>
                    <span>Delight Sales</span>
                </a>
            </li>
            @endauth
            @role('Manager')
           <!--  <li class="{{ active_class(Active::checkUriPattern('admin/sales/delight')) }}">
                <a href="{{ route('admin.sales.index') }}">
                    <i class="fa fa-line-chart"></i>
                    <span>Delight Sales</span>
                </a>
            </li> -->
            <li class="{{ active_class(Active::checkUriPattern('admin/fb/getCampaign')) }}">
                <a href="{{ route('admin.fb.index') }}">
                    <i class="fa fa-facebook"></i>
                    <span>FB Campaigns</span>
                </a>
            </li>
            @endauth

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
            <li class="{{ active_class(Active::checkUriPattern('hmvisits')) }}">
                <a href="{{ route('admin.hmvisits') }}">
                    <i class="fa fa-comment"></i>
                    <span>HM Visits</span>
                </a>
            </li>
            <li class="{{ active_class(Active::checkUriPattern('admin/reports/*')) }} treeview">
                <a href="#">
                    <i class="fa fa-file"></i>
                    <span>Reports</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu {{ active_class(Active::checkUriPattern('admin/reports/sales'), 'menu-open') }}">
                    <li class="{{ active_class(Active::checkUriPattern('admin/reports/sales*')) }}">
                        <a href="{{ route('admin.reports.sales') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>Sales Report</span>
                        </a>
                    </li>

                    <li class="{{ active_class(Active::checkUriPattern('admin/reports/leads*')) }}">
                        <a href="{{ route('admin.reports.leads') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>Leads Report</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endauth

        </ul><!-- /.sidebar-menu -->
    </section><!-- /.sidebar -->
</aside>