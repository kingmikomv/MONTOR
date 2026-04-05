<div class="az-header">
    <div class="container">
        <div class="az-header-left">
            <a href="index.html" class="az-logo"><span></span> SERVEEER</a>
            <a href="" id="azMenuShow" class="az-header-menu-icon d-lg-none"><span></span></a>
        </div><!-- az-header-left -->
        <div class="az-header-menu">
            <div class="az-header-menu-header">
                <a href="index.html" class="az-logo"><span></span> SERVEEER</a>
                <a href="" class="close">&times;</a>
            </div><!-- az-header-menu-header -->
            <ul class="nav">
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link"><i class="typcn typcn-chart-area-outline"></i>
                        Dashboard</a>
                </li>
                 <li class="nav-item">
                    <a href="{{ route('monitoring') }}" class="nav-link">
                        <i class="typcn typcn-rss"></i> Monitoring
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pelanggans.index') }}" class="nav-link">
                        <i class="typcn typcn-user"></i> Pelanggan
                    </a>
                </li>
                <li class="nav-item">
                   <a href="{{ route('hirarki.index') }}" class="nav-link">
    <i class="typcn typcn-map"></i> Hirarki
</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link with-sub"><i class="typcn typcn-document"></i> Master Data</a>
                    <nav class="az-menu-sub">
                        <a href="{{ route('servers.index') }}" class="nav-link">Server</a>
                        <a href="{{ route('olts.index') }}" class="nav-link">OLT</a>

                    </nav>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link with-sub"><i class="typcn typcn-document"></i> Infrastruktur</a>
                    <nav class="az-menu-sub">
                        <a href="{{ route('odcs.index') }}" class="nav-link">ODC</a>
                        <a href="{{ route('odps.index') }}" class="nav-link">ODP</a> <a href="page-signup.html"
                            class="nav-link">Converter</a>

                    </nav>
                </li>

            </ul>
        </div><!-- az-header-menu -->
        <div class="az-header-right">

            <div class="dropdown az-header-notification">
                <div class="dropdown-menu">
                    <div class="az-dropdown-header mg-b-20 d-sm-none">
                        <a href="" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
                    </div>

                </div><!-- dropdown-menu -->
            </div><!-- az-header-notification -->
            <div class="dropdown az-profile-menu">
                <a href="" class="az-img-user"><img src="{{asset('img/faces/face1.jpg')}}" alt=""></a>
                <div class="dropdown-menu">
                    <div class="az-dropdown-header d-sm-none">
                        <a href="" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
                    </div>
                    <div class="az-header-profile">
                        <div class="az-img-user">
                            <img src="{{asset('img/faces/face1.jpg')}}" alt="">
                        </div><!-- az-img-user -->
                        <h6>Aziana Pechon</h6>
                        <span>Premium Member</span>
                    </div><!-- az-header-profile -->

                    <a href="page-signin.html" class="dropdown-item"><i class="typcn typcn-power-outline"></i> Sign
                        Out</a>
                </div><!-- dropdown-menu -->
            </div>
        </div><!-- az-header-right -->
    </div><!-- container -->
</div><!-- az-header -->