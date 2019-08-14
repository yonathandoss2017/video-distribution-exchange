<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha384-3ceskX3iaEnIogmQchP8opvBy3Mi7Ce34nWjpBIwVTHfGYWQS9jwHDVRnpKKHJg7" crossorigin="anonymous"></script>

<script>
    $('#properties').on('show.bs.collapse', function () {
        $(this).parent().addClass('active');
    })
    $('#properties').on('hide.bs.collapse', function () {
        $(this).parent().removeClass('active');
    })
</script>