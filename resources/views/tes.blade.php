<!DOCTYPE html>
<html>

<head>
    @yield('title')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"
        integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
</head>

<body>
    <div class="wrapper">
        <nav>
            <ul class="list-unstyled components" id="kategori">
                <li>
                    <a href="#dashboardSubmenu1" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        Manajemen Data</a>
                    <ul class="collapse list-unstyled" id="dashboardSubmenu1">
                        <li>
                            <input type="radio" id="html" name="fav_language" value="HTML"
                                onchange="limitRadioSelection()">
                            <label for="html">HTML</label><br>
                            <input type="radio" id="css" name="fav_language" value="CSS"
                                onchange="limitRadioSelection()">
                            <label for="css">CSS</label><br>
                            <input type="radio" id="javascript" name="fav_language" value="JavaScript"
                                onchange="limitRadioSelection()">
                            <label for="javascript">JavaScript</label>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#dashboardSubmenu2" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                            class="fa fa-database"></i> Manajemen Data</a>
                    <ul class="collapse list-unstyled" id="dashboardSubmenu2">
                        <li>
                            <input type="radio" id="html2" name="fav_language2" value="HTML"
                                onchange="limitRadioSelection()">
                            <label for="html2">HTML</label><br>
                            <input type="radio" id="css2" name="fav_language2" value="CSS"
                                onchange="limitRadioSelection()">
                            <label for="css2">CSS</label><br>
                            <input type="radio" id="javascript2" name="fav_language2" value="JavaScript"
                                onchange="limitRadioSelection()">
                            <label for="javascript2">JavaScript</label>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#dashboardSubmenu3" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                            class="fa fa-database"></i> Manajemen Data</a>
                    <ul class="collapse list-unstyled" id="dashboardSubmenu3">
                        <li>
                            <input type="radio" id="html3" name="fav_language3" value="HTML"
                                onchange="limitRadioSelection()">
                            <label for="html3">HTML</label><br>
                            <input type="radio" id="css3" name="fav_language3" value="CSS"
                                onchange="limitRadioSelection()">
                            <label for="css3">CSS</label><br>
                            <input type="radio" id="javascript3" name="fav_language3" value="JavaScript"
                                onchange="limitRadioSelection()">
                            <label for="javascript3">JavaScript</label>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#dashboardSubmenu4" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                            class="fa fa-database"></i> Manajemen Data</a>
                    <ul class="collapse list-unstyled" id="dashboardSubmenu4">
                        <li>
                            <input type="radio" id="html4" name="fav_language4" value="HTML"
                                onchange="limitRadioSelection()">
                            <label for="html4">HTML</label><br>
                            <input type="radio" id="css4" name="fav_language4" value="CSS"
                                onchange="limitRadioSelection()">
                            <label for="css4">CSS</label><br>
                            <input type="radio" id="javascript4" name="fav_language4" value="JavaScript"
                                onchange="limitRadioSelection()">
                            <label for="javascript4">JavaScript</label>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#dashboardSubmenu5" data-toggle="collapse" aria-expanded="false"
                        class="dropdown-toggle"><i class="fa fa-database"></i> Manajemen Data</a>
                    <ul class="collapse list-unstyled" id="dashboardSubmenu5">
                        <li>
                            <input type="radio" id="html5" name="fav_language5" value="HTML"
                                onchange="limitRadioSelection()">
                            <label for="html5">HTML</label><br>
                            <input type="radio" id="css5" name="fav_language5" value="CSS"
                                onchange="limitRadioSelection()">
                            <label for="css5">CSS</label><br>
                            <input type="radio" id="javascript5" name="fav_language5" value="JavaScript"
                                onchange="limitRadioSelection()">
                            <label for="javascript5">JavaScript</label>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="row">
                <div class="col-auto"><button class="btn btn-secondary btn-sm" onclick="clearRadio()">Batal</button>
                </div>
                <div class="col"><a href="" class="btn btn-secondary btn-sm">Terapkan</a></div>
            </div>
        </nav>
    </div>

    <script>
        function limitRadioSelection() {
            var selectedRadios = document.querySelectorAll('#kategori input[type="radio"]:checked');

            if (selectedRadios.length >= 3) {
                var allRadios = document.querySelectorAll('#kategori input[type="radio"]');

                for (var i = 0; i < allRadios.length; i++) {
                    if (!allRadios[i].checked) {
                        allRadios[i].disabled = true;
                    }
                }
            } else {
                var disabledRadios = document.querySelectorAll('#kategori input[type="radio"]:disabled');

                for (var j = 0; j < disabledRadios.length; j++) {
                    disabledRadios[j].disabled = false;
                }
            }
        }

        var radios = document.querySelectorAll('#kategori input[type="radio"]');
        for (var k = 0; k < radios.length; k++) {
            radios[k].addEventListener('change', limitRadioSelection);
        }

        function clearRadio() {
            let radios = document.querySelectorAll('#kategori input[type="radio"]');
            radios.forEach(function(radio) {
                radio.checked = false;
                radio.disabled = false;
            });
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"
        integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous">
    </script>
</body>

</html>
