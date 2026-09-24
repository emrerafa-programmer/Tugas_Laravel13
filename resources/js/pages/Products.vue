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

        <div
            class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
        >
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
                        <tr
                            v-for="product in products"
                            :key="product.id"
                            class="border-b border-sidebar-border/70 dark:border-sidebar-border"
                        >
                            <td class="py-3">
                                <div>
                                    <div class="font-medium">{{ product.name }}</div>
                                    <div
                                        v-if="product.description"
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        {{ product.description }}
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">{{ formatPrice(product.price) }}</td>
                            <td class="py-3">
                                <span v-if="product.discount_price" class="text-green-600 dark:text-green-400">
                                    {{ formatPrice(product.discount_price) }}
                                </span>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td class="py-3">
                                <span
                                    :class="{
                                        'text-red-600 dark:text-red-400': product.stock === 0,
                                        'text-yellow-600 dark:text-yellow-400': product.stock > 0 && product.stock <= 10,
                                        'text-green-600 dark:text-green-400': product.stock > 10,
                                    }"
                                >
                                    {{ product.stock }}
                                </span>
                            </td>
                            <td class="py-3 font-mono text-xs">{{ product.sku || '-' }}</td>
                            <td class="py-3">
                                <span
                                    :class="{
                                        'rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300':
                                            product.is_active,
                                        'rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-300':
                                            !product.is_active,
                                    }"
                                >
                                    {{ product.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="products.length === 0">
                            <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400">
                                No products found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
