@include('header')
@if(\Illuminate\Support\Facades\Session::has('success'))
    <style>
        .alert.alert-success {
            margin-top: 20px;
        }
    </style>
    <div class="alert alert-success">{{ \Illuminate\Support\Facades\Session::get('success') }}</div>
@endif
<h2>初期設定</h2>
<p>このページでは初期設定ができます。</p>
<table>
    <tr>
        <td>
            <a class="default js-check" href="{{ route('sendAuthorize') }}">初期化（インストール）</a>
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
                    text: "既にインストールされている場合、以前の設定は破棄されます",
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
