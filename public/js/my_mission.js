$(document).ready(function(){
    var typingTimer;
    var doneTypingInterval = 1000;
    var $input = $('#search');

    //on keyup, start the countdown
    $input.on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(search_nhiemvu, doneTypingInterval);
    });

    //on keydown, clear the countdown
    $input.on('keydown', function () {
        clearTimeout(typingTimer);
    });
});
function huydang(id) {
    if (confirm("Bạn chắc chắn muốn hủy đăng nhiệm vụ?")) {
        $.ajax({
            url: `/nhiemvucuatoi/cancel/` + id,
            type: `POST`,
            dataType: `json`,
            async: true,

            success: function (data, status) {
                let index = $('#nhiemvu' + id + '> th').text();
                $('#nhiemvu' + id).html(add_mission(data, index));
            },

            error: function (xhr, textStatus, errorThrown) {
                console.log('Lỗi ajax');
            }
        });
    }
}
function daxong(id) {
    if (confirm("Bạn chắc chắn nhiệm vụ đã được hoàn thành?")) {
        $.ajax({
            url: `/nhiemvucuatoi/complete/` + id,
            type: `POST`,
            dataType: `json`,
            async: true,

            success: function (data, status) {
                let index = $('#nhiemvu'+id+'>th').text();
                $('#nhiemvu'+id).html(add_mission(data,index));
            },

            error: function (xhr, textStatus, errorThrown) {
                console.log('Lỗi ajax');
            }
        });
    }
}
function add_mission(nhiemvu,index) {
    let date = new Date(nhiemvu['ngaydang'].date);
    let content = `<th scope="row">`+ index +`</th>
                <td>`+ (nhiemvu['tieude'].length > 20 ? (nhiemvu['tieude'].slice(0,20)+"...") : nhiemvu['tieude']) +`</td>
                <td>`+ nhiemvu['tengame'] +`</td>
                <td>`+ (nhiemvu['noidung'].length > 25 ? (nhiemvu['noidung'].slice(0,25)+"..."): nhiemvu['noidung'] ) +`</td>
                <td>`+ date.getDate()+`-`+ (date.getMonth()+1)+ `-` + date.getFullYear() +`</td>\n`;

    if (nhiemvu['trangthai'] == 0) {
        content += `<td>Chờ duyệt</td>
                <td>
                    <a href="/nhiemvucuatoi/edit/`+ nhiemvu['id'] +`"><button class="btn btn-primary">Sửa</button></a>
                    <button class="btn btn-warning" onclick="huydang(`+ nhiemvu['id'] +`)">Hủy đăng</button>
                </td>`;
    }
    else if (nhiemvu['trangthai'] == 1) {
        content += `<td>Đang đăng</td>
                    <td>
                        <a href="/nhiemvucuatoi/edit/`+ nhiemvu['id'] +`"><button class="btn btn-primary">Sửa</button></a>
                        <button class="btn btn-success" onclick="daxong(`+ nhiemvu['id'] +`)">Đã xong</button>
                    </td>`;
    }
    else if (nhiemvu['trangthai'] == -1) {
        content += `<td>Bị hủy</td>\n<td></td>`;
    }
    else if (nhiemvu['trangthai'] == -2) {
        content += `<td>Hết hạn</td>\n<td></td>`;
    }
    else if (nhiemvu['trangthai'] == -3) {
        content += `<td>Đã hoàn thành</td>\n<td></td>`;
    }
    return content;
}
function topage(i) {
    let keyword = $('#search').val();
    let data = {};
    if (keyword == '') data = {page: i};
    else data = {keyword:keyword,page:i};
    $.ajax({
        url: `/nhiemvucuatoi`,
        data: data,
        type: 'GET',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            $('#list_nhiemvu').html(add_list_mission(data));
            $('#pagination').html(add_pagination(data[0],i));
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}
function add_list_mission(data) {
    let content = `<table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>STT</th>
                    <th>Tiêu đề</th>
                    <th>Game</th>
                    <th>Nội dung</th>
                    <th>Ngày đăng</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
                </thead>
                <tbody>`;
    for (i=1;i<data.length;i++) {
        let nhiemvu = data[i];
        content += `<tr id="nhiemvu`+nhiemvu['id']+`">`;
        content += add_mission(nhiemvu,i);
        content += `</tr>\n`;
    }
    content += `</tbody>
        </table>`;
    return content;
}
// Tìm kiếm nhiemvu
function search_nhiemvu() {
    let keyword = $('#search').val();
    $.ajax({
        url: `/nhiemvucuatoi`,
        data: {keyword: keyword, page: 1},
        type: 'GET',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            if (data.length == 1) {
                $('#list_nhiemvu').html('<div class="row"><h3 style="margin: 10% auto;">Không tìm thấy kết quả!</h3></div>');
                $('#pagination').empty();
            }
            else {
                $('#list_nhiemvu').html(add_list_mission(data));
                $('#pagination').html(add_pagination(data[0],1));
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}