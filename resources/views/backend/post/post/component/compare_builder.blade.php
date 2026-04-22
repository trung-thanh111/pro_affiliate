@php
    $compareProducts = isset($post) ? $post->post_products : collect();
    $compareSections = isset($post) ? $post->comparison_sections : collect();
@endphp

<div id="compare-builder-container"
    class="ibox mb20 {{ in_array(old('post_type', isset($post->post_type) ? $post->post_type : ''), ['compare', 'review']) ? '' : 'hidden' }}">
    <div class="ibox-title">
        <h5>Bảng so sánh sản phẩm</h5>
    </div>
    <div class="ibox-content">
        <!-- Bước 1: Quản lý Sản phẩm (Cột) -->
        <div class="product-management mb20">
            <h3 class="mb10">1. Chọn sản phẩm so sánh</h3>
            <div class="table-responsive">
                <table class="table table-bordered" id="product-compare-table">
                    <thead>
                        <tr>
                            <th style="width: 350px;">Sản phẩm</th>
                            <th>Tiêu đề tùy chỉnh</th>
                            <th>Badge</th>
                            <th class="text-center">Highlight</th>
                            <th class="text-center">Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($compareProducts->count() > 0)
                            @foreach ($compareProducts as $pp)
                                <tr class="product-item" data-id="{{ $pp->product_id }}">
                                    <td>
                                        <input type="hidden" name="compare_products[{{ $pp->product_id }}][product_id]"
                                            value="{{ $pp->product_id }}">
                                        <input type="hidden" name="compare_products[{{ $pp->product_id }}][temp_id]"
                                            value="{{ $pp->product_id }}">
                                        <div class="uk-flex uk-flex-top w-100">
                                            <span class="img-cover mr10 flex-none" style="width:64px;height:64px;">
                                                <img src="{{ $pp->product->image }}" alt="">
                                            </span>
                                            <div class="product-name-clamp"
                                                title="{{ $pp->product->languages->first()->pivot->name }}">
                                                {{ $pp->product->languages->first()->pivot->name }}
                                            </div>
                                        </div>
                                    </td>
                                    <td><input type="text"
                                            name="compare_products[{{ $pp->product_id }}][column_title]"
                                            value="{{ $pp->column_title }}" class="form-control"></td>
                                    <td><input type="text"
                                            name="compare_products[{{ $pp->product_id }}][badge_text]"
                                            value="{{ $pp->badge_text }}" class="form-control"
                                            placeholder="VD: Rẻ nhất"></td>
                                    <td class="text-center">
                                        <input type="checkbox"
                                            name="compare_products[{{ $pp->product_id }}][is_highlight]" value="1"
                                            {{ $pp->is_highlight ? 'checked' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-product"><i
                                                class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="uk-flex uk-flex-middle gap-10 mt10 w-100">
                <div class="flex-grow-1">
                    <select id="product-selector" class="form-control setupSelect2">
                        <option value="">-- Chọn sản phẩm để thêm vào danh sách so sánh --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" data-image="{{ $product->image }}"
                                data-name="{{ $product->languages->first()->pivot->name }}">
                                {{ $product->languages->first()->pivot->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="button" class="btn btn-success add-product-to-compare"
                    style="height: 34px; white-space: nowrap;">Thêm vào bảng</button>
            </div>
        </div>

        <hr>

        <!-- Bước 2: Quản lý Tiêu chí (Hàng) -->
        <div class="criteria-management">
            <h3 class="mb10">2. Thiết lập tiêu chí & Dữ liệu</h3>
            <div id="comparison-sections-wrapper">
                @if ($compareSections->count() > 0)
                    @foreach ($compareSections as $sIndex => $section)
                        <div class="section-item mb20 border p15 bg-light" data-index="{{ $sIndex }}">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between mb15">
                                <input type="text" name="compare_sections[{{ $sIndex }}][title]"
                                    value="{{ $section->title }}" class="form-control fw-bold" style="width: 300px;"
                                    placeholder="Tên nhóm tiêu chí (VD: Cấu hình)">
                                <button type="button" class="btn btn-danger btn-xs remove-section">Xóa nhóm</button>
                            </div>
                            <div class="rows-container">
                                <table class="table table-bordered bg-white">
                                    <thead>
                                        <tr class="row-header">
                                            <th style="width: 200px;">Tên tiêu chí</th>
                                            @foreach ($compareProducts as $pp)
                                                <th class="text-center product-col-head"
                                                    data-product-id="{{ $pp->product_id }}">
                                                    {{ $pp->product->languages->first()->pivot->name }}
                                                </th>
                                            @endforeach
                                            <th style="width: 50px;">Xóa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($section->rows as $rIndex => $row)
                                            <tr class="row-item" data-index="{{ $rIndex }}">
                                                <td>
                                                    <input type="text"
                                                        name="compare_sections[{{ $sIndex }}][rows][{{ $rIndex }}][label]"
                                                        value="{{ $row->label }}" class="form-control">
                                                    <input type="hidden"
                                                        name="compare_sections[{{ $sIndex }}][rows][{{ $rIndex }}][row_type]"
                                                        value="text">
                                                </td>
                                                @foreach ($compareProducts as $pp)
                                                    @php
                                                        $cell = $row->cells->where('post_product_id', $pp->id)->first();
                                                    @endphp
                                                    <td class="product-cell" data-product-id="{{ $pp->product_id }}">
                                                        <textarea name="compare_sections[{{ $sIndex }}][rows][{{ $rIndex }}][cells][{{ $pp->product_id }}]"
                                                            class="form-control" rows="1">{{ $cell->value_text ?? '' }}</textarea>
                                                    </td>
                                                @endforeach
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-warning btn-xs remove-row"><i
                                                            class="fa fa-times"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-primary btn-xs add-row">+ Thêm tiêu chí</button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <button type="button" class="btn btn-info add-section">Thêm nhóm tiêu chí mới</button>
        </div>
    </div>
</div>

<style>
    /* 1. Product Selection Section */
    #compare-builder-container .product-name-clamp {
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 13px;
        line-height: 1.4;
        max-height: 5.5em;
        flex: 1;
    }

    .flex-none {
        flex: none;
    }

    .flex-grow-1 {
        flex: 1;
        min-width: 0;
        /* Quan trọng: Cho phép container co lại nhỏ hơn nội dung bên trong */
    }

    /* Force Select2 to be full width and handle long text */
    #compare-builder-container .select2-container {
        width: 100% !important;
    }

    #compare-builder-container .select2-selection__rendered {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding-right: 25px !important;
    }

    #compare-builder-container .uk-flex.gap-10 {
        width: 100%;
        display: flex;
        align-items: center;
    }

    /* 2. Criteria Section (Screenshot Style) */
    #compare-builder-container .section-item {
        border-radius: 4px;
        border: 1px solid #e7eaec;
        margin-bottom: 20px;
        padding: 15px;
        background: #fdfdfd;
    }

    #compare-builder-container .bg-light {
        background-color: #f9f9f9 !important;
    }

    #compare-builder-container .table thead th {
        background: #f5f5f6;
        color: #676a6c;
        text-transform: none;
        font-weight: 600;
        border-bottom: 1px solid #e7eaec !important;
    }

    #compare-builder-container .product-col-head {
        font-size: 12px;
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    #compare-builder-container textarea {
        resize: vertical;
        min-height: 38px;
        border: 1px solid #e5e6e7;
    }

    #compare-builder-container .form-control:focus {
        border-color: #1ab394;
    }

    /* Button Colors from Screenshot */
    .btn-danger.remove-section {
        background-color: #f05a5b;
        border-color: #f05a5b;
        color: #FFFFFF;
    }

    .btn-warning.remove-row {
        background-color: #f8ac59;
        border-color: #f8ac59;
        color: #FFFFFF;
        padding: 2px 8px;
    }

    .btn-primary.add-row {
        background-color: #1abc9c;
        border-color: #1abc9c;
        color: #FFFFFF;
        font-weight: 600;
    }

    .gap-10 {
        gap: 10px;
    }

    .img-cover img {
        object-fit: cover;
        width: 100%;
        height: 100%;
    }
</style>

<script>
    $(document).ready(function() {
        // Toggle Builder dựa trên post_type
        $(document).on('change', 'select[name="post_type"]', function() {
            let val = $(this).val();
            if (val == 'compare' || val == 'review') {
                $('#compare-builder-container').removeClass('hidden');
            } else {
                $('#compare-builder-container').addClass('hidden');
            }
        });

        let currentPostType = $('select[name="post_type"]').val();
        if (currentPostType == 'compare' || currentPostType == 'review') {
            $('#compare-builder-container').removeClass('hidden');
        }

        // Thêm sản phẩm vào bảng
        $('.add-product-to-compare').on('click', function() {
            let select = $('#product-selector');
            let productId = select.val();
            if (!productId) return;

            if ($(`#product-compare-table tr[data-id="${productId}"]`).length > 0) {
                alert('Sản phẩm này đã có trong danh sách');
                return;
            }

            let name = select.find(':selected').data('name');
            let image = select.find(':selected').data('image');

            let productHtml = `
                <tr class="product-item" data-id="${productId}">
                    <td>
                        <input type="hidden" name="compare_products[${productId}][product_id]" value="${productId}">
                        <input type="hidden" name="compare_products[${productId}][temp_id]" value="${productId}">
                        <div class="uk-flex uk-flex-top w-100">
                            <span class="img-cover mr10 flex-none" style="width:64px;height:64px;"><img src="${image}" alt=""></span>
                            <div class="product-name-clamp" title="${name}">${name}</div>
                        </div>
                    </td>
                    <td><input type="text" name="compare_products[${productId}][column_title]" class="form-control"></td>
                    <td><input type="text" name="compare_products[${productId}][badge_text]" class="form-control"></td>
                    <td class="text-center"><input type="checkbox" name="compare_products[${productId}][is_highlight]" value="1"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-product"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            `;
            $('#product-compare-table tbody').append(productHtml);

            $('.rows-container table').each(function() {
                let table = $(this);
                table.find('.row-header th:last').before(`
                    <th class="text-center product-col-head" data-product-id="${productId}" title="${name}">${name}</th>
                `);
                table.find('tbody tr').each(function() {
                    let row = $(this);
                    let sIdx = row.closest('.section-item').data('index');
                    let rIdx = row.data('index');
                    row.find('td:last').before(`
                        <td class="product-cell" data-product-id="${productId}">
                            <textarea name="compare_sections[${sIdx}][rows][${rIdx}][cells][${productId}]" class="form-control" rows="1"></textarea>
                        </td>
                    `);
                });
            });
        });

        // Xóa sản phẩm
        $(document).on('click', '.remove-product', function() {
            let row = $(this).closest('tr');
            let productId = row.data('id');
            row.remove();
            $(`.product-col-head[data-product-id="${productId}"]`).remove();
            $(`.product-cell[data-product-id="${productId}"]`).remove();
        });

        // Thêm Section
        $('.add-section').on('click', function() {
            let sIdx = $('#comparison-sections-wrapper .section-item').length;
            let productHeaders = '';
            let productCells = '';
            $('#product-compare-table tbody tr').each(function() {
                let pId = $(this).data('id');
                let pName = $(this).find('.product-name-clamp').attr('title');
                productHeaders +=
                    `<th class="text-center product-col-head" data-product-id="${pId}" title="${pName}">${pName}</th>`;
                productCells +=
                    `<td class="product-cell" data-product-id="${pId}"><textarea name="compare_sections[${sIdx}][rows][0][cells][${pId}]" class="form-control" rows="1"></textarea></td>`;
            });

            let sectionHtml = `
                <div class="section-item mb20 border p15 bg-light" data-index="${sIdx}">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between mb15">
                        <input type="text" name="compare_sections[${sIdx}][title]" class="form-control fw-bold" style="width: 300px;" placeholder="Tên nhóm tiêu chí">
                        <button type="button" class="btn btn-danger btn-xs remove-section">Xóa nhóm</button>
                    </div>
                    <div class="rows-container">
                        <table class="table table-bordered bg-white">
                            <thead>
                                <tr class="row-header">
                                    <th style="width: 200px;">Tên tiêu chí</th>
                                    ${productHeaders}
                                    <th style="width: 50px;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="row-item" data-index="0">
                                    <td>
                                        <input type="text" name="compare_sections[${sIdx}][rows][0][label]" class="form-control">
                                        <input type="hidden" name="compare_sections[${sIdx}][rows][0][row_type]" value="text">
                                    </td>
                                    ${productCells}
                                    <td class="text-center">
                                        <button type="button" class="btn btn-warning btn-xs remove-row"><i class="fa fa-times"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-primary btn-xs add-row">+ Thêm tiêu chí</button>
                    </div>
                </div>
            `;
            $('#comparison-sections-wrapper').append(sectionHtml);
        });

        // Thêm Row
        $(document).on('click', '.add-row', function() {
            let container = $(this).closest('.rows-container');
            let section = $(this).closest('.section-item');
            let sIdx = section.data('index');
            let rIdx = container.find('tbody tr').length;

            let productCells = '';
            $('#product-compare-table tbody tr').each(function() {
                let pId = $(this).data('id');
                productCells +=
                    `<td class="product-cell" data-product-id="${pId}"><textarea name="compare_sections[${sIdx}][rows][${rIdx}][cells][${pId}]" class="form-control" rows="1"></textarea></td>`;
            });

            let rowHtml = `
                <tr class="row-item" data-index="${rIdx}">
                    <td>
                        <input type="text" name="compare_sections[${sIdx}][rows][${rIdx}][label]" class="form-control">
                        <input type="hidden" name="compare_sections[${sIdx}][rows][${rIdx}][row_type]" value="text">
                    </td>
                    ${productCells}
                    <td class="text-center">
                        <button type="button" class="btn btn-warning btn-xs remove-row"><i class="fa fa-times"></i></button>
                    </td>
                </tr>
            `;
            container.find('tbody').append(rowHtml);
        });

        $(document).on('click', '.remove-section', function() {
            if (confirm('Bạn có chắc muốn xóa nhóm tiêu chí này?')) {
                $(this).closest('.section-item').remove();
            }
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });
    });
</script>
