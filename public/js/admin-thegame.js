var current_page = 1;
function topage(i) {
    let keyword = $('#search').val();
    let data = {};
    if (keyword == '') data = {page: i};
    else data = {keyword:keyword,page:i};
    $.ajax({
        url: `/admin/quanly_thegame`,
        data: data,
        type: 'GET',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            if (data.length == 1) {
                topage(i-1);
            }
            else {
                $('#list_thegame').html(danhsach_thegame(data));
                $('#pagination').html(add_pagination(data[0],i));
                current_page = i;
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}
function danhsach_thegame(data,page) {
    var content = `<table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>STT</th>
                <th>Tên game</th>
                <th>Số seri</th>
                <th>Mã số thẻ</th>
                <th>Mệnh giá</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
            </thead>
            <tbody>`;
    for (i = 1; i < data.length; i++) {
        thegame = data[i];
        var id = thegame['id'];
        content += `<tr id="` + ('thegame' + thegame['id']) + `">
                <th scope="row">` + i + `</th>
                <td>` + thegame['tengame'] + `</td>
                <td>` + thegame['seri'] + `</td>
                <td>` + thegame['card_number'] + `</td>
                <td>` + thegame['gia'] + `</td>\n`;
        if (thegame['status'] == 1) content += `<td>Chưa bán</td>\n<td>
                    <a href="/admin/quanly_thegame/edit/` + id + `"><button class="btn btn-primary btn-sm">Sửa</button></a>
                    <button class="btn btn-danger btn-sm" onclick="delete_thegame(` + id + `)">Xóa</button>
                </td>\n`;
        else if (thegame['status'] == 0) content += `<td>Đã bán</td>\n<td>
                    <button class="btn btn-danger btn-sm" onclick="delete_thegame(` + id + `)">Xóa</button>
                </td>\n`;
    }
    content += `</tbody>\n</table>`;
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
        typingTimer = setTimeout(search_thegame, doneTypingInterval);
    });

    //on keydown, clear the countdown
    $input.on('keydown', function () {
        clearTimeout(typingTimer);
    });
});
// Tìm kiếm thẻ game
function search_thegame() {
    let keyword = $('#search').val();
    $.ajax({
        url: `/admin/quanly_thegame`,
        data: {keyword: keyword, page: 1},
        type: 'GET',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            if (data.length==1) {
                $('#list_thegame').html('<h3 align="center" style="margin-top: 20%;">Không tìm thấy kết quả!</h3>');
                $('#pagination').html('');
            }
            else {
                $('#list_thegame').html(danhsach_thegame(data));
                $('#pagination').html(add_pagination(data[0],1));
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}
// Hàm xóa thẻ game
function delete_thegame(id) {
    var question = confirm("Bạn chắc chắn muốn xóa thẻ game này?");
    if (question) {
        $.ajax({
            url: `/admin/quanly_thegame/delete/` + id,
            type: 'POST',
            dataType: 'json',
            async: true,

            success: function (data) {
                topage(current_page);
                alert("Xóa thẻ game thành công");
            },
            error: function (xhr, textStatus, errorThrown) {
                console.log('loi ajax');
            }
        });
    }
}

function add() {
    location.assign('/admin/quanly_thegame/add');
}