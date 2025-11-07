<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>製品の新規登録</title>
    <style>
        body { font-family: sans-serif; margin: 2em; }
        form > div { margin-bottom: 1em; }
        label { display: block; font-weight: bold; margin-bottom: 0.2em; }
        input, select, button { padding: 8px; width: 300px; box-sizing: border-box; }
        button { width: auto; cursor: pointer; }
    </style>
</head>
<body>
    <h1>製品の新規登録</h1>

    <form action="{{ route('products.store') }}" method="POST">
        @csrf 


        <div>
            <label for="model_number">型番</label>
            <input type="text" name="model_number" id="model_number" required>
        </div>
        
        <div>
            <label for="name">製品名</label>
            <input type="text" name="name" id="name" required>
        </div>
        
        <div>
            <label for="manufacturer">メーカー</label>
            <input type="text" name="manufacturer" id="manufacturer" required>
        </div>
        
        <div>
            <label for="category">カテゴリ</label>
            <input type="text" name="category" id="category" required>
        </div>

        <div>
            <label for="purchase_date">購入日</label>
            <input type="date" name="purchase_date" id="purchase_date" required>
        </div>

        <button type="submit">登録する</button>
    </form>
</body>
</html>