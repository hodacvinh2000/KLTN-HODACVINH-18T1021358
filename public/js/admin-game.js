var current_page = 1;
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
    let keyword = $('#search').val();
    $.ajax({
        url: `/admin/quanly_game`,
        data: {keyword: keyword, page: 1},
        type: 'GET',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            if (data.length == 1) {
                $('#list_game').html('<h3 align="center" style="margin-top: 20%;">Không tìm thấy kết quả!</h3>');
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
function topage(i) {
    let keyword = $('#search').val();
    let data = {};
    if (keyword == '') data = {page: i};
    else data = {keyword:keyword,page:i};
    console.log(data);
    $.ajax({
        url: `/admin/quanly_game`,
        data: data,
        type: 'GET',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            if (data.length==1 && i > 1) {
                topage(i-1);
            }
            else {
                $('#list-game').html(danhsach_game(data));
                $('#pagination').html(add_pagination(data[0],i))
                current_page = i;
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}

function danhsach_game(data) {
    var content = `<table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>STT</th>
                <th>Tên game</th>
                <th>Hành động</th>
            </tr>
            </thead>
            <tbody>\n`;
    for (i = 1; i < data.length; i++) {
        game = data[i];
        var id = game['id'];
        content += `<tr id="` + ('game' + game['id']) + `">
                <th scope="row">` + i + `</th>
                <td>` + game['tengame'] + `</td>
                <td>
                    <a href="/admin/quanly_game/edit/` + id + `"><button class="btn btn-primary">Sửa</button></a>
                    <button class="btn btn-danger" onclick="delete_game(` + id + `)">Xóa</button>
                </td>\n`;
    }
    content += `</tbody>\n</table>`;
    return content;
}
// Hàm xóa game
function delete_game(id) {
    var question = confirm("Bạn chắc chắn muốn xóa game này?");
    if (question) {
        $.ajax({
            url: `/admin/quanly_game/delete/` + id,
            type: 'POST',
            dataType: 'json',
            async: true,

            success: function (data) {
                topage(current_page);
                if (data['status'] == 1) {
                    alert("Xóa game thành công");
                }
                else alert("Game này không thể xóa!");
            },
            error: function (xhr, textStatus, errorThrown) {
                console.log('loi ajax');
            }
        });
    }
}
function add() {
    location.assign('/admin/quanly_game/add');
}