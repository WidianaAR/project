<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"
        integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/all.min.js"></script>

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
                        <td>
                            <h5>SIMJAMU ITK</h5>
                        </td>
                    </tr>
                </table>
            </div>

            <ul class="list-unstyled components">
                <li
                    class="{{ Request::is(['ks_chart', 'ed_chart', 'pjm', 'kajur', 'koorprodi', 'auditor']) ? 'active' : '' }}">
                    <a href="#dashboardSubmenu" data-toggle="collapse" aria-expanded="false"
                        class="dropdown-toggle">Dashboard</a>
                    <ul class="collapse list-unstyled" id="dashboardSubmenu">
                        <li>
                            <a href="{{ URL('ks_chart') }}">Ketercapaian Standar</a>
                        </li>
                        <li>
                            <a href="{{ URL('ed_chart') }}">Evaluasi Diri</a>
                        </li>
                    </ul>
                </li>
                <li class="{{ Request::is('standar*') ? 'active' : '' }}">
                    <a href="{{ URL('standar') }}">Ketercapaian Standar</a>
                </li>
                <li class="{{ Request::is('evaluasi*') ? 'active' : '' }}">
                    <a href="{{ URL('evaluasi') }}">Evaluasi Diri</a>
                </li>
                <li class="{{ Request::is(['panduans*', 'panduan*']) ? 'active' : '' }}">
                    <a
                        href="@if (Auth::user()->role_id == 1) {{ URL('panduans') }} @else {{ URL('panduan') }} @endif">Panduan</a>
                </li>
                @can('pjm')
                    <li class="{{ Request::is('user*') ? 'active' : '' }}">
                        <a href="{{ URL('user') }}">User</a>
                    </li>
                    <li class="{{ Request::is('jurusans*') ? 'active' : '' }}">
                        <a href="{{ URL('jurusans') }}">Jurusan</a>
                    </li>
                    <li class="{{ Request::is('prodis*') ? 'active' : '' }}">
                        <a href="{{ URL('prodis') }}">Program Studi</a>
                    </li>
                @endcan
                <li class="{{ Request::is(['feedbacks*', 'feedback*']) ? 'active' : '' }}">
                    <a
                        href="@if (Auth::user()->role_id == 4) {{ URL('feedbacks') }} @else {{ URL('feedback') }} @endif">Feedback
                        Auditor</a>
                </li>
            </ul>
        </nav>

        <!-- Navbar -->
        <div class="container-fluid p-0" id="top-navbar">
            <nav class="navbar navbar-expand-lg navbar-light" id="navbar">
                <div class="collapse navbar-collapse align-items-middle" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto ml-3">
                        <li class="nav-item active">
                            <button type="button" id="sidebarCollapse" class='btn btn-light'>
                                <i class='fas fa-bars' style='color:#808080'></i>
                            </button>
                        </li>
                    </ul>

                    <span class="navbar-text text-right mr-1">
                        <b>{{ Auth::user()->name }}</b>
                        <br> {{ Auth::user()->role->role_name }}
                    </span>

                    <ul class="navbar-nav">
                        <li class="nav-item dropdown pr-4">
                            <a class="dropdown-toggle nav-link" href="#" id="dropdownMenuLink"
                                data-toggle="dropdown">
                                <img style="border-radius: 50%" src="{{ URL::asset('images/user.png') }}"
                                    width="40px" height="40px">
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
                <div class="col px-4">
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
