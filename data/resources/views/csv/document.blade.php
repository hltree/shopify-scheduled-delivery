@include('header')
<style>
    table tr:nth-child(odd) {
        background-color: initial;
        border: 2px solid #eee;
    }
    th {
        background-color: #eee;
    }
    td {
        border: 2px solid #eee;
    }
    th, td {
        font-size: .9em;
        padding: 10px;
        text-align: left!important;
    }
    .max-w-6xl {
        padding-top: 20px;
    }
</style>
<h2>出力CSV データ構造ドキュメント</h2>
<p>出力CSVのデータ構造を下記にまとめています</p>
<table>
    <thead>
    <th>プロパティ名</th>
    <th>説明</th>
    <th>出力形式</th>
    </thead>
    <tbody>
    @foreach($properties as $pname => $ps)
    <tr>
        <td>{{ $pname }}</td>
        <td>{{ $ps['description'] }}</td>
        <td>{{ $ps['return'] }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
@include('footer')
