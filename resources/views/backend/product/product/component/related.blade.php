<div class="ibox">
    <div class="ibox-title">
        <h5>Bài viết cùng danh mục</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <div class="hidden">
                        <input type="radio" name="model" value="Post" checked>
                    </div>
                    <div class="search-model-box">
                        <i class="fa fa-search"></i>
                        <input type="text" class="form-control search-model"
                            placeholder="Nhập tên bài viết để tìm kiếm..." data-model="Post">
                        <div class="ajax-search-result"></div>
                    </div>
                    <div class="search-model-result mt20">
                        @if (isset($model->posts) && count($model->posts))
                            @foreach ($model->posts as $post)
                                <div class="search-result-item" id="model-{{ $post->id }}"
                                    data-modelid="{{ $post->id }}">
                                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                        <div class="uk-flex uk-flex-middle">
                                            <span class="image img-cover"><img src="{{ $post->image }}"
                                                    alt=""></span>
                                            <span class="name">{{ $post->languages->first()->pivot->name }}</span>
                                            <div class="hidden">
                                                <input type="text" name="modelItem[id][]"
                                                    value="{{ $post->id }}">
                                                <input type="text" name="modelItem[name][]"
                                                    value="{{ $post->languages->first()->pivot->name }}">
                                                <input type="text" name="modelItem[image][]"
                                                    value="{{ $post->image }}">
                                                <input type="text" name="modelItem[description][]"
                                                    value="{{ $post->languages->first()->pivot->description }}">
                                            </div>
                                        </div>
                                        <div class="deleted">
                                            <svg class="svg-next-icon svg-next-icon-size-12" width="12"
                                                height="12">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                                    <path
                                                        d="M18.263 16l10.07-10.07c.625-.625.625-1.636 0-2.26s-1.638-.627-2.263 0L16 13.737 5.933 3.667c-.626-.624-1.637-.624-2.262 0s-.624 1.64 0 2.264L13.74 16 3.67 26.07c-.626.625-.626 1.636 0 2.26.312.313.722.47 1.13.47s.82-.157 1.132-.47l10.07-10.068 10.068 10.07c.312.31.722.468 1.13.468s.82-.157 1.132-.47c.626-.625.626-1.636 0-2.26L18.262 16z">
                                                    </path>
                                                </svg>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .search-model-box {
        position: relative;
        margin-top: 10px !important;
    }

    .search-model-box i {
        position: absolute;
        top: 10px;
        left: 10px;
        color: #999;
    }

    .search-model-box input {
        padding-left: 30px;
    }

    .ajax-search-result {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #ddd;
        z-index: 1000;
        display: none;
        max-height: 300px;
        overflow-y: auto;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .ajax-search-item {
        display: block;
        width: 100%;
        padding: 10px;
        border: none;
        background: none;
        text-align: left;
        border-bottom: 1px solid #eee;
        cursor: pointer;
    }

    .ajax-search-item:hover {
        background: #f9f9f9;
    }

    .search-result-item {
        background: #f8f9fa;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        transition: all 0.3s;
    }

    .search-result-item:hover {
        border-color: #1ab394;
        background: #fff;
    }

    .search-result-item .image {
        width: 50px;
        height: 50px;
        margin-right: 15px;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #eee;
    }

    .search-result-item .image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .search-result-item .name {
        font-weight: 600;
        color: #333;
    }

    .search-result-item .deleted {
        cursor: pointer;
        color: #ed5565;
        padding: 5px;
        border-radius: 50%;
        transition: background 0.2s;
    }

    .search-result-item .deleted:hover {
        background: #fdf2f2;
    }
</style>

<script>
    $(document).ready(function() {
        // Hiển thị gợi ý khi focus vào ô tìm kiếm
        $(document).on('focus', '.search-model', function() {
            let _this = $(this);
            let keyword = _this.val();
            let model = _this.data('model');
            let option = {
                model: model,
                keyword: keyword
            };

            // Nếu ô tìm kiếm trống, tải danh sách bài viết mới nhất
            if (keyword.length === 0) {
                $.ajax({
                    url: '/ajax/dashboard/findModelObject',
                    type: 'GET',
                    data: option,
                    dataType: 'json',
                    success: function(res) {
                        if (typeof HT !== 'undefined' && typeof HT.renderSearchResult ===
                            'function') {
                            let html = HT.renderSearchResult(res);
                            if (html.length) {
                                _this.siblings('.ajax-search-result').html(html).show();
                            } else {
                                _this.siblings('.ajax-search-result').html(html).hide();
                            }
                        }
                    }
                });
            }
        });
    });
</script>
