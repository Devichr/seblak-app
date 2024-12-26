<template>
    <div>
        <h1 class="text-2xl font-bold mb-6">Kelola Order</h1>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-bold mb-4">Daftar Order</h2>
            <table class="w-full table-auto border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border p-4 text-left">Nama Pembeli</th>
                        <th class="border p-4 text-left" style="max-width: 200px;">Email</th>
                        <th class="border p-4 text-left">Nomor Telepon</th>
                        <th class="border p-4 text-left">Nomor Meja</th>
                        <th class="border p-4 text-left" style="width: 40%;">Isi Order</th>
                        <th class="border p-4 text-left">Total Order</th>
                        <th class="border p-4 text-left">Metode Pembayaran</th>
                        <th class="border p-4 text-left">Status</th>
                        <th class="border p-4 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50 transition">
                        <td class="border p-4">{{ order.payment.customer_name || '-' }}</td>
                        <td class="border p-4 max-w-12 break-words">{{ order.payment.customer_email || '-' }}</td>
                        <td class="border p-4">{{ order.payment.customer_phone || '-' }}</td>
                        <td class="border p-4">{{ order.table_number || 'Take Away' }}</td>
                        <td class="border p-4 min-w-64">
                            <ul>
                                <li v-for="(portion, index) in order.portions" :key="index">
                                    <p class="font-bold text-sm mb-2">Porsi {{ index + 1 }} - Rp{{
                                        calculatePortionTotal(portion).toLocaleString() }}</p>
                                    <ul>
                                        <li v-for="(topping, i) in portion.toppings" :key="i"
                                            class="text-sm text-gray-500">
                                            + {{ topping.name }} - Rp{{ topping.price.toLocaleString() }}
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </td>
                        <td class="border p-4">Rp{{ order.total.toLocaleString() }}</td>
                        <td class="border p-4">{{ order.payment.payment_method || 'Belum Menyelesaikan Pembayaran' }}</td>
                        <td class="border p-4">{{ order.status }}</td>
                        <td class="border p-4">
                            <button v-if="order.status !== 'completed'" @click="confirmPayment(order.id)"
                                class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                                Konfirmasi Pembayaran
                            </button>
                            <span v-else class="text-gray-500">Sudah Dibayar</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
    name: 'KelolaOrder',
    setup() {
        const orders = ref([]);

        const fetchOrders = async () => {
            try {
                const response = await axios.get('/orders');
                orders.value = response.data.map(order => {
                    let portions = [];
                    if (typeof order.portions === 'string') {
                        try {
                            portions = JSON.parse(order.portions);
                        } catch (error) {
                            console.error(`Gagal parsing portions untuk order ID: ${order.id}`, error);
                        }
                    } else if (typeof order.portions === 'object' && order.portions !== null) {
                        portions = order.portions;
                    }
                    return { ...order, portions };
                });
            } catch (error) {
                console.error('Gagal mengambil data order:', error);
            }
        };

        const confirmPayment = async (id) => {
            if (confirm('Apakah Anda yakin ingin mengkonfirmasi pembayaran untuk pesanan ini?')) {
                try {
                    await axios.put(`/orders/${id}/confirm`);
                    const order = orders.value.find(o => o.id === id);
                    if (order) order.status = 'completed';
                    alert('Pembayaran berhasil dikonfirmasi!');
                } catch (error) {
                    console.error('Gagal mengkonfirmasi pembayaran:', error);
                    alert('Terjadi kesalahan saat mengkonfirmasi pembayaran.');
                }
            }
        };

        onMounted(fetchOrders);

        return { orders, confirmPayment };
    },
    methods: {
        calculatePortionTotal(portion) {
            if (!portion.toppings) return 0;
            return portion.toppings.reduce((total, topping) => total + parseFloat(topping.price || 0), 0);
        },
    },
};
</script>
