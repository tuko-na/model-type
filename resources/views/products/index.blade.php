<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>製品一覧</title>
    
    <style>
        body { font-family: sans-serif; margin: 2em; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>

    <h1>製品一覧</h1>

    <div style="margin-bottom: 1em;">
        <a href="{{ route('products.create') }}">
            <button type="button">新規登録</button>
        </a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>型番</th>
                <th>製品名</th>
                <th>グループ名</th>
                <th>購入日</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->model_number }}</td>
                    <td>{{ $product->name }}</td>
                    <td>
                        {{ $product->group?->name ?? '未所属' }}
                    </td>
                    <td>{{ $product->purchase_date }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">製品が登録されていません。</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>