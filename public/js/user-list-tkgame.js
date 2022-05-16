function topage(i) {
    let keyword = document.getElementById('search').value;
    let game_id = $('#game_id').val();
    let data = {};
    if (keyword == '') data = {page: i};
    else data = {keyword:keyword,page:i};
    $.ajax({
        url: `/taikhoangame/`+game_id,
        data: data,
        type: 'GET',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            $('#list-tkgame').html(danhsach_tkgame(data));
            $('#pagination').html(add_pagination(data[0],i));
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}

function danhsach_tkgame(data) {
    let content = ``;
    for (i = 1; i < data.length; i++) {
        let tkgame = data[i];
        content += `<div id="game` + tkgame['id'] + `" class="col-12 col-md-3 col-lg-3">
                <a href="/chitiettaikhoan/` + tkgame['id'] + `"><img src="` + (tkgame['anhgame'] == "" ? "/images/games/0.jpg" : tkgame['anhgame']) + `" alt="` + tkgame['tengame'] + `" title="` + tkgame['tengame'] + `" style="width: 100%; height: 180px"></a>
                <div>
                    <h5 align="center">` + tkgame['ingame'] + `</h5>
                    <h5 align="center">Giá: `+ tkgame['gia'] +`</h5>
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
        typingTimer = setTimeout(search_tkgame, doneTypingInterval);
    });

    //on keydown, clear the countdown
    $input.on('keydown', function () {
        clearTimeout(typingTimer);
    });
});
// Tìm kiếm game
function search_tkgame() {
    let keyword = $('#search').val();
    let game_id = $('#game_id').val();
    $.ajax({
        url: `/taikhoangame/`+game_id,
        data: {keyword: keyword, page: 1},
        type: 'GET',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            if (data.length == 1) {
                $('#list-tkgame').html('<h3 style="margin: 10% auto;">Không tìm thấy kết quả</h3>');
                $('#pagination').html('');
            }
            else {
                $('#list-tkgame').html(danhsach_tkgame(data));
                $('#pagination').html(add_pagination(data[0],1));
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}