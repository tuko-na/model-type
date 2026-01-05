# 型番製品管理

個人またはグループが所有する製品を「型番（model_number）」をキーとして管理するWebアプリケーションです。製品に紐づく「インシデント（故障、メンテ履歴）」を記録・管理し、そのデータを「統計」として可視化します。

## Requirements

- PHP ^8.2
- Composer
- Node.js & NPM

## Setup & Installation

このプロジェクトのセットアップには、以下のコマンドを実行してください。これには依存関係のインストール、環境設定、データベースのセットアップ、フロントエンドのビルドが含まれます。

### 現在の作業ブランチはpublic_pageです！　最新の状態を閲覧したい方はお手数ですがブランチの移動までお願いします。

```bash
composer setup
```

コマンドが完了したら、以下のコマンドでサーバーを起動できます。

```bash
php artisan serve
```

## テストデータ
推奨ユーザー
メールアドレス：　test_user10@example.com
パスワード：　　　password

## Features

- **型番管理**: 製品を型番で一意に管理し、故障予測などの統計データとしての価値を高めます。
- **インシデント記録**: 故障やメンテナンスの履歴を記録できます。
- **グループ管理**: 家族や組織で製品情報を共有できます。

## Tech Stack

- **Backend**: Laravel 12 (PHP)
- **Frontend**: Livewire, Tailwind CSS
- **Database**: SQLite (Development), MySQL/PostgreSQL (Production)