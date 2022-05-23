var current_page = 1;
// Set delay thời gian tìm kiếm
$(document).ready(function(){
    activeNavLink($('#admin-user'));
    var typingTimer;
    var doneTypingInterval = 1000;
    var $input = $('#search');

    //on keyup, start the countdown
    $input.on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(search_user, doneTypingInterval);
    });

    //on keydown, clear the countdown
    $input.on('keydown', function () {
        clearTimeout(typingTimer);
    });
});
// Tìm kiếm user
function search_user() {
    let keyword = $('#search').val();
    $.ajax({
        url: `/admin/quanly_user`,
        data: {keyword: keyword, page: 1},
        type: 'GET',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            if (data.length == 1) {
                $('#list_user').html('<h3 align="center" style="margin-top: 20%;">Không tìm thấy kết quả!</h3>');
                $('#pagination').html('');
            }
            else {
                $('#list_user').html(danhsach_user(data, 1));
                add_pagination(data[0], 1);
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}
// Hàm khóa, mở khóa tài khoản của user
function lock_unlock(id) {
    $.ajax({
        url: `/admin/quanly_user/lock_unlock/` + id,
        type: 'POST',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            let stt = $('#user'+id+'> th').text();
            $('#user'+id).html(update_onerow(data[0],stt));
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}
// Convert list user => html
function danhsach_user(data,page) {
    let content = `<table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>STT</th>
                <th>Tên đăng nhập</th>
                <th>Họ tên</th>
                <th>Ngày sinh</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Giới tính</th>
                <th>Số dư</th>
                <th>Hành động</th>
            </tr>
            </thead>
            <tbody>`;
    for (i=1;i<data.length;i++) {
        user = data[i];
        let id = user['id'];
        let date = new Date(user['ngaysinh'].date);
        content += `<tr id="`+('user'+user['id'])+`">
                <th scope="row">`+i+`</th>
                <td>`+ user['tendangnhap'] +`</td>
                <td>`+ user['hoten'] +`</td>
                <td>`+ date.getDate()+`-`+ (date.getMonth()+1)+ `-` + date.getFullYear()+`</td>
                <td>`+ user['email'] +`</td>
                <td>`+ user['sodt'] +`</td>
                <td>`+ user['gioitinh'] +`</td>
                <td>`+ user['sodu'] +`</td>
                <td>
                    <a href="`+ ('/admin/quanly_user/edit/' + user['id']) +`"><button class="btn btn-primary btn-sm">Sửa</button></a>
                `;
        if (user['quyen'] == '-1')
        {
            content += `<button class="btn btn-success btn-sm" type="button" id="unlock-button" onclick="lock_unlock(`+id+`)">Mở khóa</button>
                         `;
            content +=`<button class="btn btn-danger btn-sm" onclick="delete_user(`+ user['id'] +`)">Xóa</button>\n</tr>`;
        }
        else
        {
            content += `<button class="btn btn-dark btn-sm" type="button" id="lock-button" onclick="lock_unlock(`+id+`)">Khóa</button>
                        `;
            content +=`<button class="btn btn-danger btn-sm" onclick="delete_user(`+ user['id'] +`)">Xóa</button>\n</tr>`;
        }
    }
    content += `</tbody>
        </table>`;
    return content;
}
// Convert 1 hàng user => html
function update_onerow(user,stt) {
    let id = user['id'];
    let date = new Date(user['ngaysinh'].date);
    let content = `<th scope="row">`+ stt +`</th>
                <td>`+ user['tendangnhap'] +`</td>
                <td>`+ user['hoten'] +`</td>
                <td>`+ date.getDate()+`-`+ (date.getMonth()+1)+ `-` + date.getFullYear()+`</td>
                <td>`+ user['email'] +`</td>
                <td>`+ user['sodt'] +`</td>
                <td>`+ user['gioitinh'] +`</td>
                <td>`+ user['sodu'] +`</td>
                <td>
                    <a href="`+ ('/admin/quanly_user/edit/' + user['id']) +`"><button class="btn btn-primary btn-sm">Sửa</button></a>
                `;
    if (user['quyen'] == '-1')
    {
        content += `<button class="btn btn-success btn-sm" type="button" id="unlock-button" onclick="lock_unlock(`+id+`)">Mở khóa</button>
                         `;
        content +=`<button class="btn btn-danger btn-sm" onclick="delete_user(`+ user['id'] +`)">Xóa</button>\n</tr>`;
    }
    else
    {
        content += `<button class="btn btn-dark btn-sm" type="button" id="lock-button" onclick="lock_unlock(`+id+`)">Khóa</button>
                        `;
        content +=`<button class="btn btn-danger btn-sm" onclick="delete_user(`+ user['id'] +`)">Xóa</button>\n`;
    }
    return content;
}
// Hàm hiện mật khẩu
function button_password() {
    var password = document.getElementById('matkhau');
    var button = document.getElementById('button-password');
    if (password.getAttribute('type') === 'password') {
        password.setAttribute('type','text');
        button.innerHTML = 'Ẩn mật khẩu';
    }
    else {
        password.setAttribute('type','password');
        button.innerHTML = 'Hiện mật khẩu';
    }
}
// Hàm xóa user
function delete_user(id) {
    let question = confirm("Bạn chắc chắn muốn xóa user này?");
    if (question) {
        $.ajax({
            url: `/admin/quanly_user/delete/` + id,
            type: 'POST',
            dataType: 'json',
            async: true,

            success: function (data) {
                topage(current_page);
                if (data['status']==1) alert("Xóa user thành công!");
                else alert("Không thể xóa người dùng này!");
            },
            error: function (xhr, textStatus, errorThrown) {
                console.log('loi ajax');
            }
        });
    }
}
function topage(i) {
    current_page = i;
    let keyword = $('#search').val();
    let data = {};
    if (keyword == '') data = {page: i};
    else data = {keyword:keyword,page:i};
    $.ajax({
        url: `/admin/quanly_user`,
        data: data,
        type: 'GET',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            if (data.length == 1 && i > 1) {
                topage(i-1);
            }
            else {
                $('#list_user').html(danhsach_user(data,i));
                add_pagination(data[0],i);
                current_page = i;
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}

function add() {
    location.assign('/admin/quanly_user/add');
}
