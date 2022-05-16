function xem_phanhoi(id) {
    if ($('#xemphanhoi'+id).attr('data-text')=='Xem phản hồi') {
        $.ajax({
            url: `/xemphanhoi/` + id,
            type: `GET`,
            dataType: `json`,
            async: true,

            success: function (data, status) {
                $('#xemphanhoi'+id).attr("data-text","Ẩn phản hồi");
                $('#xemphanhoi'+id).html(`<a href="javascript:void(0);" style="margin-left: -20px;" onclick="xem_phanhoi(`+ id +`)">
                                    <i class="fas fa-reply" style="transform: rotate(180deg);"></i>
                                    Ẩn phản hồi
                                </a>`);
                let content = ``;
                for (let i = 0; i < data.length; i++) {
                    let phanhoi = data[i];
                    content += add_phanhoi(phanhoi,1);
                }
                $('#phanhoi'+id).html(content);
            },

            error: function (xhr, textStatus, errorThrown) {
                console.log('Lỗi ajax');
            }
        });
    }
    else {
        $('#phanhoi'+id).empty();
        $('#xemphanhoi'+id).attr("data-text","Xem phản hồi");
        $('#xemphanhoi'+id).html(`<a href="javascript:void(0);" style="margin-left: -20px;" onclick="xem_phanhoi(`+ id +`)">
                                    <i class="fas fa-reply" style="transform: rotate(180deg);"></i>
                                    Xem phản hồi
                                </a>`);
    }
}
function show_form_traloi(id,nguoiduocphanhoi) {
    let form_id = `input-traloi`+id;
    let form = `<input class="form-control" style="border-radius: 20px;width: 85%; display: inline-block;" type="text" name="traloi" id="`+form_id+`" onkeyup="submit_phanhoi(`+id+`)" placeholder="Trả lời `+nguoiduocphanhoi+`">
                <i class="fas fa-paper-plane" style="font-size: 150%; transform: rotate(45deg);" onclick="submit_phanhoi(`+id+`,1)"></i>
                <a href="javascript:void(0);" style="margin-left: 10px;" onclick="hide_form_traloi(`+ id +`)">Hide</a>`;
    $('#form-traloi'+id).html(form);
}
function hide_form_traloi(id) {
    $('#form-traloi'+id).empty();
}
function submit_phanhoi(id,click) {
    var current_user_id = $('#current_user').val();
    let form_id = `#input-traloi` + id;
    if (event.keyCode == 13 || event.which == 13 || click == 1) {
        let text = $(form_id).val();
        // trả lời bình luận
        $.ajax({
            url: `/traloibinhluan/` + id,
            data: {content: text, current_user_id: current_user_id},
            type: `POST`,
            dataType: `json`,
            async: true,

            success: function (data, status) {
                console.log(data['sophanhoi_parent']);
                if (data['sophanhoi_parent'] == 1) {
                    $('#xemphanhoi'+id).html(`<a href="javascript:void(0);" style="margin-left: -20px;" onclick="xem_phanhoi(`+ id +`)">
                                    <i class="fas fa-reply" style="transform: rotate(180deg);"></i>
                                    Ẩn phản hồi
                                </a>`);
                    $('#xemphanhoi'+id).attr('data-text','Ẩn phản hồi');
                }
                else if (data['sophanhoi_parent'] > 1 && $('#xemphanhoi'+id).attr('data-text')=='Xem phản hồi') {
                    $('#xemphanhoi'+id).html(`<a href="javascript:void(0);" style="margin-left: -20px;" onclick="xem_phanhoi(`+ id +`)">
                                    <i class="fas fa-reply" style="transform: rotate(180deg);"></i>
                                    Xem thêm phản hồi
                                </a>`);
                }
                $(form_id).val("");
                let content = add_phanhoi(data);
                if ($('#phanhoi'+id).html() == "") {
                    $('#phanhoi' + id).html(content);
                }
                else {
                    $('#phanhoi' + id).append(content);
                }
            },

            error: function (xhr, textStatus, errorThrown) {
                console.log('Lỗi ajax');
            }
        });
    }
}
function binhluan(nhiemvu_id,click) {
    var current_user_id = $('#current_user').val();
    if (event.keyCode == 13 || click == 1) {
        let text = $('#form-binhluan').val();
        // trả lời bình luận
        $.ajax({
            url: `/binhluan/` + nhiemvu_id,
            data: {content: text, current_user_id: current_user_id},
            type: `POST`,
            dataType: `json`,
            async: true,

            success: function (data, status) {
                $('#form-binhluan').val("");
                let content = add_binhluan(data);
                if ($('#list_binhluan').html() == "") {
                    $('#list_binhluan').html(content);
                }
                else {
                    $('#list_binhluan').append(content);
                }
            },

            error: function (xhr, textStatus, errorThrown) {
                console.log('Lỗi ajax');
            }
        });
    }
}
function add_binhluan(phanhoi,status) {
    let content = `<div id="binhluan`+ phanhoi['id'] +`" class="comment">
                        <i class="fas fa-user"></i> <strong>`+ phanhoi['hoten'] +`</strong>
                        <br>
                        <p style="margin: 0px 0px 0px 10px;">`+ phanhoi['binhluan'] +`</p>
                        <a style="margin-left: 10px;" href="javascript:void(0);" onclick="show_form_traloi(`+ phanhoi['id'] +`,'`+ phanhoi['hoten'] +`')">Trả lời</a>
                        <a style="margin-left: 10px;" href="javascript:void(0);" onclick="xoa_binhluan(`+ phanhoi['id'] +`)">Xóa</a>
                        <div id="form-traloi`+ phanhoi['id'] +`"></div>`;
    content += `<div id="xemphanhoi`+ phanhoi['id'] +`" data-text="Xem phản hồi">`;
    if (phanhoi['sophanhoi'] > 0 || status > 0)
        content += `<a href="javascript:void(0);" style="margin-left: -20px;" onclick="xem_phanhoi(`+ phanhoi['id'] +`)">
                        <i class="fas fa-reply" style="transform: rotate(180deg);"></i> 
                        Xem phản hồi
                    </a>`;
    content += `</div><div id="phanhoi`+ phanhoi['id'] +`" class="phanhoi"></div>
                    </div>`;
    return content;
}
function add_phanhoi(phanhoi) {
    let content = `<div id="binhluan`+ phanhoi['id'] +`" class="comment feedback">
                        <i class="fas fa-user"></i> <strong>`+ phanhoi['hoten'] +`</strong>
                        <br>
                        <p style="margin: 0px 0px 0px 10px;">`+ phanhoi['binhluan'] +`</p>
                        <a style="margin-left: 10px;" href="javascript:void(0);" onclick="show_form_traloi(`+ phanhoi['id'] +`,'`+ phanhoi['hoten'] +`')">Trả lời</a>`;
    if ($('#current_user').val() == phanhoi['nguoiduocphanhoi']) {
        content += `<a style="margin-left: 10px;" href="javascript:void(0);" onclick="xoa_binhluan(`+ phanhoi['id'] +`)">Xóa</a>`;
    }
    content += `<div id="form-traloi`+ phanhoi['id'] +`"></div>`;
    content += `<div id="xemphanhoi`+ phanhoi['id'] +`" data-text="Xem phản hồi">`;
    if (phanhoi['sophanhoi'] > 0 || status > 0)
        content += `<a href="javascript:void(0);" style="margin-left: -20px;" onclick="xem_phanhoi(`+ phanhoi['id'] +`)">
                        <i class="fas fa-reply" style="transform: rotate(180deg);"></i> 
                        Xem phản hồi
                    </a>`;
    content += `</div><div id="phanhoi`+ phanhoi['id'] +`" class="phanhoi"></div>
                    </div>`;
    return content;
}
function xoa_binhluan(id) {
    $.ajax({
        url: `/xoabinhluan/` + id,
        type: `POST`,
        dataType: `json`,
        async: true,

        success: function (data, status) {
            if (data['sophanhoi_parent'] == 0) {
                $('#xemphanhoi'+data['id_parent']).empty();
            }
            $('#binhluan'+id).remove();
        },

        error: function (xhr, textStatus, errorThrown) {
            console.log('Lỗi ajax');
        }
    });
}

// Xử lý danh sách nhiệm vụ
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
    $.ajax({
        url: `/`,
        data: {keyword: keyword, page: 1},
        type: 'GET',
        dataType: 'json',
        async: true,

        success: function (data, status) {
            if (data.length == 1 ) {
                $('#list_nhiemvu').html('<h3 align="center" style="margin-top: 20%;">Không tìm thấy kết quả!</h3>');
            }
            else {
                $('#list_nhiemvu').html(danhsach_nhiemvu(data));
                $('#list_nhiemvu').append('<div id="pagination"></div>')
                $('#pagination').html(add_pagination(data[0],1));
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}

function danhsach_nhiemvu(data) {
    var content = `<table class="table table-hover">`;
    for (i = 1; i < data.length; i++) {
        nhiemvu = data[i];
        var id = nhiemvu['id'];
        var date = new Date(nhiemvu['ngaydang'].date);
        content += `<tr onclick="chitietnhiemvu(`+ nhiemvu['id'] +`)">
                        <td>
                            <div class="mission" id="mission`+ nhiemvu['id'] +`">
                                <h5><i class="fas fa-gamepad" style="font-size: 120%;"></i>` + (nhiemvu['tieude'].length > 120 ? (nhiemvu['tieude'].slice(0, 120) + "...") : nhiemvu['tieude']) + `</h5>
                                <h6><em>Ngày ` + date.getDate() + `-` + (date.getMonth() + 1) + `-` + date.getFullYear() + ` | `+ nhiemvu['hoten'] +` | `+ nhiemvu['tengame'] +`</em></h6>
                                <p>Nội dung: `+ (nhiemvu['noidung'].length > 120 ? (nhiemvu['noidung'].slice(0, 120) + "...") : nhiemvu['noidung']) +`</p>
                            </div>
                        </td>
                    </tr>\n`;
    }
    content += `</table>`;
    return content;
}
var current_page = 1;
function topage(i) {
    let keyword = $('#search').val();
    let data = {};
    if (keyword == '') data = {page: i};
    else data = {keyword:keyword,page:i};
    $.ajax({
        url: `/`,
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
                $('#list_nhiemvu').append('<div id="pagination"></div>')
                $('#pagination').html(add_pagination(data[0],i));
                current_page = i;
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('loi ajax');
        }
    });
}
function chitietnhiemvu(id) {
    location.assign('/chitietnhiemvu/'+id);
}