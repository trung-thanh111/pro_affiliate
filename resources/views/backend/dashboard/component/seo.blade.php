<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('messages.seo') }}</h5>
    </div>
    <div class="ibox-content">
        @php
            $postTitle = trim($model->name ?? ($model->title ?? ''));
            $postMetaTitle = trim($model->meta_title ?? '');
            $titleSource = !empty($postMetaTitle) ? 'SEO Custom Title' : (!empty($postTitle) ? 'Tiêu đề bài viết (fallback)' : 'Default Site Title');
            $activeTitle = !empty($postMetaTitle) ? $postMetaTitle : (!empty($postTitle) ? $postTitle : __('messages.seoTitle'));

            $postDesc = trim($model->description ?? '');
            $postMetaDesc = trim($model->meta_description ?? '');
            $postContent = trim($model->content ?? '');
            
            if (!empty($postMetaDesc)) {
                $activeDesc = $postMetaDesc;
                $descSource = 'SEO Custom Description';
            } elseif (!empty($postDesc)) {
                $activeDesc = $postDesc;
                $descSource = 'Mô tả ngắn bài viết (fallback)';
            } elseif (!empty($postContent)) {
                $decoded = html_entity_decode($postContent, ENT_QUOTES, 'UTF-8');
                $activeDesc = trim(preg_replace('/\s+/', ' ', strip_tags($decoded)));
                $activeDesc = mb_strlen($activeDesc, 'UTF-8') > 160 ? mb_substr($activeDesc, 0, 160, 'UTF-8') . '...' : $activeDesc;
                $descSource = 'Nội dung bài viết (fallback)';
            } else {
                $activeDesc = __('messages.seoDescription');
                $descSource = 'Default Site Description';
            }

            $activeImg = !empty($model->image) ? $model->image : '';
            $imgSource = !empty($activeImg) ? 'Ảnh đại diện bài viết' : 'Default Sharing Image';
        @endphp
        <div class="seo-preview-wrapper mb20" style="background: #f8f9fa; border: 1px solid #e7eaec; padding: 15px; border-radius: 6px;">
            <h5 style="margin-top: 0; color: #1ab394; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-google"></i> Google Search Snippet Preview
            </h5>
            <div class="seo-container" style="background: #fff; border: 1px solid #e7eaec; padding: 12px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div class="meta-title" style="color: #1a0dab; font-size: 18px; font-family: arial, sans-serif; line-height: 1.2; margin-bottom: 2px; font-weight: normal; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    {{ $activeTitle }}
                </div>
                <div class="canonical" style="color: #006621; font-size: 14px; font-family: arial, sans-serif; line-height: 1.4; margin-bottom: 2px;">
                    {{ (old('canonical', ($model->canonical) ?? '')) ? config('app.url').'/'.old('canonical', ($model->canonical) ?? '').config('apps.general.suffix') :  config('app.url').'/canonical-url.html'  }}
                </div>
                <div class="meta-description" style="color: #545454; font-size: 14px; font-family: arial, sans-serif; line-height: 1.4; word-wrap: break-word;">
                    {{ $activeDesc }}
                </div>
            </div>
            
            <div class="seo-fallback-info mt10" style="font-size: 12px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; border-top: 1px solid #e7eaec; padding-top: 10px; margin-top: 10px;">
                <div>
                    <strong style="color: #676a6c;">Nguồn Title:</strong> 
                    <span class="label {{ !empty($postMetaTitle) ? 'label-primary' : 'label-warning' }}" style="padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: 600;">
                        {{ $titleSource }}
                    </span>
                </div>
                <div>
                    <strong style="color: #676a6c;">Nguồn Description:</strong> 
                    <span class="label {{ !empty($postMetaDesc) ? 'label-primary' : 'label-warning' }}" style="padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: 600;">
                        {{ $descSource }}
                    </span>
                </div>
                @if(!empty($activeImg))
                <div style="grid-column: 1 / -1; display: flex; align-items: center; gap: 10px;">
                    <strong style="color: #676a6c;">Ảnh Share:</strong>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <img src="{{ asset($activeImg) }}" alt="Preview" style="max-height: 40px; border-radius: 3px; border: 1px solid #e7eaec; object-fit: cover;">
                        <span class="text-muted" style="font-size: 11px;">({{ $imgSource }})</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div class="seo-wrapper">
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                <span>{{ __('messages.seoMetaTitle') }}</span>
                                <span class="count_meta-title">0 {{ __('messages.character') }}</span>
                            </div>
                        </label>
                        <input 
                            type="text"
                            name="meta_title"
                            value="{{ old('meta_title', ($model->meta_title) ?? '' ) }}"
                            class="form-control"
                            placeholder=""
                            autocomplete="off"
                            {{ (isset($disabled)) ? 'disabled' : '' }}
                        >
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">
                            <span>{{ __('messages.seoMetaKeyword') }}</span>
                        </label>
                        <input 
                            type="text"
                            name="meta_keyword"
                            value="{{ old('meta_keyword', ($model->meta_keyword) ?? '' ) }}"
                            class="form-control"
                            placeholder=""
                            autocomplete="off"
                            {{ (isset($disabled)) ? 'disabled' : '' }}
                        >
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                <span>{{ __('messages.seoMetaDescription') }}</span>
                                <span class="count_meta-description"><span class="countD">0</span> / 160 {{ __('messages.character') }}</span>
                            </div>
                        </label>
                        <textarea 
                            name="meta_description"
                            class="form-control"
                            placeholder=""
                            autocomplete="off"
                            {{ (isset($disabled)) ? 'disabled' : '' }}
                        >{{ old('meta_description', ($model->meta_description) ?? '') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">
                            <span>{{ __('messages.canonical') }} (không bao gồm đuôi .html) <span class="text-danger">*</span></span>
                        </label>
                       <div class="input-wrapper">
                            <input 
                                type="text"
                                name="canonical"
                                value="{{ old('canonical', ($model->canonical) ?? '' ) }}"
                                class="form-control seo-canonical"
                                placeholder=""
                                autocomplete="off"
                                {{ (isset($disabled)) ? 'disabled' : '' }}
                            >
                            <span class="baseUrl">{{ config('app.url') }}</span>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>