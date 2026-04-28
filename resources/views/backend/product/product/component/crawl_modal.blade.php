<div id="crawl-modal" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Crawl dữ liệu sản phẩm</h4>
                <small class="font-bold">Nhập link từ TikTok, Shopee hoặc Lazada để lấy thông tin tự động.</small>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Link sản phẩm</label> 
                            <div class="input-group">
                                <input type="text" id="crawl-url" placeholder="https://tiktok.com/..." class="form-control">
                                <span class="input-group-btn">
                                    <button type="button" id="start-crawl-btn" class="btn btn-primary">Quét dữ liệu</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div id="crawl-preview" style="display: none;">
                    <div class="row">
                        <div class="col-sm-4">
                            <img id="preview-image" src="" class="img-responsive thumbnail" style="max-height: 200px; width: 100%; object-fit: cover;">
                        </div>
                        <div class="col-sm-8">
                            <div class="form-group">
                                <label>Tên sản phẩm</label>
                                <input type="text" id="preview-name" class="form-control">
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Giá gốc (VNĐ)</label>
                                        <input type="text" id="preview-price" class="form-control int">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Giá khuyến mãi (VNĐ)</label>
                                        <input type="text" id="preview-price-sale" class="form-control int">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Mô tả ngắn</label>
                        <textarea id="preview-description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div id="crawl-loading" style="display: none;" class="text-center py-4">
                    <div class="sk-spinner sk-spinner-wave">
                        <div class="sk-rect1"></div>
                        <div class="sk-rect2"></div>
                        <div class="sk-rect3"></div>
                        <div class="sk-rect4"></div>
                        <div class="sk-rect5"></div>
                    </div>
                    <p class="mt10">Đang quét dữ liệu, vui lòng chờ...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Đóng</button>
                <button type="button" id="save-crawled-btn" class="btn btn-primary" style="display: none;">Lưu vào hệ thống</button>
            </div>
        </div>
    </div>
</div>
