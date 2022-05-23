var current_page = 1;
function topage(i) {
    let keyword = $('#search').val();
    let data = {};
    if (keyword == '') data = {page: i};
    else data = {keyword:keyword,page:i};
    $.ajax({
        url: `/admin/quanly_taikhoangame`,
        data: data,
        type: 'GET',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            if (data.length==1 && i > 1) {
                topage(i-1);
            }
            else {
                $('#list_tkgame').html(danhsach_tkgame(data));
                $('#pagination').html(add_pagination(data[0], i))
                current_page = i;
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}

function danhsach_tkgame(data) {
    var content = `<table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>STT</th>
                <th>Game</th>
                <th>Tên đăng nhập</th>
                <th>Ingame</th>
                <th>Mô tả</th>
                <th>Giá</th>
                <th>Hành động</th>
            </tr>
            </thead>
            <tbody>`;
    for (i = 1; i < data.length; i++) {
        let tkgame = data[i];
        var id = tkgame['id'];
        content += `<tr id="` + ('tkgame' + tkgame['id']) + `">
                <th scope="row">` + i + `</th>
                <td>` + tkgame['tengame'] + `</td>
                <td>` + tkgame['username'] + `</td>
                <td>` + tkgame['ingame'] + `</td>
                <td>` + (tkgame['description'].length > 25 ? (tkgame['description'].slice(0,25)+"..."): tkgame['description']) + `</td>
                <td>` + tkgame['gia'] + `</td>
                <td>
                    <a href="/admin/quanly_taikhoangame/edit/` + id + `"><button class="btn btn-primary btn-sm">Sửa</button></a>
                    <button class="btn btn-danger btn-sm" onclick="delete_tkgame(` + id + `)">Xóa</button>
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
    $.ajax({
        url: `/admin/quanly_taikhoangame`,
        data: {keyword: keyword, page: 1},
        type: 'GET',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            if (data.length == 1) {
                $('#list_tkgame').html('<h3 align="center" style="margin-top: 20%;">Không tìm thấy kết quả!</h3>');
                $('#pagination').html('');
            }
            else {
                $('#list_tkgame').html(danhsach_tkgame(data));
                $('#pagination').html(add_pagination(data[0],1))
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}
// Hàm xóa game
function delete_tkgame(id) {
    var question = confirm("Bạn chắc chắn muốn xóa tài khoản game này?");
    if (question) {
        $.ajax({
            url: `/admin/quanly_taikhoangame/delete/` + id,
            type: 'POST',
            dataType: 'json',
            async: true,

            success: function (data) {
                topage(current_page);
                if (data['status'] == 1) {
                    alert("Xóa tài khoản game thành công");
                }
                else alert("Tài khoản game này không thể xóa!");
            },
            error: function (xhr, textStatus, errorThrown) {
                console.log('loi ajax');
            }
        });
    }
}
function button_password() {
    var password = document.getElementById('password');
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
function show_image(id) {
    $.ajax({
        url: `/admin/quanly_taikhoangame/images/` + id,
        type: 'POST',
        dataType: 'json',
        async: true,

        success: function (data) {
            let content = "<div class='row'>";
            console.log(data);
            if (data.length > 0) {
                for (i=0;i<data.length;i++) {
                    console.log(data[i]);
                    let img = data[i];
                    content += `<div id="img-container`+img['id']+`" class="col-12 col-md-3 col-lg-3">
                                    <img src="`+ img['link'] +`" style="width: 100%; height: 60%; border: 1px solid lightgray;">
                                    <i class="fa fa-times-circle" style="position: relative; right: -85%; top: -70%;" aria-hidden="true" onclick="delete_img(`+img['id']+`)"></i>    
                                </div>\n`;
                }
                content += `</div>\n`;
            }
            else content = "Không có ảnh!";
            $('#add-image').empty();
            $('#img-edit').html('<br><div>'+ content +'</div>');
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}
function delete_img(id) {
    $.ajax({
        url: `/admin/quanly_taikhoangame/delete-image/` + id,
        type: 'POST',
        dataType: 'json',
        async: true,

        success: function (data) {
            let img_container = document.getElementById('img-container'+id);
            img_container.remove();
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}
function show_form_add_image() {
    let img_edit = $('#add-image');
    $('#img-edit').empty();
    img_edit.html(`<lable for="file">Ảnh:</lable>
                   <input type="file" name="file[]" id="file" class="form-control" multiple>`);
}

function add() {
    location.assign('/admin/quanly_taikhoangame/add');
}