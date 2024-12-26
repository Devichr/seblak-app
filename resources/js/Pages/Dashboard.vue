<template>
    <div class="flex bg-gray-100 min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-blue-600 text-white p-6">
            <h1 class="text-xl font-bold mb-6">Admin Panel</h1>
            <nav>
                <ul>
                    <li>
                        <button @click="navigate('KelolaMenu')" class="block w-full text-left p-3 rounded hover:bg-blue-700">
                            Kelola Menu
                        </button>
                    </li>
                    <li>
                        <button @click="navigate('KelolaOrder')" class="block w-full text-left p-3 rounded hover:bg-blue-700">
                            Kelola Order
                        </button>
                    </li>
                    <li>
                        <button @click="navigate('KelolaMetodePembayaran')" class="block w-full text-left p-3 rounded hover:bg-blue-700">
                            Kelola Metode Pembayaran
                        </button>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Konten -->
        <main class="flex-1 p-6">
            <component :is="currentView" />
        </main>
    </div>
</template>

<script>
import KelolaMenu from './KelolaMenu.vue';
import KelolaOrder from './KelolaOrder.vue';
import KelolaMetodePembayaran from './KelolaMetodePembayaran.vue';

export default {
    components: {
        KelolaMenu,
        KelolaOrder,
        KelolaMetodePembayaran,
    },
    data() {
        return {
            currentView: 'KelolaMenu', // Default view
        };
    },
    methods: {
        navigate(view) {
            this.currentView = view;
        },
    },
};
</script>

<style scoped>
/* Tambahkan styling sesuai kebutuhan */
</style>
