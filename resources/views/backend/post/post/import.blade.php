@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['index']['title']])

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <!-- Configuration and Upload Panel -->
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Cấu Hình Import Bài Viết & Danh Mục (JSON > 100MB)</h5>
                </div>
                <div class="ibox-content">
                    <form id="importForm" class="form-horizontal">
                        <!-- Dropzone File Upload -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Chọn File JSON</label>
                            <div class="col-sm-9">
                                <div class="file-upload-wrapper" style="border: 2px dashed #1ab394; border-radius: 6px; padding: 40px 20px; text-align: center; background: #f9fbfd; cursor: pointer; transition: all 0.3s;" id="dropzone">
                                    <i class="fa fa-file-code-o" style="font-size: 50px; color: #1ab394; margin-bottom: 15px;"></i>
                                    <h4 style="margin: 5px 0; font-weight: 600;">Kéo & thả file JSON vào đây</h4>
                                    <p style="color: #888; font-size: 12px; margin-bottom: 15px;">Hỗ trợ các file dung lượng cực lớn (lên tới 1GB)</p>
                                    <input type="file" id="jsonFile" accept=".json" style="display: none;">
                                    <div id="fileInfo" class="text-navy" style="display: none; font-weight: bold; font-size: 14px; margin-top: 10px;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Force Update Config -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Ghi đè trùng lặp</label>
                            <div class="col-sm-9">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="forceUpdate" value="1"> 
                                        <strong>Cập nhật thông tin bài viết nếu đã tồn tại slug (canonical)</strong>
                                    </label>
                                </div>
                                <span class="help-block m-b-none text-muted" style="font-size: 11px; margin-top: 5px;">
                                    * Tắt tính năng này giúp tăng tốc độ ghi cơ sở dữ liệu lên gấp 10 lần (bỏ qua bản ghi trùng).
                                </span>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-sm-9 col-sm-offset-3">
                                <button type="button" id="startImportBtn" class="btn btn-primary btn-block btn-lg" disabled style="font-weight: bold; border-radius: 4px; box-shadow: 0 2px 4px rgba(26,179,148,0.2);">
                                    <i class="fa fa-play mr5"></i> Bắt Đầu Tải Lên & Xử Lý
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Progress and Status Dashboard Panel -->
        <div class="col-lg-6">
            <div class="ibox float-e-margins" id="progressBox" style="display: none;">
                <div class="ibox-title">
                    <h5>Trạng Thái Xử Lý Thực Tế</h5>
                    <span class="label label-warning pull-right" id="statusLabel">Đang kết nối...</span>
                </div>
                <div class="ibox-content" style="padding: 25px;">
                    <!-- Pulse Loader for Server Side -->
                    <div id="processingLoader" style="display: none; text-align: center; padding: 20px 0;">
                        <div class="sk-spinner sk-spinner-wave" style="margin-bottom: 15px;">
                            <div class="sk-rect1" style="background-color: #1ab394;"></div>
                            <div class="sk-rect2" style="background-color: #1ab394;"></div>
                            <div class="sk-rect3" style="background-color: #1ab394;"></div>
                            <div class="sk-rect4" style="background-color: #1ab394;"></div>
                            <div class="sk-rect5" style="background-color: #1ab394;"></div>
                        </div>
                        <h4 class="text-navy" style="font-weight: bold; margin-bottom: 5px; animation: pulse 1.5s infinite;">Hệ thống đang xử lý file JSON & cập nhật Database...</h4>
                        <p class="text-muted" style="font-size: 12px;">Đang thực hiện bulk inserts hàng chục nghìn bản ghi cùng lúc. Vui lòng không đóng trình duyệt!</p>
                    </div>

                    <!-- Progress Bar for File Uploading -->
                    <div id="uploadProgressContainer" style="margin-bottom: 25px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <strong style="font-size: 14px;" id="progressTitle">Tiến trình tải lên: <span id="percentVal">0</span>%</strong>
                            <span class="text-muted" id="uploadSpeed">-- MB/s</span>
                        </div>
                        <div class="progress progress-striped active" style="height: 25px; border-radius: 4px; overflow: hidden; margin-bottom: 0;">
                            <div id="progressBar" class="progress-bar progress-bar-success" role="progressbar" style="width: 0%; line-height: 25px; font-weight: bold; background-image: linear-gradient(135deg, #1ab394 0%, #18a689 100%);">0%</div>
                        </div>
                    </div>

                    <!-- Complete Result Details (Initially Hidden) -->
                    <div id="resultContainer" style="display: none; border-top: 1px solid #e7eaec; padding-top: 20px;">
                        <h3 class="text-success" style="font-weight: bold; text-align: center; margin-bottom: 20px;">
                            <i class="fa fa-check-circle" style="font-size: 30px; vertical-align: middle; margin-right: 8px;"></i>
                            IMPORT HOÀN TẤT THÀNH CÔNG!
                        </h3>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr style="background: #f5f5f6;">
                                    <th style="width: 70%;">Thông số thống kê</th>
                                    <th class="text-center" style="width: 30%;">Số lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Danh mục mới được tạo</strong></td>
                                    <td class="text-center text-navy font-bold" id="resCatCreated">0</td>
                                </tr>
                                <tr>
                                    <td><strong>Danh mục hiện tại được cập nhật</strong></td>
                                    <td class="text-center text-warning font-bold" id="resCatUpdated">0</td>
                                </tr>
                                <tr>
                                    <td><strong>Bài viết mới được import</strong></td>
                                    <td class="text-center text-success font-bold" id="resPostCreated">0</td>
                                </tr>
                                <tr>
                                    <td><strong>Bài viết trùng lặp được cập nhật</strong></td>
                                    <td class="text-center text-info font-bold" id="resPostUpdated">0</td>
                                </tr>
                                <tr>
                                    <td><strong>Bài viết trùng lặp được bỏ qua</strong></td>
                                    <td class="text-center text-muted font-bold" id="resPostSkipped">0</td>
                                </tr>
                                <tr style="background: #fbfbfb;">
                                    <td><strong>Tổng thời gian thực thi</strong></td>
                                    <td class="text-center font-bold" id="resTimeElapsed">--</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Live Log Terminal console -->
                    <div style="margin-top: 15px;">
                        <strong class="text-muted" style="display: block; margin-bottom: 5px;">Hệ thống nhật ký thời gian thực:</strong>
                        <div id="logConsole" style="background: #151515; color: #39ff14; font-family: 'Courier New', monospace; font-size: 11px; padding: 15px; border-radius: 4px; height: 180px; overflow-y: auto; box-shadow: inset 0 0 15px #000; border: 1px solid #222;">
                            [SYSTEM] Sẵn sàng nhận file JSON của bạn.<br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0% { opacity: 0.6; }
        50% { opacity: 1; }
        100% { opacity: 0.6; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('jsonFile');
        const fileInfo = document.getElementById('fileInfo');
        const startBtn = document.getElementById('startImportBtn');
        
        const progressBox = document.getElementById('progressBox');
        const uploadProgressContainer = document.getElementById('uploadProgressContainer');
        const processingLoader = document.getElementById('processingLoader');
        const progressBar = document.getElementById('progressBar');
        const percentVal = document.getElementById('percentVal');
        const progressTitle = document.getElementById('progressTitle');
        const uploadSpeed = document.getElementById('uploadSpeed');
        const statusLabel = document.getElementById('statusLabel');
        const logConsole = document.getElementById('logConsole');
        
        // Result elements
        const resultContainer = document.getElementById('resultContainer');
        const resCatCreated = document.getElementById('resCatCreated');
        const resCatUpdated = document.getElementById('resCatUpdated');
        const resPostCreated = document.getElementById('resPostCreated');
        const resPostUpdated = document.getElementById('resPostUpdated');
        const resPostSkipped = document.getElementById('resPostSkipped');
        const resTimeElapsed = document.getElementById('resTimeElapsed');

        let selectedFile = null;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Drag & drop handlers
        dropzone.addEventListener('click', () => fileInput.click());
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.style.background = '#eef5fc';
            dropzone.style.borderColor = '#18a689';
        });
        dropzone.addEventListener('dragleave', () => {
            dropzone.style.background = '#f9fbfd';
            dropzone.style.borderColor = '#1ab394';
        });
        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.style.background = '#f9fbfd';
            dropzone.style.borderColor = '#1ab394';
            if (e.dataTransfer.files.length) {
                handleFileSelect(e.dataTransfer.files[0]);
            }
        });
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length) {
                handleFileSelect(e.target.files[0]);
            }
        });

        function addLog(message, type = 'info') {
            const time = new Date().toLocaleTimeString();
            let color = '#39ff14'; // neon green
            if (type === 'error') color = '#ff3333';
            if (type === 'warning') color = '#ffcc00';
            if (type === 'system') color = '#00ccff';

            logConsole.innerHTML += `<span style="color: ${color}">[${time}] ${message}</span><br>`;
            logConsole.scrollTop = logConsole.scrollHeight;
        }

        function handleFileSelect(file) {
            if (!file.name.endsWith('.json')) {
                alert('Vui lòng chọn file định dạng .json');
                return;
            }

            selectedFile = file;
            fileInfo.style.display = 'block';
            fileInfo.innerHTML = `📄 <strong>File:</strong> ${file.name} <br> ⚡ <strong>Dung lượng:</strong> ${(file.size / 1024 / 1024).toFixed(2)} MB`;
            
            addLog(`Đã chọn file ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB) để import.`, 'system');
            startBtn.removeAttribute('disabled');
        }

        startBtn.addEventListener('click', async function () {
            if (!selectedFile) return;

            // Reset UI State
            startBtn.setAttribute('disabled', 'disabled');
            progressBox.style.display = 'block';
            uploadProgressContainer.style.display = 'block';
            processingLoader.style.display = 'none';
            resultContainer.style.display = 'none';
            statusLabel.textContent = 'ĐANG TẢI LÊN...';
            statusLabel.className = 'label label-warning pull-right';

            const forceUpdate = document.getElementById('forceUpdate').checked;
            const uniqueId = 'import_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            
            const chunkSize = 5 * 1024 * 1024; // 5MB chunks
            const totalChunks = Math.ceil(selectedFile.size / chunkSize);
            const uploadStart = Date.now();

            addLog(`Bắt đầu tải file lên máy chủ (được cắt làm ${totalChunks} chunks của 5MB)...`, 'system');

            try {
                // 1. Upload Chunks loop
                for (let i = 0; i < totalChunks; i++) {
                    const start = i * chunkSize;
                    const end = Math.min(start + chunkSize, selectedFile.size);
                    const chunk = selectedFile.slice(start, end);

                    const formData = new FormData();
                    formData.append('chunk', chunk);
                    formData.append('index', i);
                    formData.append('total', totalChunks);
                    formData.append('uniqueId', uniqueId);

                    const response = await fetch('{{ route("post.import.uploadChunk") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    });

                    const resData = await response.json();
                    if (!resData.success) throw new Error(resData.message || `Lỗi tải chunk ${i + 1}`);

                    // Calculate Upload speed and percentage
                    const percent = Math.round(((i + 1) / totalChunks) * 100);
                    progressBar.style.width = `${percent}%`;
                    progressBar.textContent = `${percent}%`;
                    percentVal.textContent = percent;

                    const elapsed = (Date.now() - uploadStart) / 1000;
                    const uploadedMB = (end / 1024 / 1024).toFixed(1);
                    const totalMB = (selectedFile.size / 1024 / 1024).toFixed(1);
                    const speed = (end / 1024 / 1024 / elapsed).toFixed(1);

                    uploadSpeed.textContent = `${speed} MB/s`;
                    progressTitle.innerHTML = `Đang tải lên: ${uploadedMB} / ${totalMB} MB (${percent}%)`;
                    
                    if ((i + 1) % 5 === 0 || i + 1 === totalChunks) {
                        addLog(`Đã tải lên chunk ${i + 1}/${totalChunks} (${percent}%)...`, 'info');
                    }
                }

                // 2. Trigger server-side Processing
                addLog('Tải file lên máy chủ thành công 100%. Bắt đầu kích hoạt tiến trình xử lý cơ sở dữ liệu trên Server...', 'system');
                
                uploadProgressContainer.style.display = 'none';
                processingLoader.style.display = 'block';
                statusLabel.textContent = 'SERVER ĐANG XỬ LÝ...';
                statusLabel.className = 'label label-primary pull-right';

                const processStart = Date.now();
                const processRes = await fetch('{{ route("post.import.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        uniqueId: uniqueId,
                        forceUpdate: forceUpdate
                    })
                });

                const processData = await processRes.json();
                if (!processData.success) throw new Error(processData.message || 'Lỗi xử lý file import trên server');

                const totalTime = ((Date.now() - processStart) / 1000).toFixed(1);
                addLog(`Xử lý thành công trên Server trong ${totalTime} giây!`, 'system');
                addLog('Xây dựng lại chỉ mục cây danh mục (Nested Set) hoàn tất.', 'system');
                
                // Show statistics
                statusLabel.textContent = 'HOÀN TẤT!';
                statusLabel.className = 'label label-success pull-right';
                processingLoader.style.display = 'none';
                resultContainer.style.display = 'block';

                resCatCreated.textContent = processData.stats.categories_created.toLocaleString();
                resCatUpdated.textContent = processData.stats.categories_updated.toLocaleString();
                resPostCreated.textContent = processData.stats.posts_created.toLocaleString();
                resPostUpdated.textContent = processData.stats.posts_updated.toLocaleString();
                resPostSkipped.textContent = processData.stats.posts_skipped.toLocaleString();
                resTimeElapsed.textContent = `${totalTime} giây`;

                addLog(`IMPORT HOÀN THÀNH: Tạo mới ${processData.stats.posts_created} bài viết & ${processData.stats.categories_created} danh mục thành công!`, 'system');
                alert(`Import thành công! Đã tạo mới ${processData.stats.posts_created} bài viết.`);

            } catch (err) {
                addLog(`Lỗi tiến trình: ${err.message}`, 'error');
                statusLabel.textContent = 'LỖI!';
                statusLabel.className = 'label label-danger pull-right';
                processingLoader.style.display = 'none';
                alert(`Tiến trình thất bại: ${err.message}`);
            } finally {
                startBtn.removeAttribute('disabled');
            }
        });
    });
</script>
