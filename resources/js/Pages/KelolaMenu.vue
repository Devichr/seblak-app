<template>
    <div class="bg-gray-100 min-h-screen p-6">
        <h1 class="text-2xl font-bold mb-6">Kelola Menu</h1>

        <!-- Form Tambah/Edit Menu -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-lg font-bold mb-4">{{ isEditing ? 'Edit Menu' : 'Tambah Menu' }}</h2>
            <form @submit.prevent="submitMenu">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium mb-1">Nama Menu</label>
                        <input id="name" v-model="form.name" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring focus:ring-blue-300" placeholder="Nama Menu" required />
                    </div>
                    <div>
                        <label for="price" class="block text-sm font-medium mb-1">Harga</label>
                        <input id="price" v-model="form.price" type="number" class="w-full border rounded-lg px-4 py-3 focus:ring focus:ring-blue-300" placeholder="Harga" required />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Gambar</label>
                        <div class="flex items-center mb-4">
                            <label class="mr-6">
                                <input type="radio" v-model="imageInputType" value="url" /> URL
                            </label>
                            <label>
                                <input type="radio" v-model="imageInputType" value="file" /> File Lokal
                            </label>
                        </div>
                        <div v-if="imageInputType === 'url'">
                            <input id="image-url" v-model="form.image" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring focus:ring-blue-300" placeholder="Masukkan URL Gambar" />
                        </div>
                        <div v-if="imageInputType === 'file'">
                            <input id="image-file" type="file" @change="handleFileUpload" class="w-full border rounded-lg px-4 py-3 focus:ring focus:ring-blue-300" accept="image/*" />
                        </div>
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium mb-1">Tipe Menu</label>
                        <select id="type" v-model="form.type" class="w-full border rounded-lg px-4 py-3 focus:ring focus:ring-blue-300" required>
                            <option value="paket">Paket</option>
                            <option value="satuan">Satuan</option>
                            <option value="dessert">Dessert</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="submit" class="bg-blue-500 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition">
                        {{ isEditing ? 'Update' : 'Tambah' }}
                    </button>
                    <button type="button" class="bg-gray-500 text-white px-6 py-3 rounded-lg shadow hover:bg-gray-700 transition" v-if="isEditing" @click="resetForm">
                        Batal
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Menu -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-bold mb-4">Daftar Menu</h2>
            <table class="w-full table-auto border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border p-4 text-left">Nama</th>
                        <th class="border p-4 text-left">Harga</th>
                        <th class="border p-4 text-left">Gambar</th>
                        <th class="border p-4 text-left">Tipe</th>
                        <th class="border p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="menu in menus" :key="menu.id" class="hover:bg-gray-50 transition">
                        <td class="border p-4">{{ menu.name }}</td>
                        <td class="border p-4">{{ menu.price }}</td>
                        <td class="border p-4">
                            <img :src="menu.image.startsWith('http') ? menu.image : `/storage/${menu.image}`" alt="Menu Image" class="h-16 w-16 object-cover rounded-lg" />
                        </td>
                        <td class="border p-4">{{ menu.type }}</td>
                        <td class="border p-4 text-center">
                            <button class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition" @click="editMenu(menu)">
                                Edit
                            </button>
                            <button class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition ml-2" @click="deleteMenu(menu.id)">
                                Hapus
                            </button>
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
    name: 'Kelolamenu',
    setup() {
        const menus = ref([]);
        const form = ref({
            name: '',
            price: '',
            image: '',
            type: 'satuan', // Default value
        });
        const imageInputType = ref('url'); // Default to URL
        const file = ref(null);

        const isEditing = ref(false);
        const editingId = ref(null);

        const fetchMenus = async () => {
            try {
                const response = await axios.get('/menus');
                menus.value = response.data;
            } catch (error) {
                console.error('Gagal mengambil data menu:', error);
            }
        };

        const handleFileUpload = (event) => {
            file.value = event.target.files[0];
        };

        const submitMenu = async () => {
            try {
                const formData = new FormData();
                formData.append('name', form.value.name);
                formData.append('price', form.value.price);
                formData.append('type', form.value.type);

                if (imageInputType.value === 'url') {
                    formData.append('image', form.value.image); // This should be a string (URL)
                } else {
                    formData.append('image', file.value); // This should be a file
                }

                let response;
                if (isEditing.value) {
                    formData.append('_method', 'PUT'); // Laravel method spoofing
                    response = await axios.post(`/menus/${editingId.value}`, formData, {
                        headers: { 'Content-Type': 'multipart/form-data' },
                    });
                    // Update existing menu
                    const index = menus.value.findIndex(menu => menu.id === editingId.value);
                    if (index !== -1) {
                        menus.value[index] = response.data;
                    }
                } else {
                    response = await axios.post('/menus', formData, {
                        headers: { 'Content-Type': 'multipart/form-data' },
                    });
                    // Add new menu to the list
                    menus.value.push(response.data);
                }

                resetForm();
            } catch (error) {
                console.error('Gagal menyimpan menu:', error.response?.data || error);
            }
        };

        const resetForm = () => {
            form.value = { name: '', price: '', image: '', type: 'satuan' };
            imageInputType.value = 'url';
            file.value = null;
            isEditing.value = false;
            editingId.value = null;
        };

        const editMenu = (menu) => {
            form.value.name = menu.name;
            form.value.price = menu.price;
            form.value.image = menu.image; // This will be used if you want to show the current image URL
            form.value.type = menu.type;
            imageInputType.value = menu.image.startsWith('http') ? 'url' : 'file'; // Set the input type based on the image source
            editingId.value = menu.id;
            isEditing.value = true;
        };

        const deleteMenu = async (id) => {
            if (confirm('Apakah Anda yakin ingin menghapus menu ini?')) {
                try {
                    await axios.delete(`/menus/${id}`);
                    // Remove the deleted menu from the local state
                    menus.value = menus.value.filter(menu => menu.id !== id);
                } catch (error) {
                    console.error('Gagal menghapus menu:', error);
                }
            }
        };

        onMounted(fetchMenus);

        return {
            menus,
            form,
            imageInputType,
            handleFileUpload,
            isEditing,
            submitMenu,
            resetForm,
            editMenu,
            deleteMenu,
        };
    },
};
</script>

<style scoped>
/* Styling tambahan */
</style>
