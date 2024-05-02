<?php
require_once __DIR__ . '/vendor/autoload.php'; // Pastikan Anda telah menginstal library composer node-telegram-bot-api

use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Message;
use GuzzleHttp\Client as GuzzleClient;

// Token bot Telegram Anda
$token = '7190883171:AAH-9Fu-EnOInHjit7H5_jfahn2dBK4nHYY'; // Ganti dengan token bot Anda

// Inisialisasi bot
$bot = new Client($token);

// Event listener untuk perintah /tt
$bot->command('tt', function ($message, $matches) use ($bot) {
    $chatId = $message->getChat()->getId();
    $url = $matches[1]; // Ambil URL dari pesan

    try {
        // Mengambil data dari API TikWM
        $guzzleClient = new GuzzleClient();
        $response = $guzzleClient->get("https://tikwm.com/api/?url=" . urlencode($url) . "&count=12&cursor=0&web=1&hd=1");

        // Pastikan respons dari API TikWM adalah berhasil
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Gagal mendapatkan data video dari API TikWM');
        }

        $data = json_decode($response->getBody(), true);

        if (empty($data['data']['hdplay'])) {
            throw new Exception('Tidak dapat menemukan video TikTok pada URL yang diberikan.');
        }

        // Ambil URL video dari respons
        $videoUrl = "https://tikwm.com" . $data['data']['hdplay'];

        // Kirim video ke pengguna dengan ukuran asli
        $bot->sendVideo($chatId, $videoUrl);
    } catch (Exception $e) {
        error_log('Terjadi kesalahan: ' . $e->getMessage());
        $bot->sendMessage($chatId, 'Terjadi kesalahan dalam melakukan operasi.');
    }
});

// Jalankan bot
$bot->run();
