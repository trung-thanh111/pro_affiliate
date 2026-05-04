from dataclasses import dataclass, asdict
from typing import List

@dataclass
class ShopeeProduct:
    name: str
    price: float
    price_discount: float
    album: List[str]
    

    def to_dict(self):
        return asdict(self)
