@if(isset($slides['middle-home']) && count($slides['middle-home']['item']))
    <section class="panel-middle-banner mt30 mb30">
        <div class="uk-container uk-container-center">
            <div class="uk-grid uk-grid-medium" data-uk-grid-match="{target:'.banner-item'}">
                @foreach($slides['middle-home']['item'] as $key => $val)
                    @if($key < 2)
                        <div class="uk-width-large-1-2 mb20">
                            <div class="banner-item" style="height: 260px;">
                                <a href="{{ $val['canonical'] ?? '#' }}" class="image img-cover"
                                    style="height: 100%; display: block;">
                                    <img src="{{ $val['image'] }}" alt="{{ $val['alt'] ?? '' }}"
                                        style="border-radius: 12px; width: 100%; height: 100%; object-fit: cover; display: block;">
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endif