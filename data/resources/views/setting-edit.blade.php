@include('header')
<style>
    .alert {
        position: relative;
        padding: 0.75rem 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        border-radius: 0.25rem;
    }

    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
    }

    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }
</style>
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
            }

            document.getElementById('flatpickr-clear').addEventListener('click', function () {
                fp.clear()
            })
        })
    </script>
    <form action="{{ route('setting.update', ['themeId' => $themeId]) }}" method="post">
        @csrf
        @method('PUT')
        <input type="text" id="scheduled-delivery" data-id="multiple" name="close_days" style="border: 1px solid" />
        <span class="underline text-gray-900 dark:text-white" id="flatpickr-clear">クリアする</span>
        <input type="submit" value="保存" />
    </form>
@endisset
@include('footer')
