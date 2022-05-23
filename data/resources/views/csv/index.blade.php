@include('header')
<style>
    .max-w-6xl {
        max-width: 130rem;
    }

    .list-group-item:first-child {
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }
    .list-group-item {
        position: relative;
        display: block;
        padding: 0.75rem 1.25rem;
        margin-bottom: -1px;
        background-color: #fff;
        border: 1px solid rgba(0,0,0,.125);
    }

    .list-group-items {
        display: flex;
        margin-top: 10px;
    }

    .list-group-item:first-child, .list-group-item:first-child select {
        background-color: #fff6b2;
    }

    .list-group-wrap {
        overflow: scroll;
        padding: 1px 0;
    }

    select {
        font-size: initial;
        width: 100%;
    }

    fieldset + fieldset {
        margin-top: 30px;
    }

    legend {
        padding-bottom: 5px;
    }

    .document-link {
        margin-top: 10px;
    }

    .buttons {
        display: flex;
    }

    input[name="block"] {
        border: 1px solid;
        height: 100%;
        width: 230px;
    }

    .custom-select-box {
        margin-left: 10px;
    }
</style>
<form action="{{ route('csv.export') }}" method="post">
    @csrf
    @error('order')
    <div class="error alert alert-danger">{{ $message }}</div>
    @enderror
    <span data-error-export="order"></span>
    <fieldset>
        <legend>対象ラベル</legend>
        @foreach ($TARGET_LABEL as $key => $value)
            <input type="radio" id="target_label_{{ $key }}" name="target_label" value="{{ $key }}">
            <label for="target_label_{{ $key }}">{{ $value }}</label>
        @endforeach
        @error('target_label')
        <div class="error alert alert-danger">{{ $message }}</div>
        @enderror
        <span data-error-export="target_label"></span>
    </fieldset>
    <fieldset>
        <legend>アーカイブされた注文を出力対象に含めますか？</legend>
        @foreach ($INCLUDE_ARCHIVE_ORDER as $key => $value)
            <input type="radio" id="include_archive_order_{{ $key }}" name="include_archive_order" value="{{ $key }}">
            <label for="include_archive_order_{{ $key }}">{{ $value }}</label>
        @endforeach
        @error('include_archive_order')
        <div class="error alert alert-danger">{{ $message }}</div>
        @enderror
        <span data-error-export="include_archive_order"></span>
    </fieldset>
    <fieldset>
        <legend>出力するCSVの項目を決めてください<br>（入力データはドラッグアンドドロップで横に入れ替えられます）</legend>
        @error('select')
        <div class="error alert alert-danger">{{ $message }}</div>
        @enderror
        <span data-error-export="select"></span>
        <div class="buttons">
            <div class="btn btn-primary js-add-column">列を追加</div>
            <div class="custom-select-box">
                <input class="js-add-option-value" type="text" name="block" placeholder="{{ __('追加する選択肢名を入力してください') }}" />
                <div class="btn btn-secondary js-add-option">選択肢を追加</div>
            </div>
        </div>
        <div class="list-group-wrap js-sortable-elms-wrap">
            <div class="js-sortable-elms list-group-items">
                <div class="list-group js-copy-target-group" data-column="1">
                    <div class="list-group-item">
                        <select name="select[]" class="js-select-box">
                            @foreach ($ALLOW_READ_PROPERTIES as $pkey => $array)
                                <option value="{{ $pkey }}">{{ $pkey }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="list-group-item">
                        <div class="btn btn-danger js-delete-column" data-delete-column="1">列を削除</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="document-link">各データの説明は<a href="{{ route('csv.document') }}" target="_blank">データ構造ドキュメント</a>をご確認ください</div>
    </fieldset>
    <fieldset>
        <input type="submit" name="submitter" value="ダウンロードする" class="btn btn-outline-primary" />
    </fieldset>
</form>
<script src="//cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.querySelector('form')
        var bt = document.querySelector('input[name=submitter]')

        var sortableElms = document.querySelector('.js-sortable-elms')
        var SortableInstance = new Sortable(sortableElms, {
            animation: 150
        });

        var wrap = document.querySelector('.js-sortable-elms-wrap')
        if (wrap) {
            wrap.style.width = form.clientWidth + 'px'
        }

        var copyTargetGroup = document.querySelector('.js-copy-target-group')
        var addButton = document.querySelector('.js-add-column')
        if (addButton && copyTargetGroup) {
            var groupWidthNum = copyTargetGroup.clientWidth
            copyTargetGroup.style.width = groupWidthNum + 'px'
            addButton.addEventListener('click', function () {
                var node = copyTargetGroup.cloneNode(true)
                sortableElms.append(node)

                var loopNumber = 1
                var targetGroups = document.querySelectorAll('.js-copy-target-group')
                if (targetGroups.length) {
                    sortableElms.style.width = (targetGroups.length * groupWidthNum) + 'px'
                    targetGroups.forEach(function (elm) {
                        elm.dataset.column = loopNumber
                        var col = elm.querySelector('.js-delete-column')
                        col.dataset.deleteColumn = loopNumber
                        deleteColumnMethod(col)

                        loopNumber++
                    })
                }
            })
        }
        var addOption = document.querySelector('.js-add-option')
        var addOptionValue = document.querySelector('.js-add-option-value')
        if (addOption && addOptionValue) {
            addOption.addEventListener('click', function () {
                var addOptionValue = document.querySelector('.js-add-option-value')
                if (!addOptionValue.value) {
                    alert('{{ __('入力値がありません') }}')
                    return;
                }

                var selectBoxes = document.querySelectorAll('.js-select-box')
                if (selectBoxes) {
                    var option = document.createElement('option')
                    option.text = addOptionValue.value
                    option.value = addOptionValue.value

                    var checker = true
                    var allow = true;
                    selectBoxes.forEach(function (elm) {
                        if (true === checker) {
                            Array.from(elm.options).forEach(function (childEl) {
                                if (true === checker && addOptionValue.value === childEl.value) {
                                    alert('{{ __('既に存在する値です ') }}');
                                    checker = false
                                    allow = false
                                }
                            })
                        }
                        if (true === allow) elm.append(option.cloneNode(true))
                    })
                }
            })
        }
        var deleteButton = document.querySelector('.js-delete-column')
        deleteColumnMethod(deleteButton)

        function deleteColumnMethod(elm)
        {
            elm.addEventListener('click', function (e) {
                document.querySelector('*[data-column="' + elm.dataset.deleteColumn + '"]').remove()
            })
        }

        if (form && bt) {
            bt.addEventListener('click', function (e) {
                e.preventDefault()
                Swal.fire({
                    title: '{{ __('Please wait.') }}',
                    html: '',
                    timerProgressBar: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                })
                var Request = new XMLHttpRequest()
                Request.onreadystatechange = function () {
                    if (Request.readyState === XMLHttpRequest.DONE) {
                        var jsonResponse = false
                        try {
                            var response = JSON.parse(Request.responseText)
                            jsonResponse = true
                        } catch (exception) {
                            console.log('not json')
                        }

                        document.querySelectorAll('.error').forEach(function (erEl) {
                            erEl.remove()
                        })

                        if (jsonResponse && response.errors) {
                            for (var [key, error] of Object.entries(response.errors)) {
                                if (0 < document.querySelectorAll('*[data-error-export="' + key + '"]').length) {
                                    var el = document.createElement('div')
                                    el.classList.add('error', 'alert', 'alert-danger')
                                    el.textContent = error
                                    document.querySelectorAll('*[data-error-export="' + key + '"]').forEach(function (oldEl) {
                                        oldEl.after(el)
                                    })
                                }
                            }
                        } else {
                            form.submit()
                        }

                        Swal.close()
                    }
                }
                Request.open('POST', form.action)
                Request.send(new FormData(form))
            })
        }
    })
</script>
@include('footer')
