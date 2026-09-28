<!-- main-header opened -->
<div class="main-header sticky side-header nav nav-item">
    <div class="container-fluid the-main">
        <div class="main-header-left ">
            <div class="responsive-logo">
                <a href="https://ebdeasoft.com/"><img src="{{ URL::asset('assets/img/brand/logo.png') }}" onerror="this.onerror=null;this.src='{{ asset('assets/img/default-logo.svg') }}'" class="logo-1"
                        alt="logo"></a>
                <a href="https://ebdeasoft.com/"><img src="{{ URL::asset('assets/img/brand/logo-white.png') }}" onerror="this.onerror=null;this.src='{{ asset('assets/img/default-logo.svg') }}'"
                        class="dark-logo-1" alt="logo"></a>
                <a href="https://ebdeasoft.com/"><img src="{{ URL::asset('assets/img/brand/favicon.png') }}" onerror="this.onerror=null;this.src='{{ asset('assets/img/default-logo.svg') }}'"
                        class="logo-2" alt="logo"></a>
                <a href="https://ebdeasoft.com/"><img src="{{ URL::asset('assets/img/brand/favicon.png') }}" onerror="this.onerror=null;this.src='{{ asset('assets/img/default-logo.svg') }}'"
                        class="dark-logo-2" alt="logo"></a>
            </div>
            <div class="app-sidebar__toggle" data-toggle="sidebar">
                <a class="open-toggle" href="#"><i class="header-icon fe fe-align-left"></i></a>
                <a class="close-toggle" href="#"><i class="header-icons fe fe-x"></i></a>
            </div>
            <div class="main-header-center mr-3 d-sm-none d-md-none d-lg-block parent-header mb-2">
         




            </div>
        </div>

        <div class="main-header-right">


            <div class="nav nav-item  navbar-nav-right ml-auto">
                <ul class="links">
                    <li class="dropdown">
                        <a href="#" class="trigger-drop main-a lang-a">
                            {{ LaravelLocalization::getCurrentLocaleName() == 'اللغة العربية' ? 'العربية' : LaravelLocalization::getCurrentLocaleName() }}
                            <i class="arrow">
                            </i>
                        </a>
                        <ul class="drop" style="margin-left: 20px">
                            
                            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                <li class="cont-a">
                                    <a class="lang-a choice" rel="alternate" hreflang="{{ $localeCode }}"
                                        href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                        {{ $properties['native'] }}
                                    </a>
                                </li>
                            @endforeach

                        </ul>
                    </li>
                </ul>


                <?php
                $products = [];
                $products_low_quantity = [];
                foreach ($products as $product) {
                    if ($product->numberofpice <= $product->minmum_quantity_stock_alart) {
                        $products_low_quantity[] = $product;
                    }
                }
                ?>

                @include('layouts._notifications_bell')

                <div class="nav-item full-screen fullscreen-button left-link">
                    <a class="new nav-link full-screen-link" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 22 22"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-maximize">
                            <path
                                d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3">
                            </path>
                        </svg>
                    </a>
                </div>
                <div class="dropdown main-profile-menu nav nav-item nav-link left-link">
                    <a class="profile-user d-flex" href="">
                        <img alt="" src="{{ !empty(Auth::user()->profile_photo_path) ? URL::asset('storage/' . Auth::user()->profile_photo_path) : asset('assets/img/default-user.svg') }}" onerror="this.onerror=null;this.src='{{ asset('assets/img/default-user.svg') }}'">
                    </a>
                    <div class="dropdown-menu profile-menu">
                        <div class="main-header-profile bg-primary p-3">
                            <div class="d-flex wd-100p">
                                <div class="main-img-user"><img alt=""
                                        src="{{ !empty(Auth::user()->profile_photo_path) ? URL::asset('storage/' . Auth::user()->profile_photo_path) : asset('assets/img/default-user.svg') }}" onerror="this.onerror=null;this.src='{{ asset('assets/img/default-user.svg') }}'"
                                        class=""></div>
                                <div class="mr-3 my-auto">
                                    <h6>{{ Auth::user()->name }}</h6><span>{{ Auth::user()->email }}</span>
                                </div>
                            </div>
                        </div>

                        <a class="dropdown-item" href="{{ route('profile.show') }}"><i
                                class="bx bx-slider-alt"></i>{{ __('auth.setting') }}</a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i
                                class="bx bx-log-out"></i>تسجيل خروج</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                            style="display: none;">
                            @csrf
                        </form>

                    </div>

                </div>
                <div class="dropdown main-header-message right-toggle left-link shortcut-window">
                    <a class="nav-link pr-0" data-toggle="sidebar-left" data-target=".sidebar-left">
                        <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-menu">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /main-header -->


@section('js')
    <script>
        function display_ct6() {
            var x = new Date()
            var ampm = x.getHours() >= 12 ? ' PM' : ' AM';
            hours = x.getHours() % 12;
            hours = hours ? hours : 12;
            var x1 = x.getMonth() + 1 + "/" + x.getDate() + "/" + x.getFullYear();
            x1 = x1 + " - " + hours + ":" + x.getMinutes() + ":" + x.getSeconds() + ":" + ampm;
            document.getElementById('ct6').innerHTML = x1;
         
            display_c6();
            
        
            
        }

        function display_c6() {
            var refresh = 1000; // Refresh rate in milli seconds
            mytime = setTimeout('display_ct6()', refresh)
        }



        $(document).ready(function() {


            display_c6()
        });
    </script>
@endsection
