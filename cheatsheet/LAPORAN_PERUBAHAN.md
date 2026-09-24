# 📋 Laporan Perubahan - Fitur Halaman Products

## 🎯 Tujuan
Membuat halaman untuk menampilkan tabel data products menggunakan Laravel + Inertia.js + Vue.

---

## 📁 Files yang Diubah

| # | File | Status | Penjelasan |
|---|------|--------|------------|
| 1 | `app/Http/Controllers/ProductController.php` | ✏️ Diubah | Dari class kosong → ditambah method `index()` |
| 2 | `routes/web.php` | ✏️ Diubah | Ditambah route `GET /products` |
| 3 | `resources/js/pages/Products.vue` | 🆕 Dibuat baru | Halaman Vue dengan tabel products |
| 4 | `resources/js/components/AppSidebar.vue` | ✏️ Diubah | Ditambah menu "Products" di sidebar |

---

## 📝 Detail Perubahan

### 1. ProductController.php
```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();

        return Inertia::render('Products', [
            'products' => $products,
        ]);
    }
}
```
**Penjelasan:**
- Import `Product` model dan `Inertia` facade
- Method `index()` mengambil semua data products dari database (diurutkan terbaru)
- Menggunakan `Inertia::render()` untuk merender Vue component `Products.vue`

---

### 2. routes/web.php
```php
<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::get('products', [ProductController::class, 'index'])->name('products');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
```
**Penjelasan:**
- Import `ProductController`
- Route `GET /products` ditempatkan **di luar** middleware auth (bisa diakses tanpa login)
- Route mengarah ke `ProductController::class` method `index`

---

### 3. resources/js/pages/Products.vue (FILE BARU)
```vue
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

interface Product {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    price: number;
    discount_price: number | null;
    stock: number;
    sku: string | null;
    image: string | null;
    is_active: boolean;
    created_at: string;
}

defineProps<{
    products: Product[];
}>();

function formatPrice(price: number): string {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
}
</script>

<template>
    <Head title="Products" />
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Products</h1>
        </div>
        <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
            <div class="p-4">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-sidebar-border/70 dark:border-sidebar-border">
                        <tr>
                            <th class="pb-3 font-medium">Name</th>
                            <th class="pb-3 font-medium">Price</th>
                            <th class="pb-3 font-medium">Discount</th>
                            <th class="pb-3 font-medium">Stock</th>
                            <th class="pb-3 font-medium">SKU</th>
                            <th class="pb-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in products" :key="product.id"
                            class="border-b border-sidebar-border/70 dark:border-sidebar-border">
                            <td class="py-3">
                                <div>
                                    <div class="font-medium">{{ product.name }}</div>
                                    <div v-if="product.description" class="text-xs text-gray-500">
                                        {{ product.description }}
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">{{ formatPrice(product.price) }}</td>
                            <td class="py-3">
                                <span v-if="product.discount_price" class="text-green-600">
                                    {{ formatPrice(product.discount_price) }}
                                </span>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td class="py-3">
                                <span :class="{
                                    'text-red-600': product.stock === 0,
                                    'text-yellow-600': product.stock > 0 && product.stock <= 10,
                                    'text-green-600': product.stock > 10,
                                }">{{ product.stock }}</span>
                            </td>
                            <td class="py-3 font-mono text-xs">{{ product.sku || '-' }}</td>
                            <td class="py-3">
                                <span :class="{
                                    'rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800': product.is_active,
                                    'rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800': !product.is_active,
                                }">
                                    {{ product.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="products.length === 0">
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                No products found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
```
**Penjelasan:**
- Membuat TypeScript interface `Product` sesuai struktur database
- `defineProps` untuk menerima data products dari backend
- `formatPrice()` untuk format mata uang Rupiah (IDR)
- Tabel dengan 6 kolom: Name, Price, Discount, Stock, SKU, Status
- Warna dinamis: stock hijau/kuning/merah, badge Active/Inactive

---

### 4. AppSidebar.vue
```vue
<!-- Perubahan import icon -->
<script setup lang="ts">
// Sebelum
import { BookOpen, FolderGit2, LayoutGrid } from '@lucide/vue';

// Sesudah
import { BookOpen, FolderGit2, LayoutGrid, ShoppingBag } from '@lucide/vue';
</script>

<!-- Perubahan navigation items -->
<script setup lang="ts">
const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    // Ditambah:
    {
        title: 'Products',
        href: '/products',
        icon: ShoppingBag,
    },
];
</script>
```
**Penjelasan:**
- Import `ShoppingBag` icon dari Lucide icons
- Menambahkan menu "Products" di sidebar navigation

---

## 🔑 Konsep yang Digunakan

| Konsep | Penjelasan |
|--------|------------|
| **Inertia.js** | Bridge antara Laravel backend dan Vue frontend, memungkinkan SPA tanpa API terpisah |
| **Wayfinder** | Laravel plugin untuk type-safe route generation di TypeScript |
| **Controller** | Class PHP yang menangani request HTTP dan mengirim response |
| **Vue Component** | File `.vue` untuk frontend UI dengan Composition API (`<script setup>`) |
| **Props** | Data yang dikirim dari parent (backend) ke child component (Vue) |
| **Middleware** | Laravel mekanisme untuk filter request (contoh: auth middleware) |

---

## 🚀 Cara Menjalankan

```bash
# 1. Pastikan MySQL running (XAMPP)

# 2. Buat database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS laravels;"

# 3. Jalankan migration
cd laravels
php artisan migrate

# 4. (Opsional) Seed data products
php artisan tinker
```

```php
// Di tinker:
App\Models\Product::create([
    'name' => 'Laptop',
    'slug' => 'laptop',
    'price' => 10000000,
    'stock' => 50
]);
App\Models\Product::create([
    'name' => 'Mouse',
    'slug' => 'mouse',
    'price' => 150000,
    'stock' => 100
]);
```

```bash
# 5. Jalankan dev server
php artisan serve

# 6. Buka browser
# http://localhost:8000/products
```

---

## ⚠️ Catatan Penting

1. **Kenapa edit `index.blade.php` tidak berpengaruh?**
   - Project ini pakai **Inertia.js + Vue**, bukan Blade template biasa
   - Route `/` menampilkan `resources/js/pages/Welcome.vue`, bukan Blade view
   - Jadi halaman harus dibuat sebagai Vue component di `resources/js/pages/`

2. **Kenapa harus login?**
   - Route awalnya di dalam middleware `auth` group
   - Solusi: pindahkan route ke luar middleware auth

3. **Database belum ada?**
   - Harus run `php artisan migrate` untuk membuat tabel products
   - Error "Connection refused" = MySQL belum running di XAMPP
