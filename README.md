# 日時指定配達ぷらす（仮）

名前は適当につけました。お察しください。

## Installation

基本的なアプリの導入方法は [こちら](https://github.com/hltree/learn-shopify-app/) を参照ください。

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
});
```
こちらも変更しないでください