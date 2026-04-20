@if(isset($latestPosts) && count($latestPosts))
<section class="panel-hot-topic mt50 mb50">
    <div class="uk-container uk-container-center">
        <div class="hot-topic-wrapper">
            <div class="hot-topic-head uk-flex uk-flex-middle uk-flex-space-between mb40">
                <div class="head-left">
                    <h2 class="title">Bạn đang tìm kiếm chủ đề nóng ?</h2>
                    <div class="subtitle">
                        Khám phá những chủ đề đang được quan tâm thảo luận nhiều nhất, mang lại cái nhìn toàn diện về xu hướng thị trường hiện nay.
                    </div>
                </div>
                <div class="head-right">
                    <div class="search-box">
                        <input type="text" placeholder="Từ khóa nổi bật">
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="hot-topic-body">
                <div class="uk-grid uk-grid-medium">
                    @foreach($latestPosts->chunk(3) as $chunk)
                        <div class="uk-width-large-1-4 uk-width-medium-1-2 uk-width-small-1-1">
                            @foreach($chunk as $post)
                                @php
                                    $lang = $post->languages->first();
                                    $name = $lang->pivot->name;
                                    $canonical = write_url($lang->pivot->canonical);
                                @endphp
                                <div class="mb20">
                                    <a href="{{ $canonical }}" class="topic-tag">
                                        {{ Str::limit($name, 45) }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif
