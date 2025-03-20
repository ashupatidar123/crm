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
                <?php
                    $permission_menu = @Session::get('permission_menu');
                    
                    //$check_menu_url = str_replace(url('/').'/','',url()->current());

                    $check_menu_url = Request::segment(1).'/'.Request::segment(2);
                    $check_menu_url_region = 'master/region/'.Request::segment(3);
                ?>
                @if(!empty($permission_menu))
                    @foreach($permission_menu as $d_menu)
                    <?php
                        $menu_open = sidebar_menu_open($check_menu_url,$d_menu['menu_slug']);
                        $menu_active = sidebar_menu_active(Request::segment(2),$d_menu['menu_slug']);   
                    ?>

                    <li class="nav-item {{$menu_open}}">
                        <a href="#" class="nav-link {{$menu_active}}">
                            <i class="nav-icon fa fa-database"></i>
                            <p>{{@$d_menu['menu_name']}}
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        @foreach($d_menu['sub_menu_one'] as $s_menu)
                            <?php
                                $s_menu_link = !empty($s_menu['menu_link']) ?$s_menu['menu_link']: '#';

                                $sub_nav_active = '';
                                if($s_menu_link == $check_menu_url || $check_menu_url_region == $s_menu_link){
                                    $sub_nav_active = 'nav_active';
                                }
                            ?>
                            <ul class="nav nav-treeview">
                                <li class="nav-item {{$sub_nav_active}}">
                                    <a href="{{url($s_menu_link)}}" class="nav-link">
                                        <i class="fa fa-mail-reply-all nav-icon"></i>
                                        <p>{{@$s_menu['menu_name']}}</p>
                                    </a>
                                </li>
                            </ul>
                        @endforeach    
                    </li>
                    @endforeach
                @endif
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>