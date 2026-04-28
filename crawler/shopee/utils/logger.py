"""
=====================================================
  utils/logger.py — Logger dùng chung toàn project
  Hỗ trợ: in màu ra terminal + ghi ra file log
=====================================================
"""

import logging
import os
import colorlog
from config import LOG_FILE, OUTPUT_DIR

# Đảm bảo thư mục output tồn tại
os.makedirs(OUTPUT_DIR, exist_ok=True)


def get_logger(name: str = "shopee_crawler") -> logging.Logger:
    """
    Tạo logger với:
    - Console handler: in màu theo level
    - File handler:    ghi toàn bộ vào crawler.log
    """
    logger = logging.getLogger(name)

    # Tránh add handler nhiều lần nếu gọi get_logger() nhiều lần
    if logger.handlers:
        return logger

    logger.setLevel(logging.DEBUG)

    # ── Console handler (có màu) ──────────────────
    console_formatter = colorlog.ColoredFormatter(
        "%(log_color)s[%(levelname)s]%(reset)s %(asctime)s %(name)s: %(message)s",
        datefmt="%H:%M:%S",
        log_colors={
            "DEBUG":    "cyan",
            "INFO":     "green",
            "WARNING":  "yellow",
            "ERROR":    "red",
            "CRITICAL": "red,bg_white",
        },
    )
    console_handler = logging.StreamHandler()
    console_handler.setLevel(logging.INFO)
    console_handler.setFormatter(console_formatter)

    # ── File handler (không màu) ──────────────────
    # CHỈ LOG LỖI (ERROR) RA FILE ĐỂ TIẾT KIỆM BỘ NHỚ VÀ DỄ THEO DÕI
    file_formatter = logging.Formatter(
        "[%(levelname)s] %(asctime)s %(name)s: %(message)s",
        datefmt="%Y-%m-%d %H:%M:%S",
    )
    file_handler = logging.FileHandler(LOG_FILE, encoding="utf-8")
    file_handler.setLevel(logging.ERROR)
    file_handler.setFormatter(file_formatter)

    logger.addHandler(console_handler)
    logger.addHandler(file_handler)

    return logger
