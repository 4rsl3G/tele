const axios = require('axios');
const TelegramBot = require('node-telegram-bot-api');

// Token bot Telegram Anda
const token = '7190883171:AAH-9Fu-EnOInHjit7H5_jfahn2dBK4nHYY'; // Ganti dengan token bot Anda

// Inisialisasi bot
const bot = new TelegramBot(token, { polling: true });

// Event listener untuk perintah /tt
bot.onText(/\/tt (.+)/, async (msg, match) => {
    const chatId = msg.chat.id;
    const url = match[1]; // Ambil URL dari pesan

    try {
        // Mengambil data dari API TikWM
        const response = await axios.get(`https://tikwm.com/api/?url=${encodeURIComponent(url)}&count=12&cursor=0&web=1&hd=1`);

        // Pastikan respons dari API TikWM adalah berhasil
        if (response.status !== 200 || !response.data || !response.data.data || !response.data.data.hdplay) {
            throw new Error('Gagal mendapatkan data video dari API TikWM');
        }

        // Ambil URL video dari respons
        const videoUrl = `https://tikwm.com${response.data.data.hdplay}`;

        // Kirim video ke pengguna dengan ukuran asli
        bot.sendVideo(chatId, videoUrl).then(() => {
            console.log('Video berhasil dikirim.');
        }).catch(error => {
            console.error('Gagal mengirim video:', error);
            bot.sendMessage(chatId, 'Gagal mengirim video.');
        });
    } catch (error) {
        console.error('Terjadi kesalahan:', error.message);
        bot.sendMessage(chatId, 'Terjadi kesalahan dalam melakukan operasi.');
    }
});

// Logging jika bot berjalan
bot.on('polling_error', (error) => {
    console.error('Polling error:', error);
});

console.log('Bot Telegram sedang berjalan...');
