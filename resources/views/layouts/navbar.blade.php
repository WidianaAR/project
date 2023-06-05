<!DOCTYPE html>
<html>

<head>
    @yield('title')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"
        integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">

    <script src="https://kit.fontawesome.com/478979d709.js" crossorigin="anonymous"></script>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>

    <link rel="stylesheet" href="/css/menu_bar.css">
    <link rel="stylesheet" href="/css/app.css">
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar  -->
        <nav id="sidebar">
            <div class="sidebar-header p-3">
                <table>
                    <tr>
                        <td style="width: 15%; background-color: white; border-radius: 5px"> <img
                                src="{{ URL::asset('images/Logo ITK_no teks.png') }}" width="100%" height="100%">
                        </td>
                        <td class="pl-3">
                            <h5><b>SIMJAMU ITK</b></h5>
                        </td>
                    </tr>
                </table>
            </div>

            <ul class="list-unstyled components">
                <li
                    class="{{ Request::is(['ed_chart', 'ks_chart', 'pjm', 'kajur', 'koorprodi', 'auditor']) ? 'active' : '' }}">
                    <a href="{{ URL('ed_chart') }}"><i class="fa fa-bar-chart"></i> Dashboard</a>
                </li>
                @can('pjm')
                    <li class="{{ Request::is(['user*', 'jurusans*', 'prodis*']) ? 'active' : '' }}">
                        <a href="#dashboardSubmenu2" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                                class="fa fa-database"></i> Manajemen Data</a>
                        <ul class="collapse list-unstyled" id="dashboardSubmenu2">
                            <li>
                                <a href="{{ URL('jurusans') }}">Jurusan</a>
                            </li>
                            <li>
                                <a href="{{ URL('prodis') }}">Program Studi</a>
                            </li>
                            <li>
                                <a href="{{ URL('user') }}">Pengguna</a>
                            </li>
                        </ul>
                    </li>
                @endcan
                <li class="{{ Request::is(['panduans*', 'panduan*']) ? 'active' : '' }}">
                    <a href="@if (Auth::user()->role_id == 1) {{ URL('panduans') }} @else {{ URL('panduan') }} @endif"><i
                            class="fa fa-circle-info"></i> Panduan</a>
                </li>
                @cannot('auditor')
                    <li class="{{ Request::is('evaluasi*') ? 'active' : '' }}">
                        <a href="{{ URL('evaluasi') }}"> <i class="fa fa-file"></i> Evaluasi Diri</a>
                    </li>
                    <li class="{{ Request::is('standar*') ? 'active' : '' }}">
                        <a href="{{ URL('standar') }}"> <i class="fa fa-file"></i> Ketercapaian Standar</a>
                    </li>
                @endcannot
                @can('auditor')
                    <li class="{{ Request::is('tilik*') ? 'active' : '' }}">
                        <a href="{{ URL('tilik') }}"><i class="fa-solid fa-table-list"></i> Daftar Tilik</a>
                    </li>
                    <li class="{{ Request::is('pasca*') ? 'active' : '' }}">
                        <a href="{{ URL('pasca') }}"><i class="fa fa-files-o"></i> Pasca Audit</a>
                    </li>
                @endcan
                @can('pjm')
                    <li class="{{ Request::is('logs*') ? 'active' : '' }}">
                        <a href="{{ URL('logs') }}"> <i class="fa fa-history"></i> Riwayat Aktivitas</a>
                    </li>
                @endcan
            </ul>
        </nav>

        <!-- Navbar -->
        <div class="container-fluid" id="top-navbar">
            <nav class="navbar navbar-expand-md navbar-light mx-4 mt-3" id="navbar">
                <div class="collapse navbar-collapse align-items-middle">
                    <ul class="navbar-nav mr-auto ml-3">
                        <li class="nav-item active">
                            <a class="btn btn-light btn-sm nav-link dropdown-toggle" href="#" id="sidebarCollapse"
                                role="button" data-toggle="dropdown">
                                <i class="fas fa-bars fa-sm" style="color:#808080"></i>
                            </a>
                            <div class="dropdown-menu" id="dropdown-nav">
                                <a class="dropdown-item" href="{{ URL('ed_chart') }}">Chart Evaluasi Diri</a>
                                <a class="dropdown-item" href="{{ URL('ks_chart') }}">Chart Ketercapaian Standar</a>
                                <a class="dropdown-item" href="{{ URL('evaluasi') }}">Evaluasi Diri</a>
                                <a class="dropdown-item" href="{{ URL('standar') }}">Ketercapaian Standar</a>
                                <a class="dropdown-item"
                                    href="@if (Auth::user()->role_id == 1) {{ URL('panduans') }} @else {{ URL('panduan') }} @endif">Panduan</a>
                                @can('pjm')
                                    <a class="dropdown-item" href="{{ URL('user') }}">User</a>
                                    <a class="dropdown-item" href="{{ URL('jurusans') }}">Jurusan</a>
                                    <a class="dropdown-item" href="{{ URL('prodis') }}">Program Studi</a>
                                @endcan
                                <a class="dropdown-item" href="{{ URL('feedbacks') }}">Temuan Audit</a>
                                @can('pjm')
                                    <a class="dropdown-item" href="{{ URL('logs') }}">Riwayat Aktivitas</a>
                                @endcan
                            </div>
                        </li>
                    </ul>

                    <ul class="navbar-nav">
                        <li class="nav-item dropdown pr-4">
                            <a class="dropdown-toggle nav-link py-0" href="#" id="dropdownMenuLink"
                                data-toggle="dropdown">
                                <span class="navbar-text text-right mr-1">
                                    <b>{{ Auth::user()->name }}</b>
                                    <br> {{ Auth::user()->role->role_name }}
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('change_password', Auth::user()->id) }}">Ubah
                                    Password</a>
                                <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <!-- Content -->
        <div id="content">
            <div class="container-fluid row justify-content-center p-0 m-0">
                <div class="col mx-4">
                    @yield('isi')
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"
        integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"
        integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous">
    </script>
    <script src="/js/app.js"></script>
</body>

</html>
