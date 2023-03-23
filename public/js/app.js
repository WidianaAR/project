$(document).ready(function () {
    $("#sidebar").mCustomScrollbar({
        theme: "minimal"
    });

    $('#sidebarCollapse').on('click', function () {
        $('#sidebar, #content, #top-navbar').toggleClass('active');
        $('.collapse.in').toggleClass('in');
        $('a[aria-expanded=true]').attr('aria-expanded', 'false');
    });

    $('#sidebar .list-unstyled a').on('click', function () {
        $('#sidebar .list-unstyled').find('li.active').removeClass('active');
        $(this).parent('li').addClass('active');
    });

    $("select.select-role").click(function () {
        var selected = $(this).children("option:selected").val();
        if (selected == 2) {
            $('#jurusan_option').removeAttr('hidden');
            $('#prodi_option').attr('hidden', 'hidden');
        } else if (selected == 3) {
            $('#jurusan_option').removeAttr('hidden');
            $('#prodi_option').removeAttr('hidden');
        } else {
            $('#jurusan_option').attr('hidden', 'hidden');
            $('#prodi_option').attr('hidden', 'hidden');
        }
    });
});