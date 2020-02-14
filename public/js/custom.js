$('document').ready(function() {
    $('#hide').click('on', function () {
        $('#table1').css('display', 'none');
        $('#table2').css('display', 'block');
    });

    $('#start').click('on', function () {
        $('#stop').addClass('disabled');
    });

    $('#stop').click('on', function () {
        $('#start').addClass('disabled');
        $('#start').removeClass('disabled');
    });
});
console.log(1);