@include('header')
<h2>設定</h2>
<p>このページではカレンダーの休日設定ができます。</p>
@isset($cEroors)
    @foreach ($cEroors as $error)
        <div class="error alert alert-danger">{{ $error }}</div>
    @endforeach
@else
    <style>
        .max-w-6xl {
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }

        table th, table td {
            text-align: center;
        }

        table tr:nth-child(odd) {
            background-color: #eee
        }

        table a {
            display: block;
            height: 100%;
            padding: 10px 0;
            transition: opacity .5s;
            width: 100%;
        }

        table a:hover {
            opacity: .5;
        }
    </style>
    <p>編集したいカレンダーのあるテーマを選択してください。</p>
    <table>
        @foreach($themes as $themeId => $themeName)
            <tr>
                <td>
                    <a class="default" href="{{ route('setting.edit', ['themeId' => $themeId]) }}">{{ $themeName }}</a>
                </td>
            </tr>
        @endforeach
    </table>
@endisset
@include('footer')
