<template>
    <div class="bg-gray-100 min-h-screen p-6">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-xl font-bold mb-4">Detail Order</h1>

            <!-- Detail Pesanan -->
            <div v-if="order" class="mb-6">
                <p><strong>Order ID:</strong> {{ order.id }}</p>
                <p><strong>No Meja:</strong> {{ order.table_number || 'Take Away' }}</p>
                <p><strong>Tanggal:</strong> {{ new Date(order.created_at).toLocaleDateString() }}</p>
            </div>

            <!-- Daftar Portions -->
            <div v-if="order.portions && order.portions.length" class="mb-6">
                <h2 class="text-lg font-bold mb-2">Daftar Portions</h2>
                <div v-for="(portion, index) in order.portions" :key="index" class="bg-gray-50 p-3 rounded mb-4 border">
                    <h3 class="font-bold text-sm mb-2">Porsi {{ index + 1 }}</h3>

                    <!-- Daftar Topping -->
                    <div class="mt-2">
                        <h4 class="text-sm font-bold">Topping:</h4>
                        <ul class="list-disc pl-5 text-sm">
                            <li v-for="topping in portion.toppings" :key="topping.name">
                                {{ topping.name }} - Rp{{ parseFloat(topping.price).toLocaleString() }}
                            </li>
                        </ul>
                    </div>

                    <!-- Total Harga Porsi -->
                    <div class="mt-3">
                        <p><strong>Total Harga Porsi:</strong> Rp{{ calculatePortionTotal(portion).toLocaleString() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total Keseluruhan -->
            <div v-if="order.portions && order.portions.length" class="mt-6 text-right">
                <h2 class="text-lg font-bold">Total Keseluruhan:</h2>
                <p class="text-2xl font-semibold">Rp{{ calculateTotal().toLocaleString() }}</p>
            </div>

            <!-- Form Input untuk Pembeli -->
            <div>
                <h2 class="text-lg font-bold mb-2">Form Pembeli</h2>
                <form @submit.prevent="submitOrder">
                    <div class="mb-4">
                        <label for="customer_name" class="block font-semibold mb-1">Nama Pembeli:</label>
                        <input type="text" id="customer_name" v-model="customerName"
                            class="bg-gray-100 p-2 rounded w-full" required />
                    </div>

                    <div class="mb-4">
                        <label for="customer_phone" class="block font-semibold mb-1">Nomor HP:</label>
                        <input type="tel" id="customer_phone" v-model="customerPhone"
                            class="bg-gray-100 p-2 rounded w-full" required />
                    </div>

                    <div class="mb-4">
                        <label for="customer_email" class="block font-semibold mb-1">Email:</label>
                        <input type="email" id="customer_email" v-model="customerEmail"
                            class="bg-gray-100 p-2 rounded w-full" required />
                    </div>

                    <!-- Opsi Metode Pembayaran -->
                    <div class="mb-4">
                        <label for="payment_method" class="block font-semibold mb-1">Metode Pembayaran:</label>
                        <select id="payment_method" v-model="paymentMethod" class="bg-gray-100 p-2 rounded w-full"
                            required>
                            <!-- Tambahkan opsi default "Tunai" -->
                            <option value="cash">Tunai</option>

                            <!-- Render metode pembayaran dari database -->
                            <option v-for="method in paymentMethods" :key="method.id" :value="method.name">
                                {{ method.name }}
                            </option>
                        </select>
                    </div>


                    <!-- Tombol Submit -->
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full">
                        Konfirmasi Pesanan
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        order: Object, // Order diterima dari server berdasarkan ID
    },
    data() {
        return {
            customerName: "",
            customerPhone: "",
            customerEmail: "",
            paymentMethod: "cash", // Default metode pembayaran
            paymentMethods: [], // Daftar metode pembayaran dari database
        };
    },
    mounted() {
        this.fetchPaymentMethods();
    },
    methods: {
        async submitOrder() {
            try {
                await this.$inertia.post(`/orders/${this.order.id}/payment`, {
            customer_name: this.customerName,
            customer_phone: this.customerPhone,
            customer_email: this.customerEmail,
            payment_method: this.paymentMethod,
        });
            } catch (error) {
                console.error("Gagal mengirim pembayaran:", error);
                alert("Gagal mengirim pembayaran. Silakan coba lagi.");
            }
        },

        async fetchPaymentMethods() {
            try {
                const response = await axios.get("/payment-methods"); // Endpoint untuk mengambil metode pembayaran
                this.paymentMethods = response.data || [];
            } catch (error) {
                console.error("Gagal mengambil metode pembayaran:", error);
                this.paymentMethods = []; // Set default jika gagal
            }
        },
        // Hitung total harga untuk setiap porsi
        calculatePortionTotal(portion) {
            if (!portion.toppings) return 0;
            return portion.toppings.reduce((total, topping) => {
                return total + parseFloat(topping.price || 0);
            }, 0);
        },
        // Hitung total keseluruhan
        calculateTotal() {
            if (!this.order.portions) return 0;
            return this.order.portions.reduce((total, portion) => {
                return total + this.calculatePortionTotal(portion);
            }, 0);
        },
    },
};
</script>
