# Shopify Custom CSV Export App

名前は適当につけました。お察しください。

## Requirements

用意していただくものは下記の要件を満たすサーバーのみです。

当アプリは Laravel Framework 8.83.8 で動作しています。

利用要件は [公式](https://readouble.com/laravel/8.x/ja/deployment.html?header=%25E3%2582%25B5%25E3%2583%25BC%25E3%2583%2590%25E8%25A6%2581%25E4%25BB%25B6) をご確認ください。

## Installation

アプリの導入方法は [こちら](./documentation/Installation.md) を参照ください。

## Usage

### 日時指定フィールドをカート画面に表示させる 

!! アプリインストール後に行ってください !!

Dawnテーマで当アプリを適用する方法は下記の通りです

1. 適用したいストアのテーマ編集から `main-cart-items.liquid` を開きます
2. `<form></form>` タグの中に `{% render 'form-scheduled-delivery' %}` を埋め込んでください

### 日時指定フィールドの名前を変更したい

[form-scheduled-delivery.php](./data/resources/views/snippets/form-scheduled-delivery.blade.php)を開いて
```
<label for="scheduled-delivery">配送希望日</label>
<input type="text" id="scheduled-delivery" name="attributes[配送希望日]" />
```

「配送希望日」を任意の値に変更してください。

### アプリ側でスニペットを編集したけど、テーマに適用されない

`{アプリのURL}/sendAuthorize` に再度アクセスしてください

### スニペットに変更してはいけないコードはありますか

```
<input type="text" id="scheduled-delivery" name="attributes[配送希望日]" />
<input type="hidden" name="attributes[form-scheduled-delivery-key-name]" />
```

この二つのinputフィールドのidは変更しないでください

また、その上のJavaScript記述
```
var fsd = document.querySelector('#scheduled-delivery')
var fsdh = document.querySelector('#scheduled-delivery-hidden')
if (fsd && fsdh) {
    var inputName = fsd.attributes.name.textContent
    var matches = inputName.match(/^attributes\[(.*)\]/)
    if (matches[1]) {
        fsdh.setAttribute('value', matches[1])
    }
}
```
こちらも変更しないでください