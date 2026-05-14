<div id="import-modal" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Review dữ liệu Import</h4>
                <small class="font-bold text-danger">Vui lòng gán danh mục cho các sản phẩm chưa xác định bên dưới.</small>
            </div>
            <div class="modal-body">
                <div class="row mb20">
                    <div class="col-lg-6">
                        <div class="form-group mb0">
                            <label class="control-label font-bold text-navy">Gán nhanh danh mục cho tất cả:</label>
                            <select id="select-all-catalogue" class="form-control select2">
                                <option value="">-- Chọn danh mục --</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle table-import-review">
                        <thead>
                            <tr>
                                <th class="text-center" width="50px">STT</th>
                                <th class="text-center" width="80px">Ảnh</th>
                                <th>Thông tin sản phẩm</th>
                                <th width="250px">Danh mục gán</th>
                            </tr>
                        </thead>
                        <tbody id="import-review-body">
                            <!-- JS render here -->
                        </tbody>
                    </table>
                </div>
                
                <div id="import-loading" style="display: none;" class="text-center py-4">
                    <div class="sk-spinner sk-spinner-wave">
                        <div class="sk-rect1"></div>
                        <div class="sk-rect2"></div>
                        <div class="sk-rect3"></div>
                        <div class="sk-rect4"></div>
                        <div class="sk-rect5"></div>
                    </div>
                    <p class="mt10 font-bold text-primary">Đang xử lý dữ liệu, vui lòng chờ...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Đóng</button>
                <button type="button" id="confirm-import-btn" class="btn btn-primary">Xác nhận & Import</button>
            </div>
        </div>
    </div>
</div>

<style>
    #import-modal .modal-body {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
        padding: 20px !important;
    }
    .select2-container--open {
        z-index: 9999999 !important;
    }
    .table-responsive {
        border: none !important;
    }
    .table-import-review thead th {
        position: sticky;
        top: -1px;
        background: #f5f5f6;
        z-index: 10;
        border-bottom: 2px solid #ddd !important;
    }
    .align-middle td {
        vertical-align: middle !important;
        padding: 10px 8px !important;
    }
    .product-info-name {
        font-weight: bold;
        color: #1a7bb9;
        margin-bottom: 3px;
        display: block;
        font-size: 14px;
        line-height: 1.4;
    }
    .product-info-price {
        font-size: 12px;
        color: #676a6c;
    }
    .has-error .select2-selection {
        border-color: #ed5565 !important;
    }
    .img-review {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border: 1px solid #eee;
        border-radius: 4px;
    }
</style>
