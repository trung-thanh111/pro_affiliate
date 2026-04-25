@if (isset($items) && count($items))
    <ul class="list-unstyled mb-0">
        @foreach ($items as $node)
            @php
                $cat = $node['item'];
                $name = $cat->languages->first()->pivot->name ?? $cat->name;
                $id = $cat->id;
            @endphp

            <li class="mb-2">
                <div class="form-check">
                    <input class="form-check-input filterAttribute filtering" type="checkbox" value="{{ $id }}"
                        id="cat-{{ $id }}" data-group="catalogue">
                    <label class="form-check-label" for="cat-{{ $id }}">
                        {{ $name }}
                    </label>
                </div>

                @if (isset($node['children']) && count($node['children']))
                    <div class="ms-3 mt-2">
                        @include('frontend.product.catalogue.component.product-catalogue-tree', [
                            'items' => $node['children'],
                        ])
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
@endif
