<div class="ibox w">
    <div class="ibox-title">
        <h5>{{ __('messages.parent') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <select name="post_catalogue_id" class="form-control setupSelect2" id="">
                        @foreach($dropdown as $key => $val)
                        <option {{ 
                            $key == old('post_catalogue_id', (isset($post->post_catalogue_id)) ? $post->post_catalogue_id : '') ? 'selected' : '' 
                            }} value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @php
            $catalogue = [];
            if(isset($post)){
                foreach($post->post_catalogues as $key => $val){
                    $catalogue[] = $val->id;
                }
            }
        @endphp
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label">{{ __('messages.subparent') }}</label>
                    <select multiple name="catalogue[]" class="form-control setupSelect2" id="">
                        @foreach($dropdown as $key => $val)
                        <option 
                            @if(is_array(old('catalogue', (
                                isset($catalogue) && count($catalogue)) ?   $catalogue : [])
                                ) && isset($post->post_catalogue_id) && $key !== $post->post_catalogue_id &&  in_array($key, old('catalogue', (isset($catalogue)) ? $catalogue : []))
                            )
                            selected
                            @endif value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox w hidden">
    <div class="ibox-title">
        <h5>Review</h5>
        <div class="description text-danger">Dành riêng cho mục review về các trường đại học</div>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12 mb10">
                <div class="form-row">
                    <input 
                        type="text"
                        name="logo"
                        value="{{ old('logo', ($post->logo) ?? '' ) }}"
                        placeholder="Ảnh logo của trường"
                        class="form-control upload-image"
                        autocomplete="off"
                    >
                </div>
            </div>
            <div class="col-lg-6 mb10">
                <div class="form-row">
                    <input 
                        type="text"
                        name="rate"
                        value="{{ old('rate', ($post->rate) ?? '' ) }}"
                        placeholder="Đánh giá 1 - 100"
                        class="form-control"
                        autocomplete="off"
                    >
                </div>
            </div>
            <div class="col-lg-6 mb10">
                <div class="form-row">
                    <input 
                        type="text"
                        name="comments"
                        value="{{ old('comments', ($post->comments) ?? '' ) }}"
                        placeholder="Số đánh giá"
                        class="form-control"
                        autocomplete="off"
                    >
                </div>
            </div>
            <div class="col-lg-12 mb10">
                <div class="form-row">
                    <textarea 
                        name="extra"
                        class="form-control"
                        placeholder="Phần nội dung cơ bản, mỗi dòng cách nhau 1 dấu ,"
                        autocomplete="off"
                        style="height:100px;"
                    >{{ old('extra', ($post->extra) ?? '' ) }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="ibox w hidden">
    <div class="ibox-title">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            <h5>Video Clip</h5>
            <a href="" class="upload-video">Upload Video</a>
        </div>
    </div>
    <div class="ibox-content">
        <div class="description text-danger mb10">Upload Video - hoặc copy mã nhúng</div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <textarea name="video" class="form-control video-target" style="height:168px;">{{ old('video', (isset($post->video)) ? $post->video : '') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ibox w">
    <div class="ibox-title">
        <h5>Cấu hình Review</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label">Là bài viết review?</label>
                    <select name="is_review" class="form-control setupSelect2">
                        <option value="0" {{ old('is_review', (isset($post->is_review)) ? $post->is_review : 0) == 0 ? 'selected' : '' }}>Không</option>
                        <option value="1" {{ old('is_review', (isset($post->is_review)) ? $post->is_review : 0) == 1 ? 'selected' : '' }}>Có</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label">Sản phẩm liên quan</label>
                    <select name="product_id" class="form-control setupSelect2">
                        <option value="">-- Chọn sản phẩm --</option>
                        @if(isset($products))
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id', (isset($post->product_id)) ? $post->product_id : '') == $product->id ? 'selected' : '' }}>
                                {{ $product->languages->first()->pivot->name }}
                            </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ibox w">
    <div class="ibox-title">
        <h5>Cấu hình nâng cao</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label">Ảnh Icon</label>
                    <input 
                        type="text"
                        name="icon"
                        value="{{ old('icon', ($post->icon) ?? '' ) }}"
                        class="form-control upload-image"
                        placeholder="Đường dẫn ảnh icon"
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label">Ảnh Banner Sidebar</label>
                    <input 
                        type="text"
                        name="banner"
                        value="{{ old('banner', ($post->banner) ?? '' ) }}"
                        class="form-control upload-image"
                        placeholder="Đường dẫn ảnh banner sidebar"
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
        @php
            $relatedPosts = [];
            if(isset($post)){
                foreach($post->related_posts as $key => $val){
                    $relatedPosts[] = $val->id;
                }
            }
        @endphp
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label">Bài viết liên quan (N-N)</label>
                    <select multiple name="related_posts[]" class="form-control setupSelect2">
                        @if(isset($posts))
                            @foreach($posts as $item)
                            @if(isset($post) && $item->id == $post->id) @continue @endif
                            <option value="{{ $item->id }}" {{ in_array($item->id, old('related_posts', $relatedPosts)) ? 'selected' : '' }}>
                                {{ $item->languages->first()->pivot->name }}
                            </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ibox w">
    <div class="ibox-title">
        <h5>{{ __('messages.image') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="image img-cover image-target"><img src="{{ (old('image', ($post->image) ?? '' ) ? old('image', ($post->image) ?? '')   :  'backend/img/not-found.jpg') }}" alt=""></span>
                    <input type="hidden" name="image" value="{{ old('image', ($post->image) ?? '' ) }}">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox w">
    <div class="ibox-title">
        <h5>{{ __('messages.advange') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <div class="mb15">
                        <select name="publish" class="form-control setupSelect2" id="">
                            @foreach(__('messages.publish') as $key => $val)
                            <option {{ $key == old('publish', (isset($post->publish)) ? $post->publish : '2') ? 'selected' : '' }} value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb15">
                        <select name="follow" class="form-control setupSelect2" id="">
                            @foreach(__('messages.follow') as $key => $val)
                            <option {{ 
                                $key == old('follow', (isset($post->follow)) ? $post->follow : '') ? 'selected' : '' 
                                }} value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb15 hidden">
                        <select name="recommend" class="form-control setupSelect2" id="">
                            @foreach(__('messages.recommend') as $key => $val)
                            <option {{ 
                                $key == old('recommend', (isset($post->recommend)) ? $post->recommend : '') ? 'selected' : '' 
                                }} value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="hidden">
                        <select name="post_type" class="form-control setupSelect2" id="">
                            @foreach(__('messages.post_type') as $key => $val)
                            <option {{ 
                                $key == old('post_type', (isset($post->post_type)) ? $post->post_type : '') ? 'selected' : '' 
                                }} value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
