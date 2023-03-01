<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="stylesheet" href="{{ URL::asset('css/menu_bar.css') }}">
    {{-- <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css"> --}}
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="//use.fontawesome.com/releases/v5.7.2/css/all.css">
    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.7.2/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.7.2/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>
  </head>

  <body>
    <div class="wrapper">
        <!-- Sidebar  -->
        <nav id="sidebar" class="mCustomScrollbar _mCS_1 mCS-autoHide mCS_no_scrollbar" style="overflow: visible;">
            <div id="mCSB_1" class="mCustomScrollBox mCS-minimal mCSB_vertical mCSB_outside" style="max-height: none;" tabindex="0">
                <div id="mCSB_1_container" class="mCSB_container mCS_y_hidden mCS_no_scrollbar_y" style="position:relative; top:0; left:0;" dir="ltr">
                    <div class="sidebar-header">
                        <table>
                            <tr align="center">
                                <td style="width: 15%; background-color: white; border-radius: 5px"> <img src="{{ URL::asset('images/Logo ITK_no teks.png') }}" width="100%" height="100%"> </td>
                                <td> <h5>SIMJAMU ITK</h5> </td>
                            </tr>
                        </table>
                    </div>

                    <ul class="list-unstyled components">
                        <li class="{{ Request::is(['ks_statistik', 'ed_statistik', 'pjm', 'kajur', 'koorprodi', 'auditor']) ? 'active' : '' }}">
                            <a href="#statistikSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Statistik</a>
                            <ul class="collapse list-unstyled" id="statistikSubmenu">
                                <li>
                                    <a href="{{ URL('ks_statistik') }}">Ketercapaian Standar</a>
                                </li>
                                <li>
                                    <a href="{{ URL('ed_statistik') }}">Evaluasi Diri</a>
                                </li>
                            </ul>
                        </li>
                        <li class="{{ Request::is(['standar', 'standar/set_waktu']) ? 'active' : '' }}">
                            <a href="{{ URL('standar') }}">Ketercapaian Standar</a>
                        </li>
                        <li class="{{ Request::is(['evaluasi', 'evaluasi/set_waktu']) ? 'active' : '' }}">
                            <a href="{{ URL('evaluasi') }}">Evaluasi Diri</a>
                        </li>
                        <li class="{{ Request::is('') ? 'active' : '' }}">
                            <a href="#">Panduan</a>
                        </li>
                        @if (Auth::user()->id_role == 1)
                            <li class="{{ Request::is(['user', 'tambah_user', 'user/filter/pjm', 'user/filter/kajur', 'user/filter/koorprodi', 'user/filter/auditor']) ? 'active' : '' }}">
                                <a href="{{ URL('user') }}">User</a>
                            </li>
                            <li class="{{ Request::is('') ? 'active' : '' }}">
                                <a href="#">Jurusan</a>
                            </li>
                            <li class="{{ Request::is('') ? 'active' : '' }}">
                                <a href="#">Program Studi</a>
                            </li>
                        @endif
                        <li class="{{ Request::is('') ? 'active' : '' }}">
                            <a href="#">Feedback Auditor</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Navbar -->
        <div class="container-fluid" id="top-navbar">
            <nav class="navbar navbar-expand-lg navbar-light bg-light" id="navbar">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto" id="left-menu">
                      <li class="nav-item active">
                        <button type="button" id="sidebarCollapse" class="btn btn-light">
                            <i class='fas fa-grip-lines' style='color:#808080'></i>
                        </button>
                      </li>
                      @yield('top-navbar')
                    </ul>
                    <ul class="navbar-nav ml-auto" id="right-menu">
                        <li class="nav-item">
                            <img src="{{ URL::asset('images/user.png') }}" width="40px" height="40px">
                        </li>
                        <li class="nav-item dropdown" style="padding-right: 5vh">
                            <a class="dropdown-toggle nav-link" href="#" id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                                User Name
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item" href="#">Ubah Password</a>
                                <a class="dropdown-item" href="{{url('logout')}}">Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        
        <!-- Content -->
        <div id="content" class="">
            <div class="container-fluid">
                @yield('isi')       
            </div>
        </div>
    </div>

    <!-- jQuery CDN - Slim version (=without AJAX) -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <!-- Popper.JS -->
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script> --}}
    <!-- jQuery Custom Scroller CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
    <!-- jQuery Custom Dropdown CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    <script src="{{ URL::asset('js/app.js') }}"></script>
  </body>
</html>