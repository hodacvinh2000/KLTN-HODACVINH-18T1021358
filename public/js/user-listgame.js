function topage(i) {
    var keyword = document.getElementById('search').value;
    let data = {};
    if (keyword == '') data = {page: i};
    else data = {keyword:keyword,page:i};
    $.ajax({
        url: `/taikhoangame`,
        data: data,
        type: 'GET',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            $('#list-game').html(danhsach_game(data));
            $('#pagination').html(add_pagination(data[0],i));
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}

function danhsach_game(data) {
    let content = ``;
    for (i = 1; i < data.length; i++) {
        game = data[i];
        var id = game['id'];
        content += `<div id="game` + game['id'] + `" class="col-12 col-md-3 col-lg-3">
                <a href="/taikhoangame/` + game['id'] + `"><img src="` + (game['anh'] == "" ? "/images/games/0.jpg" : game['anh']) + `" alt="` + game['tengame'] + `" title="` + game['tengame'] + `" style="width: 100%; height: 180px"></a>
                <div>
                    <h4 align="center">` + game['tengame'] + `</h4>
                </div>
            </div>\n`;
    }
    return content;
}
// Set delay thời gian tìm kiếm
$(document).ready(function(){
    var typingTimer;
    var doneTypingInterval = 1000;
    var $input = $('#search');

    //on keyup, start the countdown
    $input.on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(search_game, doneTypingInterval);
    });

    //on keydown, clear the countdown
    $input.on('keydown', function () {
        clearTimeout(typingTimer);
    });
});
// Tìm kiếm game
function search_game() {
    var keyword = $('#search').val();
    $.ajax({
        url: `/taikhoangame`,
        data: {keyword: keyword, page: 1},
        type: 'GET',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            if (data.length == 1) {
                $('#list-game').html('<h3 style="margin: 10% auto;">Không tìm thấy kết quả</h3>');
                $('#pagination').html('');
            }
            else {
                $('#list-game').html(danhsach_game(data));
                $('#pagination').html(add_pagination(data[0],1));
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}