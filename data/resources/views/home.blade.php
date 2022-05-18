@include('header')
<h2>初期設定</h2>
<p>このページではカレンダーの休日設定ができます。</p>
<table>
    <tr>
        <td>
            <a class="default js-check" href="{{ route('sendAuthorize') }}">初期化（再インストール）</a>
        </td>
    </tr>
</table>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.js-check').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault()
                Swal.fire({
                    title: 'よろしいですか？',
                    text: "以前のデータは削除され、初期化されます",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '実行する'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.href = el.href
                    }
                })
            })
        })
    })
</script>
@include('footer')
