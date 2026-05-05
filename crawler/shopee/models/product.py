from dataclasses import dataclass, asdict
from typing import List, Optional, Dict, Any

@dataclass
class ShopeeProduct:
    name: str
    price: float
    price_discount: float
    album: List[str]
    sold: int = 0
    link: str = ""
    # catalogue: Optional[Dict[str, Any]] = None
    

    def to_dict(self):
        return asdict(self)
