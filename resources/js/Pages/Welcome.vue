<template>
    <div class="bg-gray-100 min-h-screen">
        <!-- Header -->
        <div class="bg-blue-700 text-white px-4 py-3 flex items-center justify-between">
      <div>
        <h1 class="text-xl font-bold">Warung Seblak Encol</h1>
        <p class="text-sm">Status: <span class="font-semibold">Buka</span></p>
      </div>
      <!-- Buttons -->
      <div class="flex space-x-2">
        <button
          class="bg-white text-white px-4 py-2 rounded-full shadow hover:bg-blue-700 transition"
          @click="searchAction"
        >
          🔍
        </button>
        <button
          class="bg-white text-black px-4 py-2 rounded-full shadow hover:bg-gray-700 transition"
          @click="toggleSidebar"
        >
          ☰
        </button>
      </div>
    </div>
        <!-- Main Content -->
        <div class="px-4 py-6">
            <div class="bg-white px-4 py-3 rounded-md shadow-md w-full">
                <h2 class="text-lg font-bold">Tipe Pemesanan</h2>
        <div class="flex items-center space-x-4 mt-4">
          <!-- Tipe Pemesanan -->
          <div class="flex-1">
            <label for="dine" class="block text-sm font-medium mb-1">Tipe</label>
            <select
              id="dine"
              v-model="orderType"
              class="bg-gray-100 text-gray-700 py-2 px-3 rounded-lg w-full"
            >
              <option value="dine-in">Makan di Tempat</option>
              <option value="take-away">Bawa Pulang</option>
            </select>
          </div>

          <!-- No. Meja (Hanya Tampil untuk "Makan di Tempat") -->
          <div class="flex-1" v-if="orderType === 'dine-in'">
            <label for="seat" class="block text-sm font-medium mb-1">No. Meja</label>
            <select
              id="seat"
              v-model="tableNumber"
              class="bg-gray-100 text-gray-700 py-2 px-3 rounded-lg w-full"
            >
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
            </select>
          </div>
        </div>
                <!-- Porsi Section -->
                <div>
                    <h2 class="text-lg font-bold mb-4">Daftar Porsi</h2>
                    <div v-for="(portion, index) in portions" :key="index" class="mb-4">
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-md shadow">
                            <h3 class="font-bold">Porsi {{ index + 1 }}</h3>
                            <button
                                v-if="portions.length > 1"
                                class="text-red-500 text-sm"
                                @click="removePortion(index)"
                            >
                                Hapus Porsi
                            </button>
                        </div>

                        <!-- Topping untuk Porsi -->
                        <div class="mt-2 bg-gray-100 p-3 rounded-md">
                            <h4 class="text-sm font-bold">Topping untuk Porsi {{ index + 1 }}:</h4>
                            <ul>
                                <li
                                    v-for="(topping, tIndex) in portion.toppings"
                                    :key="tIndex"
                                    class="flex justify-between items-center text-sm mt-1"
                                >
                                    <span>{{ topping.name }} - {{ topping.price }}</span>
                                    <button
                                        class="text-red-500 text-sm"
                                        @click="removeTopping(index, tIndex)"
                                    >
                                        Hapus
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Tambah Porsi -->
                <div class="flex justify-between">
                    <button
                    class="bg-green-500 text-white px-4 py-2 rounded h-12"
                    @click="addPortion"
                    >
                    Tambah Pesanan
                </button>

                <!-- Submit Pesanan -->
                <button
                class=" bg-blue-700 text-white px-4 py-2 rounded h-12"
                @click="submitOrder"
                >
                Submit Pesanan
            </button>
        </div>
                </div>

                <!-- Daftar Topping -->
                <div class="mt-6">
                    <h2 class="text-lg font-bold mb-4">Pilih Topping Favoritmu</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div
                            v-for="menu in dineInMenus"
                            :key="menu.id"
                            class="bg-gray-50 rounded-lg shadow-md p-3 flex flex-col items-center"
                        >
                        <img :src="menu.image.startsWith('http') ? menu.image : `/storage/${menu.image}`" alt="Menu Image" class="h-28 w-28 object-cover rounded-lg" />
                            <span>{{ menu.name }} - Rp{{ menu.price }}</span>

                            <!-- Pilih Porsi untuk Topping -->
                            <div class="mt-3">
                                <select v-model="selectedPortion" class="p-2 w-24 border rounded">
                                    <option v-for="(portion, index) in portions" :key="index" :value="index">
                                        Porsi {{ index + 1 }}
                                    </option>
                                </select>
                            </div>

                            <button
                                class="mt-2 bg-blue-500 text-white px-3 py-1 rounded text-sm"
                                @click="selectTopping(menu)">
                                Tambah ke Porsi
                            </button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
           <!-- Sidebar -->
    <transition name="slide">
      <div
        v-if="isSidebarOpen"
        class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 flex justify-end"
      >
        <div class="bg-white w-80 h-full shadow-xl p-6 relative">
          <!-- Close Button -->
          <button
            class="absolute top-4 left-4 text-gray-600 text-xl"
            @click="toggleSidebar"
          >
            X
          </button>

          <!-- Sidebar Content -->
          <div class="flex flex-col items-center mt-10 space-y-6">
            <div class="text-center">
              <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center">
                <i class="text-2xl text-gray-500">👤</i>
              </div>
              <p class="text-lg font-bold mt-2">Masuk sebagai tamu</p>
            </div>

            <div class="w-full space-y-4">
              <button
                class="w-full flex items-center justify-between px-4 py-2 bg-gray-100 rounded-lg"
              >
                <span>Riwayat Pesanan</span>
                <i class="text-gray-500">📄</i>
              </button>
              <button
                class="w-full flex items-center justify-between px-4 py-2 bg-gray-100 rounded-lg"
              >
                <span>Bahasa</span>
                <i class="text-gray-500">🌐</i>
              </button>
              <button
                class="w-full flex items-center justify-between px-4 py-2 bg-gray-100 rounded-lg"
              >
                <span>Kebijakan Privasi</span>
                <i class="text-gray-500">🔒</i>
              </button>
            </div>

            <div class="flex space-x-4">
              <button
                class="flex items-center space-x-2 px-4 py-2 border rounded-lg"
              >
                <i class="text-blue-500">🌐</i>
                <span>Google</span>
              </button>
              <button
                class="flex items-center space-x-2 px-4 py-2 border rounded-lg"
              >
                <i class="text-blue-800">📘</i>
                <span>Facebook</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            isSidebarOpen: false,
            dineInMenus: [], // Daftar topping global
            orderType: "dine-in", // Default value
            tableNumber: null,
            portions: [{ toppings: [] }], // Daftar porsi, masing-masing punya topping sendiri
            selectedPortion: 0, // Indeks porsi yang dipilih untuk menambah topping
            paymentMethod: "cash", // Default payment type
        };
    },
    methods: {
        toggleSidebar() {
            this.isSidebarOpen = !this.isSidebarOpen;
        },
        addPortion() {
            this.portions.push({ toppings: [] });
        },
        removePortion(index) {
            this.portions.splice(index, 1);
        },
        removeTopping(portionIndex, toppingIndex) {
            this.portions[portionIndex].toppings.splice(toppingIndex, 1);
        },
        selectTopping(menu) {
            this.portions[this.selectedPortion].toppings.push({ name: menu.name, price: menu.price });
        },
        async fetchDineInMenus() {
            try {
                const response = await axios.get('/menus');
                this.dineInMenus = response.data;
            } catch (error) {
                console.error("Error fetching dine-in menus:", error);
            }
        },
        async submitOrder() {
            try {
                const response = await axios.post('/orders/create', {
                    orderType: this.orderType,
                    tableNumber: this.tableNumber,
                    portions: this.portions,
                    paymentMethod: this.paymentMethod,
                });
                alert('Pesanan berhasil dibuat!');
                window.location.href = `/orders/${response.data.id}`;
                        } catch (error) {
                console.error('Gagal mengirim pesanan:', error);
                alert('Gagal mengirim pesanan.');
            }
        }
    },
    mounted() {
        this.fetchDineInMenus();
    },
};
</script>
