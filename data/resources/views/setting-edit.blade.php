@include('header')
<h2>設定</h2>
<p>休日設定をして保存をクリックしてください</p>
@if(isset($cEroors))
    @foreach ($cEroors as $error)
        <div class="error alert alert-danger">{{ $error }}</div>
    @endforeach
    <a class="underline text-gray-900 dark:text-white" href="{{ route('home') }}">トップに戻る</a>
@elseif(\Illuminate\Support\Facades\Session::has('success'))
    <div class="alert alert-success">{{ \Illuminate\Support\Facades\Session::get('success') }}</div>
    <a class="underline text-gray-900 dark:text-white" href="{{ route('setting.edit', ['themeId' => $themeId]) }}">編集テーマに戻る</a>
@else
    <style>
        input[type="submit"] {
            display: block;
            margin-top: 20px;
        }

        fieldset {
            padding: 10px 0;K
        }

        p {
            margin: 0;
        }

        form {
            margin-top: 20px;
        }

        #scheduled-delivery {
            border: 1px solid;
        }
    </style>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="//cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            {!! $flatPickr !!}

            if (undefined !== fp) {
                fp.set('mode', 'multiple')
                fp.set('dateFormat', 'Y-m-d')
                fp.timeContainer.style = 'display: none'

                if (false === fp.config.inline) {
                    document.getElementById('disabled_inline').checked = true
                }
            }

            document.getElementById('flatpickr-clear').addEventListener('click', function () {
                fp.clear()
            })
        })
    </script>
    <form action="{{ route('setting.update', ['themeId' => $themeId]) }}" method="post">
        @csrf
        @method('PUT')
        <fieldset>
            <input type="text" id="scheduled-delivery" data-id="multiple" name="close_days" />
        </fieldset>
        <fieldset>
            <input type="checkbox" name="disabled_inline" id="disabled_inline" /><label for="disabled_inline">カレンダーをインラインで表示しない</label>
        </fieldset>
        <span class="btn btn-outline-secondary" id="flatpickr-clear">クリアする</span>
        <input type="submit" value="保存" class="btn btn-outline-primary" />
    </form>
@endisset
@include('footer')
