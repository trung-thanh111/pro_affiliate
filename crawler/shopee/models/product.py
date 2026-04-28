from dataclasses import dataclass, asdict
from typing import List

@dataclass
class ShopeeProduct:
    item_id: str
    shop_id: str
    title: str
    price_min: float
    price_max: float
    images: List[str]
    url: str
    crawled_at: str

    def to_dict(self):
        return asdict(self)
