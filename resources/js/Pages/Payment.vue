<template>
    <div v-if="isSubmitting" class="text-center p-6">
        <p>Memproses pesanan Anda...</p>
    </div>

    <div v-else class="bg-gray-100 min-h-screen p-6">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-6">
            <!-- Jika metode pembayaran tunai -->
            <div v-if="paymentMethod === 'cash'" class="text-center">
                <h1 class="text-xl font-bold mb-4">Pesanan Berhasil!</h1>
                <p>
                    Pesanan telah berhasil dikirimkan. Silakan menuju kasir untuk melakukan pembayaran dan konfirmasi
                    pesanan.
                </p>
                <button
                    @click="goToHome"
                    class="mt-4 bg-blue-600 text-white px-4 py-2 rounded"
                >
                    Kembali ke Halaman Utama
                </button>
            </div>

            <!-- Jika metode pembayaran non-tunai -->
            <div v-else-if="paymentMethod === 'non-cash'" class="text-center">
                <h1 class="text-xl font-bold mb-4">Lakukan Pembayaran</h1>
                <p>Silakan lakukan pembayaran dengan melakukan transfer ke nomor rekening di bawah:</p>

                <div class="bg-gray-100 p-4 rounded shadow-md my-4">
                    <p><strong>Bank:</strong> {{ paymentDetails.bank_name }}</p>
                    <p><strong>No. Rekening:</strong> {{ paymentDetails.account_number }}</p>
                    <p><strong>Nama:</strong> {{ paymentDetails.account_holder }}</p>
                    <p v-if="paymentDetails.qr_image">
                        <strong>QR Code:</strong>
                    </p>
                    <img
                        v-if="paymentDetails.qr_image"
                        :src="`/storage/${paymentDetails.qr_image}`"
                        alt="QR Code"
                        class="my-2 mx-auto w-48 h-48"
                    />

                    <button
                        v-if="paymentDetails.qr_image"
                        @click="downloadQR"
                        class="bg-green-600 text-white px-4 py-2 rounded mt-4"
                    >
                        Download QR Code
                    </button>

                    <button
                        @click="copyAccountNumber"
                        class="bg-gray-600 text-white px-4 py-2 rounded mt-4"
                    >
                        Salin Nomor Rekening
                    </button>
                </div>

                <p>Jika sudah, silakan menuju kasir untuk konfirmasi pesanan Anda.</p>

                <p class="text-red-600 mt-4">
                    <strong>Sisa waktu pembayaran:</strong> {{ timeLeft }} menit
                </p>

                <button
                    @click="goToHome"
                    class="mt-6 bg-blue-600 text-white px-4 py-2 rounded"
                >
                    Kembali ke Halaman Utama
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        paymentMethod: {
            type: String,
            required: true,
        },
        paymentDetails: {
            type: Object,
            default: () => ({}),
        },
        timeLimit: {
            type: Number,
            default: 15,
        },
    },
    data() {
        return {
            isSubmitting: false,
            timeLeft: this.timeLimit, // Waktu pembayaran dalam menit
            countdownInterval: null,
        };
    },
    mounted() {
        // Jika metode pembayaran non-cash, jalankan hitung mundur
        if (this.paymentMethod === 'non-cash') {
            this.startCountdown();
        }
    },
    methods: {
        startCountdown() {
            this.countdownInterval = setInterval(() => {
                if (this.timeLeft > 0) {
                    this.timeLeft--;
                } else {
                    clearInterval(this.countdownInterval);
                    this.goToHome();
                }
            }, 60000); // Interval 1 menit
        },
        goToHome() {
            clearInterval(this.countdownInterval);
            this.$inertia.visit("/"); // Redirect ke halaman utama
        },
        downloadQR() {
            const link = document.createElement("a");
            link.href = this.paymentDetails.qr_image;
            link.download = "qr-code.png";
            link.click();
        },
        copyAccountNumber() {
            navigator.clipboard.writeText(this.paymentDetails.account_number).then(() => {
                alert("Nomor rekening berhasil disalin!");
            });
        },
    },
};
</script>
