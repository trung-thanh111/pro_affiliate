(function($) {
	"use strict";
	var HT = {}; 
    var _token = $('meta[name="csrf-token"]').attr('content');
    var typingTimer;
    var doneTyingInterval = 300; // 1s

    HT.searchModel = () => {
        $(document).on('keyup', '.search-model', function(e){
            e.preventDefault()
            let _this = $(this)
            let model = _this.data('model')
            if(!model){
                if($('input[type=radio]:checked').length === 0){
                    alert('Bạn chưa chọn Module');
                    _this.val('')
                    return false;
                }
                model = $('input[type=radio]:checked').val()
            }
            let keyword = _this.val()
            let option = {
                model: model,
                keyword : keyword
            }
            HT.sendAjax(option)
            
        })
    }

    HT.chooseModel = () => {
        $(document).on('change', '.input-radio', function(){
            let _this = $(this)
            let option = {
                model: _this.val(),
                keyword : $('.search-model').val()
            }
            $('.search-model-result').html('');
            if(option.keyword.length >= 2){
                HT.sendAjax(option)
            }
            

        })
    }

    HT.sendAjax = (option) => {
        clearTimeout(typingTimer);
            typingTimer = setTimeout(function(){
                $.ajax({
                    url: '/ajax/dashboard/findModelObject', 
                    type: 'GET', 
                    data: option,
                    dataType: 'json', 
                    success: function(res) {
                        let html = HT.renderSearchResult(res)
                        if(html.length){
                            $('.ajax-search-result').html(html).show()
                        }else{
                            $('.ajax-search-result').html(html).hide()
                        }
                    },
                    beforeSend: function() {
                        $('.ajax-search-result').html('').hide()
                    },
                });
               
            }, doneTyingInterval)
    }

    HT.renderSearchResult = (data) => {
        let html = `<div class="ajax-search-header uk-flex uk-flex-middle uk-flex-space-between" style="padding: 8px 10px; border-bottom: 1px solid #eee; background: #fdfdfd; position: sticky; top: 0; z-index: 10;">
            <span class="fw-bold text-navy" style="font-size: 12px;">KẾT QUẢ TÌM KIẾM (${data.length})</span>
            <span class="close-suggestion" style="cursor: pointer; color: #ed5565; font-weight: bold; font-size: 11px; text-transform: uppercase;">[ ĐÓNG ]</span>
        </div>`
        html += '<div class="ajax-search-list" style="max-height: 300px; overflow-y: auto;">'
        if(data.length){
            for(let i = 0; i < data.length; i++){
                let flag = ($('.search-model-result #model-'+data[i].id).length) ? 1 : 0;
                let setChecked = HT.setChecked(flag == 1)
                let languages = data[i].languages || []
                let lang = languages[0] || { pivot: { name: '', canonical: '' } }
                let name = lang.pivot.name || ''
                let canonical = lang.pivot.canonical || ''
                let description = lang.pivot.description || ''

                html += `<div 
                            class="ajax-search-item" 
                            data-flag="${flag}" 
                            data-canonical="${canonical}" data-image="${data[i].image}" 
                            data-name="${name}" 
                            data-description="${description}"
                            data-id="${data[i].id}"
                            style="cursor: pointer; border-bottom: 1px solid #f4f4f4;"
                        >
                <div class="uk-flex uk-flex-middle uk-flex-space-between" style="padding: 8px 10px;">
                    <div class="uk-flex uk-flex-middle">
                        <span class="image img-cover me-2" style="width: 32px; height: 32px; display: inline-block; border-radius: 4px; overflow: hidden; border: 1px solid #eee;"><img src="${data[i].image}" alt="" style="width: 100%; height: 100%; object-fit: cover;"></span>
                        <span style="font-size: 13px; color: #333;">${name}</span>
                    </div>
                    <div class="auto-icon">
                        ${setChecked}
                    </div>
                </div>
            </div>`
            }
        }
        html += '</div>'
        return html
    }

    HT.setChecked = (isChecked = false) => {
        return `<input type="checkbox" ${isChecked ? 'checked' : ''} style="width: 16px; height: 16px; cursor: pointer;">`
    }


    HT.unfocusSearchBox = () => {
        $(document).on('click', function(e){
            let _target = $(e.target);
            if(!_target.closest('.search-model-box').length && !_target.closest('.ajax-search-result').length){
                $('.ajax-search-result').html('').hide()
            }
        })

        $(document).on('click', '.close-suggestion', function(){
            $('.ajax-search-result').html('').hide()
        })
    }

    HT.addModel = () => {
        $(document).off('click', '.ajax-search-item').on('click', '.ajax-search-item' , function(e){
            e.preventDefault()
            let _this = $(this)
            let data = _this.data()
            let flag = _this.attr('data-flag')
            let container = _this.closest('.ibox-content').find('.search-model-result')
            
            console.log('Item clicked, flag:', flag);
            
            if(flag == 0){
                _this.find('.auto-icon').html(HT.setChecked(true))
                _this.attr('data-flag', 1)
                let html = HT.modelTemplate(data)
                console.log('Appending HTML:', html);
                container.append(html)
            }else{
                container.find('#model-'+data.id).remove()
                _this.find('.auto-icon').html(HT.setChecked(false))
                _this.attr('data-flag', 0)
            }
            // $('.ajax-search-result').html('').hide()
        })
    }


    HT.modelTemplate = (data) => {
        let html = `<div class="search-result-item" id="model-${data.id}" data-modelid="${data.id}">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <div class="uk-flex uk-flex-middle">
                    <span class="image img-cover"><img src="${data.image}" alt=""></span>
                    <span class="name">${data.name}</span>
                    <div class="hidden">
                        <input type="text" name="modelItem[id][]" value="${data.id}">
                        <input type="text" name="modelItem[name][]" value="${data.name}">
                        <input type="text" name="modelItem[image][]" value="${data.image}">
                        <input type="text" name="modelItem[description][]" value="${data.description || ''}">
                    </div>
                </div>
                <div class="deleted">
                    <svg class="svg-next-icon svg-next-icon-size-12" width="12" height="12">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                            <path d="M18.263 16l10.07-10.07c.625-.625.625-1.636 0-2.26s-1.638-.627-2.263 0L16 13.737 5.933 3.667c-.626-.624-1.637-.624-2.262 0s-.624 1.64 0 2.264L13.74 16 3.67 26.07c-.626.625-.626 1.636 0 2.26.312.313.722.47 1.13.47s.82-.157 1.132-.47l10.07-10.068 10.068 10.07c.312.31.722.468 1.13.468s.82-.157 1.132-.47c.626-.625.626-1.636 0-2.26L18.262 16z">
                                
                            </path>
                        </svg>
                    </svg>
                </div>
            </div>
        </div>`
        return html
    }

    HT.removeModel = () => {
        $(document).on('click', '.deleted', function(){
            let _this = $(this)
            _this.parents('.search-result-item').remove()
        })
    }

   
	$(document).ready(function(){
        HT.searchModel()
        HT.chooseModel()
        HT.unfocusSearchBox()
        HT.addModel()
        HT.removeModel()
	});

})(jQuery);
