# WP Content Framework (Log module)

[![CI Status](https://github.com/wp-content-framework/log/workflows/CI/badge.svg)](https://github.com/wp-content-framework/log/actions)
[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](http://www.gnu.org/licenses/gpl-2.0.html)
[![PHP: >=5.6](https://img.shields.io/badge/PHP-%3E%3D5.6-orange.svg)](http://php.net/)
[![WordPress: >=3.9.3](https://img.shields.io/badge/WordPress-%3E%3D3.9.3-brightgreen.svg)](https://wordpress.org/)

[WP Content Framework](https://github.com/wp-content-framework/core) のモジュールです。

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
<details>
<summary>Details</summary>

- [要件](#%E8%A6%81%E4%BB%B6)
- [インストール](#%E3%82%A4%E3%83%B3%E3%82%B9%E3%83%88%E3%83%BC%E3%83%AB)
  - [依存モジュール](#%E4%BE%9D%E5%AD%98%E3%83%A2%E3%82%B8%E3%83%A5%E3%83%BC%E3%83%AB)
  - [関連モジュール](#%E9%96%A2%E9%80%A3%E3%83%A2%E3%82%B8%E3%83%A5%E3%83%BC%E3%83%AB)
  - [基本設定](#%E5%9F%BA%E6%9C%AC%E8%A8%AD%E5%AE%9A)
- [Author](#author)

</details>
<!-- END doctoc generated TOC please keep comment here to allow auto update -->

# 要件
- PHP 5.6 以上
- WordPress 3.9.3 以上

# インストール

``` composer require wp-content-framework/log ```

## 依存モジュール
* [db](https://github.com/wp-content-framework/db)
* [cron](https://github.com/wp-content-framework/cron)
* [admin](https://github.com/wp-content-framework/admin)

## 関連モジュール
* [mail](https://github.com/wp-content-framework/mail)
  * メール送信が必要な場合はインストールが必要です。

## 基本設定
- configs/config.php

|設定値|説明|
|---|---|
|capture_shutdown_error|シャットダウンエラーを捕捉するかどうかのデフォルト値を設定|
|target_shutdown_error|対象のshutdownエラーを設定|
|log_level|ログに関する設定|
|suppress_log_messages|除外するエラーメッセージを設定|

- configs/settings.php

|設定値|説明|
|---|---|
|is_valid_log|ログが有効かどうかを設定|
|save_log_term|ログの保存期間を設定 \[default = 2592000]|
|delete_log_interval|ログ削除間隔を設定 \[default = 86400]|
|capture_shutdown_error|シャットダウンエラーを捕捉するかどうかを設定|

# Author
- [GitHub (Technote)](https://github.com/technote-space)
- [Blog](https://technote.space)
