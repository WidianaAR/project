<div class="time" id="countdown"></div>

<script>
    var date = {!! json_encode($deadline[0]) !!};
    var id = {!! json_encode($deadline[1]) !!};
    var kategori = {!! json_encode($kategori) !!};

    if (date) {
        var countDownDate = new Date(date).getTime();
        var x = setInterval(function() {
            var now = new Date().getTime();

            var distance = countDownDate - now;

            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            $("#countdown").html(days + "d " + hours + "h " + minutes + "m " + seconds + "s");

            if (distance < 0) {
                clearInterval(x);
                if (kategori == 'evaluasi') {
                    var url = "{{ route('ed_set_time_action_end', ':id') }}";
                } else {
                    var url = "{{ route('ks_set_time_action_end', ':id') }}";
                }
                url = url.replace(':id', id);
                location.href = url;
            }
        }, 1000);
    }
</script>
