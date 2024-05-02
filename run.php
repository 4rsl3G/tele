<?php
// Token bot Telegram Anda
$token = '7190883171:AAH-9Fu-EnOInHjit7H5_jfahn2dBK4nHYY'; // Ganti dengan token bot Anda

// URL API TikWM
$urlTikWM = 'https://tikwm.com/api/';

// Fungsi untuk mengirim pesan ke bot Telegram
function sendMessage($chatId, $message) {
    global $token;
    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chatId&text=" . urlencode($message);
    file_get_contents($url);
}

// Fungsi untuk mendapatkan data dari API TikWM
function getDataFromTikWM($url) {
    global $urlTikWM;
    $response = file_get_contents("$urlTikWM?url=" . urlencode($url) . "&count=12&cursor=0&web=1&hd=1");
    return json_decode($response, true);
}

// Mendapatkan input dari pengguna
$update = json_decode(file_get_contents("php://input"), true);
if (isset($update["message"])) {
    $chatId = $update["message"]["chat"]["id"];
    $message = $update["message"]["text"];

    // Menanggapi perintah /start
    if ($message === "/start") {
        sendMessage($chatId, "Selamat datang di Bot JTikBot!\n\nUntuk menggunakan bot ini, cukup kirimkan URL dari video TikTok yang ingin Anda unduh. Bot akan mengunduh video tersebut dan mengirimkannya kepada Anda.\n\nContoh penggunaan:\n/tt <link vidio tiktok>\n\nBot ini dibuat oleh Jhody. Kunjungi website kami di [Tukukripto](https://tukukripto.my.id/) untuk informasi lebih lanjut.\n\nTerima kasih telah menggunakan bot ini!");
    }

    // Menanggapi perintah /tt
    if (strpos($message, "/tt ") === 0) {
        $url = substr($message, 4); // Mengambil URL dari pesan
        $data = getDataFromTikWM($url);

        // Cari dan tampilkan nilai hdplay jika ada dalam objek data
        if (isset($data["data"]["hdplay"])) {
            $videoUrl = "https://tikwm.com" . $data["data"]["hdplay"];

            // Kirim video ke pengguna dengan ukuran asli
            sendMessage($chatId, "Video berhasil diunduh: $videoUrl");
        } else {
            sendMessage($chatId, "Tidak dapat menemukan video TikTok pada URL yang diberikan.");
        }
    }
}
