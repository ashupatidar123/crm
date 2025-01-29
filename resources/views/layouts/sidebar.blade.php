<!-- Main Sidebar Container -->
<aside class="main-sidebar elevation-4 sidebar-light-lime">
    <!-- Brand Logo -->
    <a href="{{url('/dashboard')}}" class="brand-link text-sm">
    <img src="{{ url('public/images/img/sts-marine.png') }}" alt="STS Marien" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">STS Marien</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column text-sm" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item {{(Request::segment(1)=='dashboard')?'menu-open':''}}">
                    <a href="{{url('/dashboard')}}" class="nav-link {{(Request::segment(1)=='dashboard')?'active':''}}">
                        <i class="nav-icon fa fa-home"></i>
                        <p>Home
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                </li>

                <li class="nav-item {{(Request::segment(1)=='user')?'menu-is-opening menu-open':''}}">
                    <a href="#" class="nav-link {{(Request::segment(2)=='user')?'active':''}}">
                        <i class="nav-icon fa fa-database"></i>
                        <p>User Management
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{((Request::segment(2)=='user') || (Request::segment(2)=='user-details'))?'nav_active':''}}">
                            <a href="{{url('master/user')}}" class="nav-link">
                                <i class="fa fa-mail-reply-all nav-icon"></i>
                                <p>Users</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{(Request::segment(1)=='master')?'menu-is-opening menu-open':''}}">
                    <a href="#" class="nav-link {{((Request::segment(1)=='master') && (Request::segment(2) != 'user'))?'active':''}}">
                        <i class="nav-icon fa fa-database"></i>
                        <p>Master Management
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <!-- <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('role.index')}}" class="nav-link">
                                <i class="fa fa-mail-reply-all nav-icon"></i>
                                <p>Roles</p>
                            </a>
                        </li>
                    </ul> -->
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{(Request::segment(2)=='department')?'nav_active':''}}">
                            <a href="{{route('department.index')}}" class="nav-link">
                                <i class="fa fa-mail-reply-all nav-icon"></i>
                                <p>Departments</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{(Request::segment(2)=='designation')?'nav_active':''}}">
                            <a href="{{route('designation.index')}}" class="nav-link">
                                <i class="fa fa-mail-reply-all nav-icon"></i>
                                <p>Designations</p>
                            </a>
                        </li>
                    </ul>
                    
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{(Request::segment(2)=='document')?'nav_active':''}}">
                            <a href="{{url('master/document')}}" class="nav-link">
                                <i class="fa fa-mail-reply-all nav-icon"></i>
                                <p>Documents</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{(Request::segment(3)=='country')?'nav_active':''}}">
                            <a href="{{url('master/region/country')}}" class="nav-link">
                                <i class="fa fa-mail-reply-all nav-icon"></i>
                                <p>Country</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{(Request::segment(3)=='state')?'nav_active':''}}">
                            <a href="{{url('master/region/state')}}" class="nav-link">
                                <i class="fa fa-mail-reply-all nav-icon"></i>
                                <p>State</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{(Request::segment(3)=='city')?'nav_active':''}}">
                            <a href="{{url('master/region/city')}}" class="nav-link">
                                <i class="fa fa-mail-reply-all nav-icon"></i>
                                <p>City</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{(Request::segment(1)=='vessel')?'menu-is-opening menu-open':''}}">
                    <a href="#" class="nav-link {{(Request::segment(1)=='vessel')?'active':''}}">
                        <i class="nav-icon fa fa-database"></i>
                        <p>Vessel Management
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{(Request::segment(2)=='vessel')?'nav_active':''}}">
                            <a href="{{route('vessel.index')}}" class="nav-link">
                                <i class="fa fa-mail-reply-all nav-icon"></i>
                                <p>Vessel</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{(Request::segment(2)=='vessel-category')?'nav_active':''}}">
                            <a href="{{route('vessel-category.index')}}" class="nav-link">
                                <i class="fa fa-mail-reply-all nav-icon"></i>
                                <p>Category</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>