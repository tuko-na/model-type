# Product Management System (Model Type)

個人またはグループが所有する製品を**「型番（model_number）」**をキーとして管理するWebアプリケーションです。製品に紐づく「インシデント（故障、メンテ履歴）」を記録・管理し、そのデータを「統計」として可視化します。

## Requirements

- PHP ^8.2
- Composer
- Node.js & NPM

## Setup & Installation

このプロジェクトのセットアップには、以下のコマンドを実行してください。これには依存関係のインストール、環境設定、データベースのセットアップ、フロントエンドのビルドが含まれます。

```bash
composer setup
```

コマンドが完了したら、以下のコマンドでサーバーを起動できます。

```bash
php artisan serve
```

## Features

- **型番管理**: 製品を型番で一意に管理し、故障予測などの統計データとしての価値を高めます。
- **インシデント記録**: 故障やメンテナンスの履歴を記録できます。
- **グループ管理**: 家族や組織で製品情報を共有できます。
- **プライバシー保護**: データは認証されたユーザーのみがアクセス可能です。

## Tech Stack

- **Backend**: Laravel 12 (PHP)
- **Frontend**: Livewire, Tailwind CSS
- **Database**: SQLite (Development), MySQL/PostgreSQL (Production)