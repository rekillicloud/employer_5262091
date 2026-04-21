<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Товары</title>
</head>
<body>
<div class="container mt-4">
    <h1>Товары</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Цена</th>
                <th>Рейтинг</th>
                <th>В наличии</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->rating }}</td>
                    <td>{{ $product->in_stock ? 'Да' : 'Нет' }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Товары не найдены</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $products->links() }}
</div>
</body>
</html>
