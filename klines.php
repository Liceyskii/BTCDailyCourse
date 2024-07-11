<?php

// Подключение библиотеки Bybit API
require_once 'vendor/autoload.php';

use ByBit\SDK\ByBitApi;
use ByBit\SDK\Enums\Category;

// Ваши API ключи
$apiKey = 'Sy3j9TjouO2Lvo8cQc';
$apiSecret = 'PZGfjCDGucbRAOs9Hlml4wf8fb5Mm1P86hZO';

//create private API
$bybitApi = new ByBitApi($apiKey, $apiSecret);

// Get Kline
$params = ['category' => Category::SPOT, 'symbol' => 'BTCUSDT', 'interval' => 'D', 'limit' => 100];
$klines = $bybitApi->marketApi()->getKline($params);

// Настройки подключения к MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "APItesttask";

// Создание подключения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Создание таблицы, если она еще не существует
$sql = "CREATE TABLE IF NOT EXISTS klines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    timestamp BIGINT,
    open DECIMAL(10, 2),
    high DECIMAL(10, 2),
    low DECIMAL(10, 2),
    close DECIMAL(10, 2)
)";
if ($conn->query($sql) === FALSE) {
    echo "Ошибка создания таблицы: " . $conn->error;
}

// Преобразуем данные в нужный формат для Lightweight Charts и MySQL
$chartData = [];
foreach ($klines['list'] as $kline) {
    $timestamp = $kline[0] / 1000;
    $date = date('Y-m-d', $timestamp);
    $chartData[] = [
        'time' => $date, // Преобразование времени в формат YYYY-MM-DD
        'open' => (float)$kline[1],
        'high' => (float)$kline[2],
        'low' => (float)$kline[3],
        'close' => (float)$kline[4],
        'timestamp' => $kline[0],
    ];

    // Вставляем данные в MySQL
    $sql = "INSERT INTO klines (timestamp, open, high, low, close) VALUES (
        " . $kline[0] . ",
        " . $kline[1] . ",
        " . $kline[2] . ",
        " . $kline[3] . ",
        " . $kline[4] . "
    )";
    if ($conn->query($sql) === FALSE) {
        echo "Ошибка вставки данных: " . $conn->error;
    }
}

// Сортируем массив по возрастанию значений 'timestamp'
usort($chartData, function($a, $b) {
    return $a['timestamp'] <=> $b['timestamp'];
});

// Выводим JSON-данные для скрипта
header('Content-type: application/json');
echo json_encode($chartData);

// Закрываем подключение к MySQL
$conn->close();

?>