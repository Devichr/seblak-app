<template>
    <div>
        <h1 class="text-2xl font-bold mb-6">Kelola Metode Pembayaran</h1>
        <!-- Form Tambah Metode Pembayaran -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-lg font-bold mb-4">Tambah Metode Pembayaran</h2>
            <form @submit.prevent="submitPaymentMethod">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium mb-1">Nama Metode</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="w-full border rounded-lg px-4 py-3 focus:ring focus:ring-blue-300"
                        placeholder="Contoh: QRIS"
                        required
                    />
                </div>
                <div class="mb-4">
                    <label for="bank_name" class="block text-sm font-medium mb-1">Nama Bank</label>
                    <input
                        id="bank_name"
                        v-model="form.bank_name"
                        type="text"
                        class="w-full border rounded-lg px-4 py-3 focus:ring focus:ring-blue-300"
                        placeholder="Contoh: BCA"
                        required
                    />
                </div>
                <div class="mb-4">
                    <label for="account_holder" class="block text-sm font-medium mb-1">Pemilik rekening</label>
                    <input
                        id="account_holder"
                        v-model="form.account_holder"
                        type="text"
                        class="w-full border rounded-lg px-4 py-3 focus:ring focus:ring-blue-300"
                        placeholder="Contoh: Seblak Encol"
                        required
                    />
                </div>
                <div class="mb-4">
                    <label for="account_number" class="block text-sm font-medium mb-1">Nomor Akun</label>
                    <input
                        id="account_number"
                        v-model="form.account_number"
                        type="text"
                        class="w-full border rounded-lg px-4 py-3 focus:ring focus:ring-blue-300"
                        placeholder="Contoh: 123456789"
                    />
                </div>
                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium mb-1">Gambar</label>
                    <input
                        id="image"
                        type="file"
                        @change="handleFileUpload"
                        class="w-full border rounded-lg px-4 py-3 focus:ring focus:ring-blue-300"
                        accept="image/*"
                    />
                </div>
                <button
                    type="submit"
                    class="bg-blue-500 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition"
                >
                    Tambah
                </button>
            </form>
        </div>

        <!-- Daftar Metode Pembayaran -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-bold mb-4">Daftar Metode Pembayaran</h2>
            <ul>
                <li
                    v-for="method in paymentMethods"
                    :key="method.id"
                    class="mb-4 flex items-center justify-between"
                >
                    <div class="flex items-center">
                        <img
                            :src="`/storage/${method.image}`"
                            alt="Metode Pembayaran"
                            class="h-16 w-16 object-cover rounded-lg mr-4"
                        />
                        <div>
                            <p class="font-bold">{{ method.name }}</p>
                            <p class="font-bold text-sm text-gray-500">Bank : {{ method.bank_name }}</p>
                            <p class="text-sm text-gray-500 font-bold">Akun: {{ method.account_number }}</p>
                            <p class="font-bold text-sm text-gray-500">Pemilik Rekening : {{ method.account_holder }}</p>
                        </div>
                    </div>
                    <button
                        @click="deletePaymentMethod(method.id)"
                        class="text-red-600 hover:underline"
                    >
                        Hapus
                    </button>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
    name: 'KelolaMetodePembayaran',
    setup() {
        const form = ref({ name: '',bank_name:'',account_holder:'', account_number: '', image: '' });
        const file = ref(null);
        const paymentMethods = ref([]);

        // Fetch data metode pembayaran dari API
        const fetchPaymentMethods = async () => {
            try {
                const response = await axios.get('/payment-methods');
                paymentMethods.value = response.data;
            } catch (error) {
                console.error('Gagal mengambil data metode pembayaran:', error);
            }
        };

        // Meng-handle upload file
        const handleFileUpload = (event) => {
            file.value = event.target.files[0];
        };

        // Menambahkan metode pembayaran baru
        const submitPaymentMethod = async () => {
            try {
                const formData = new FormData();
                formData.append('name', form.value.name);
                formData.append('bank_name', form.value.bank_name);
                formData.append('account_holder', form.value.account_holder);
                formData.append('account_number', form.value.account_number);
                if (file.value) {
            formData.append('image', file.value);
        }

                const response = await axios.post('/payment-methods', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });

                paymentMethods.value.push(response.data); // Tambahkan ke daftar
                form.value.name = ''; // Reset form
                form.value.account_number = '';
                file.value = null;
                alert('Metode pembayaran berhasil ditambahkan!');
            } catch (error) {
                console.error('Gagal menambah metode pembayaran:', error);
                alert('Terjadi kesalahan saat menambah metode pembayaran.');
            }
        };

        // Menghapus metode pembayaran
        const deletePaymentMethod = async (id) => {
            if (confirm('Apakah Anda yakin ingin menghapus metode pembayaran ini?')) {
                try {
                    await axios.delete(`/payment-methods/${id}`);
                    paymentMethods.value = paymentMethods.value.filter(
                        (method) => method.id !== id
                    );
                    alert('Metode pembayaran berhasil dihapus!');
                } catch (error) {
                    console.error('Gagal menghapus metode pembayaran:', error);
                    alert('Terjadi kesalahan saat menghapus metode pembayaran.');
                }
            }
        };

        // Panggil fetch data saat komponen dimuat
        onMounted(fetchPaymentMethods);

        return {
            form,
            file,
            paymentMethods,
            handleFileUpload,
            submitPaymentMethod,
            deletePaymentMethod,
        };
    },
};
</script>

