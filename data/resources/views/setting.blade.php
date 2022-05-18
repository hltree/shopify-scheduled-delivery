@include('header')
<h2>配送希望日時カレンダーの設定</h2>
<p>このページではカレンダーの休日設定ができます。</p>
@isset($cEroors)
    @foreach ($cEroors as $error)
        <div class="error alert alert-danger">{{ $error }}</div>
    @endforeach
@else
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
