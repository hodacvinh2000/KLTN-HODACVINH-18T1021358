function activeNavLink(li) {
    $('#admin-main, #admin-user, #admin-game, #admin-cardgame, #admin-accountgame, #admin-mission').removeClass('active');
    li.addClass('active');
}

function add_pagination(data,page) {
    let num_page = data['num_page'];
    let pagination = `<ul class="pagination justify-content-center">
                    <li class="page-item">
                        <button class="page-link" id="previous-page" onclick="topage(`+ (page-1) +`)">Trước</button>
                    </li>`;
    // Số trang <= 5 thì chỉ cần hiển thị ra hết
    if (num_page <= 5) {
        for(i=1;i<=num_page;i++) pagination += renderPaginationButton(i);
    }
    // Số trang > 5 thì phải xử lý thêm
    else {
        if (page > 1) pagination += `<a href="#">...</a>\n`
        // Nếu num_page - page > 5 thì phải thêm ...
        if (num_page - page + 1 > 5) {
            for(i=page;i<page+5;i++) {
                pagination += renderPaginationButton(i);
            }
            pagination +=  `<li class="page-item">
                                <button class="page-link">...</button>
                            </li>\n`;
        }
        else {
            // Nếu num_page - page <= 5
            for (i=5-num_page+page-1;i>0;i--) pagination += renderPaginationButton(page-i);
            pagination += renderPaginationButton(page);
            for (i=page+1;i<=num_page;i++) pagination += renderPaginationButton(i);
        }
    }
    pagination += `<li class="page-item">
                      <button class="page-link" id="next-page" onclick="topage(`+ (page+1) +`)">Sau</button>
                   </li></ul>`;
    $('#pagination').html(pagination);
    $('#pagination-button'+page).parent().addClass('active');
    if (page == 1) $('#previous-page').parent().addClass('disabled');
    if (num_page == page) $('#next-page').parent().addClass('disabled');
}

// Add pagination button
function renderPaginationButton(i) {
    return `<li class="page-item">
                <button class="page-link"  id="pagination-button`+ i +`" onclick="topage(`+ i +`)" >`+ i +`</button>
            </li>\n`;
}
