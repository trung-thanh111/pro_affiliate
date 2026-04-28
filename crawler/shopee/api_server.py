"""
=====================================================
  api_server.py — FastAPI HTTP Server
  
  Chạy: uvicorn api_server:app --host 0.0.0.0 --port 8001
  
  Laravel gọi vào đây thay vì chạy Python trực tiếp.
  Lý do tách thành service riêng:
  - Laravel và Python chạy độc lập
  - Có thể scale Python riêng
  - Dễ debug từng service
=====================================================
"""

import time
from contextlib import asynccontextmanager
from typing import Optional

from fastapi import FastAPI, HTTPException, Request
from fastapi.responses import JSONResponse
from pydantic import BaseModel, HttpUrl, field_validator

from config import API_HOST, API_PORT
from crawlers.shopee_api_crawler import ShopeeApiCrawler
from crawlers.batch_crawler import crawl_urls
from utils.url_parser import parse_shopee_url
from utils.logger import get_logger

logger = get_logger("api_server")

# ── Crawler dùng chung toàn app (singleton) ──────
# Khởi tạo 1 lần khi server start, không khởi tạo lại mỗi request
# Lý do: session và cookies được giữ nguyên giữa các request
_crawler: Optional[ShopeeApiCrawler] = None


@asynccontextmanager
async def lifespan(app: FastAPI):
    """Khởi tạo crawler khi server start, cleanup khi stop."""
    global _crawler
    logger.info("🚀 Khởi động Shopee Crawler API Server...")
    _crawler = ShopeeApiCrawler()
    logger.info(f"✅ Server sẵn sàng tại http://{API_HOST}:{API_PORT}")
    yield
    # Cleanup khi shutdown
    logger.info("🔴 Server đang tắt...")


# ── Tạo FastAPI app ───────────────────────────────
app = FastAPI(
    title       = "Shopee Crawler API",
    description = "Internal service crawl dữ liệu sản phẩm Shopee",
    version     = "1.0.0",
    lifespan    = lifespan,
)


# ── Request / Response schemas ────────────────────

class CrawlSingleRequest(BaseModel):
    """Request crawl 1 sản phẩm."""
    url: str

    @field_validator("url")
    @classmethod
    def validate_shopee_url(cls, v: str) -> str:
        # Kiểm tra là URL Shopee hợp lệ ngay khi nhận request
        if "shopee.vn" not in v:
            raise ValueError("URL phải là link Shopee (shopee.vn)")
        return v.strip()


class CrawlBatchRequest(BaseModel):
    """Request crawl nhiều sản phẩm."""
    urls: list[str]

    @field_validator("urls")
    @classmethod
    def validate_urls(cls, v: list[str]) -> list[str]:
        if not v:
            raise ValueError("Danh sách URLs không được rỗng")
        if len(v) > 50:
            raise ValueError("Tối đa 50 URLs mỗi batch")
        for url in v:
            if "shopee.vn" not in url:
                raise ValueError(f"URL không hợp lệ: {url}")
        return v


# ── Middleware: log mọi request ───────────────────

@app.middleware("http")
async def log_requests(request: Request, call_next):
    start_time = time.time()
    response   = await call_next(request)
    duration   = (time.time() - start_time) * 1000
    logger.info(
        f"{request.method} {request.url.path} → "
        f"{response.status_code} ({duration:.0f}ms)"
    )
    return response


# ── Endpoints ─────────────────────────────────────

@app.get("/health")
async def health_check():
    """
    Kiểm tra server đang chạy.
    Laravel gọi endpoint này để verify service trước khi crawl.
    """
    stats = _crawler.get_stats() if _crawler else {}
    return {
        "status":  "ok",
        "crawler": "ready" if _crawler else "not initialized",
        "stats":   stats,
    }


@app.post("/crawl")
async def crawl_single(request: CrawlSingleRequest):
    """
    Crawl 1 sản phẩm từ URL Shopee.
    
    Request body:
        { "url": "https://shopee.vn/..." }
        
    Response:
        JSON thông tin sản phẩm hoặc HTTP error
    """
    try:
        # Parse URL lấy shop_id, item_id
        ids = parse_shopee_url(request.url)
    except ValueError as e:
        raise HTTPException(status_code=400, detail=f"URL không hợp lệ: {str(e)}")

    # Crawl sản phẩm
    product = _crawler.get_product(
        shop_id=ids["shop_id"],
        item_id=ids["item_id"],
    )

    if not product:
        raise HTTPException(
            status_code=404,
            detail=(
                f"Không thể lấy dữ liệu sản phẩm. "
                f"Shopee có thể đã block hoặc sản phẩm không tồn tại. "
                f"(shop={ids['shop_id']}, item={ids['item_id']})"
            ),
        )

    return product.to_dict()


@app.post("/crawl/batch")
async def crawl_batch(request: CrawlBatchRequest):
    """
    Crawl nhiều sản phẩm trong 1 lần gọi.
    
    Request body:
        { "urls": ["https://shopee.vn/...", ...] }
        
    Response:
        { "success": N, "products": [...], "errors": [...] }
    """
    result = crawl_urls(
        urls    = request.urls,
        crawler = _crawler,
        save_to_file = False,  # Không lưu file khi gọi qua API
    )

    return {
        "success":  result.success_count,
        "failed":   result.error_count,
        "total":    result.total,
        "products": [p.to_dict() for p in result.products],
        "errors":   result.errors,
    }


@app.get("/stats")
async def get_stats():
    """Thống kê số request của crawler trong session hiện tại."""
    if not _crawler:
        raise HTTPException(status_code=503, detail="Crawler chưa khởi tạo")
    return _crawler.get_stats()


# ── Chạy trực tiếp (không qua uvicorn CLI) ────────

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "api_server:app",
        host    = API_HOST,
        port    = API_PORT,
        reload  = False,  # Tắt reload trong production
        workers = 1,      # Giữ 1 worker để session crawler là singleton
    )
