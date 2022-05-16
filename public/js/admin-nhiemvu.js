var current_page = 1;
function topage(i) {
    let keyword = $('#search').val();
    let data = {};
    if (keyword == '') data = {page: i};
    else data = {keyword:keyword,page:i};
    $.ajax({
        url: `/admin/quanly_nhiemvu`,
        data: data,
        type: 'GET',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            if (data.length==1 && i > 1) {
                topage(i-1);
            }
            else {
                $('#list_nhiemvu').html(danhsach_nhiemvu(data));
                $('#pagination').html(add_pagination(data[0],i))
                current_page = i;
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}
function danhsach_nhiemvu(data) {
    var content = `<table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>STT</th>
                <th>Tiêu đề</th>
                <th>Người đăng</th>
                <th>Tên game</th>
                <th>Nội dung</th>
                <th>Ngày đăng</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
            </thead>
            <tbody>`;
    for (i = 1; i < data.length; i++) {
        nhiemvu = data[i];
        var id = nhiemvu['id'];
        var date = new Date(nhiemvu['ngaydang'].date);
        content += `<tr id="nhiemvu` + nhiemvu['id'] + `" onclick="chitietnhiemvu(`+ nhiemvu['id'] +`)">
                <th scope="row">` + i + `</th>
                <td>` + (nhiemvu['tieude'].length > 20 ? (nhiemvu['tieude'].slice(0, 20) + "...") : nhiemvu['tieude']) + `</td>
                <td>` + nhiemvu['hoten'] + `</td>
                <td>` + nhiemvu['tengame'] + `</td>
                <td>` + (nhiemvu['noidung'].length > 25 ? (nhiemvu['noidung'].slice(0, 25) + "...") : nhiemvu['noidung']) + `</td>
                <td>` + date.getDate() + `-` + (date.getMonth() + 1) + `-` + date.getFullYear() + `</td>\n`;
        if (nhiemvu['trangthai'] == '0') content += `<td>Chờ duyệt</td>
                    <td>
                        <button class="btn btn-success btn-sm" onclick="duyet_nhiemvu(` + id + `)">Duyệt</button>
                        <button class="btn btn-warning btn-sm" onclick="huy_nhiemvu(` + id + `)">Hủy</button>
                    </td>\n`;
        else if (nhiemvu['trangthai'] == '1') content += `<td>Đang đăng</td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="delete_nhiemvu(` + id + `)">Xóa</button>
                    </td>`;
        else if (nhiemvu['trangthai'] == '-1') content += `<td>Bị hủy</td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="delete_nhiemvu(` + id + `)">Xóa</button>
                    </td>`;
        else if (nhiemvu['trangthai'] == '-2') content += `<td>Hết hạn</td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="delete_nhiemvu(` + id + `)">Xóa</button>
                    </td>`;
        else if (nhiemvu['trangthai'] == '-3') content += `<td>Đã hoàn thành</td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="delete_nhiemvu(` + id + `)">Xóa</button>
                    </td>`;
        content += `</tr>`;
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
        typingTimer = setTimeout(search_nhiemvu, doneTypingInterval);
    });

    //on keydown, clear the countdown
    $input.on('keydown', function () {
        clearTimeout(typingTimer);
    });
});
// Tìm kiếm nhiemvu
function search_nhiemvu() {
    let keyword = $('#search').val();
    console.log(keyword);
    $.ajax({
        url: `/admin/quanly_nhiemvu`,
        data: {keyword: keyword, page: 1},
        type: 'GET',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            if (data.length == 1 ) {
                $('#list_nhiemvu').html('<h3 align="center" style="margin-top: 20%;">Không tìm thấy kết quả!</h3>');
                $('#pagination').html('');
            }
            else {
                $('#list_nhiemvu').html(danhsach_nhiemvu(data));
                $('#pagination').html(add_pagination(data[0],1));
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}
// Cập nhật một hàng
function update_onerow(data,stt) {
    let nhiemvu = data;
    let id = nhiemvu['id'];
    let date = new Date(nhiemvu['ngaydang'].date);
    let content = `<th scope="row">` + stt + `</th>
                <td>` + (nhiemvu['tieude'].length > 20 ? (nhiemvu['tieude'].slice(0, 20) + "...") : nhiemvu['tieude']) + `</td>
                <td>` + nhiemvu['hoten'] + `</td>
                <td>` + nhiemvu['tengame'] + `</td>
                <td>` + (nhiemvu['noidung'].length > 25 ? (nhiemvu['noidung'].slice(0, 25) + "...") : nhiemvu['noidung']) + `</td>
                <td>` + date.getDate() + `-` + (date.getMonth() + 1) + `-` + date.getFullYear() + `</td>\n`;
    if (nhiemvu['trangthai'] == '0') content += `<td>Chờ duyệt</td>
                <td>
                    <button class="btn btn-success btn-sm" onclick="duyet_nhiemvu(` + id + `)">Duyệt</button>
                    <button class="btn btn-warning btn-sm" onclick="huy_nhiemvu(` + id + `)">Hủy</button>
                </td>\n`;
    else if (nhiemvu['trangthai'] == '1') content += `<td>Đang đăng</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="delete_nhiemvu(` + id + `)">Xóa</button>
                </td>`;
    else if (nhiemvu['trangthai'] == '-1') content += `<td>Bị hủy</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="delete_nhiemvu(` + id + `)">Xóa</button>
                </td>`;
    else if (nhiemvu['trangthai'] == '-2') content += `<td>Hết hạn</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="delete_nhiemvu(` + id + `)">Xóa</button>
                </td>`;
    else if (nhiemvu['trangthai'] == '-3') content += `<td>Đã hoàn thành</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="delete_nhiemvu(` + id + `)">Xóa</button>
                </td>`;
    return content;
}
// Hàm duyệt nhiệm vụ
function duyet_nhiemvu(id) {
    $.ajax({
        url: `/admin/quanly_nhiemvu/duyet_nhiemvu/` + id,
        type: 'POST',
        data: {status: 1},
        dataType: 'json',
        async: true,

        success: function (data, status) {
            let stt = $('#nhiemvu'+id+'> th').text();
            $('#nhiemvu'+id).html(update_onerow(data[0],stt));
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}
// Hàm hủy nhiệm vụ
function huy_nhiemvu(id) {
    $.ajax({
        url: `/admin/quanly_nhiemvu/duyet_nhiemvu/` + id,
        type: 'POST',
        data: {status: -1},
        dataType: 'json',
        async: true,

        success: function (data, status) {
            let stt = $('#nhiemvu'+id+'> th').text();
            $('#nhiemvu'+id).html(update_onerow(data[0],stt));
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}
// Hàm xóa nhiệm vụ
function delete_nhiemvu(id) {
    var question = confirm("Bạn chắc chắn muốn xóa nhiệm vụ này?");
    if (question)
    {
        $.ajax({
            url: `/admin/quanly_nhiemvu/delete/` + id,
            type: 'POST',
            dataType: 'json',
            async: true,

            success: function (data, status) {
                topage(current_page);
                if (data['status']==1) alert("Xóa nhiemvu thành công!");
            },
            error: function (xhr, textStatus, errorThrown) {
                console.log('loi ajax');
            }
        });
    }
}
function chitietnhiemvu(id) {
    location.assign('/admin/quanly_nhiemvu/edit/'+id);
}