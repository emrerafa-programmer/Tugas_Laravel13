<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome - Laravel 13</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-6xl mx-auto px-4 py-10">
        <div class="text-center">
            <h3 class="text-2xl font-bold mb-2">Tutorial Laravel 13 untuk Pemula</h3>
            <h5 class="text-sm">
            </h5>
            <hr class="my-6 border-gray-200">
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-10 text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Laravel 13</h1>
            <p class="text-gray-600 mb-6">Selamat datang di aplikasi Laravel 13.</p>
            <a href="{{ route('products.index') }}"
                class="inline-flex items-center justify-center px-4 py-2 rounded-lg
                bg-emerald-600 text-white text-sm font-semibold
                hover:bg-emerald-700 transition">
                LIHAT DATA PRODUCTS
            </a>
        </div>
    </div>
</body>
</html>