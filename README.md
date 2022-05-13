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