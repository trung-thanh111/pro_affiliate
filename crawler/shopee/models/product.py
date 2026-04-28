from dataclasses import dataclass, asdict
from typing import List

@dataclass
class ShopeeProduct:
    title: str
    price_min: float
    price_max: float
    images: List[str]

    def to_dict(self):
        return asdict(self)
