<?php

function getBitcoinPrice() {
    // Obter a cotação atual do Bitcoin em Real (BRL) usando uma API de terceiros
    $url = "https://api.coinbase.com/v2/prices/BTC-BRL/spot";
    $data = file_get_contents($url);
    $json = json_decode($data);
    $bitcoinPrice = floatval($json->data->amount);
    return $bitcoinPrice;
}

function convertRealToBtc($amount) {
    $bitcoinPrice = getBitcoinPrice();
    $btcAmount = $amount / $bitcoinPrice;
    $btcAmount -= $btcAmount * 0.05;
    return $btcAmount;
}

function convertBtcToReal($amount) {
    $bitcoinPrice = getBitcoinPrice();
    $realAmount = $amount * $bitcoinPrice;
    $realAmount -= $realAmount * 0.05;
    return $realAmount;
}

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar qual conversão foi solicitada
    if (isset($_POST["real_to_btc"])) {
        $realAmount = floatval($_POST["real_amount"]);
        $bitcoinAmount = convertRealToBtc($realAmount);
        $result = "{$realAmount} BRL equivale a " . number_format($bitcoinAmount, 8) . " BTC";
    } elseif (isset($_POST["btc_to_real"])) {
        $bitcoinAmount = floatval($_POST["bitcoin_amount"]);
        $realAmount = convertBtcToReal($bitcoinAmount);
        $result = "{$bitcoinAmount} BTC equivale a " . number_format($realAmount, 8) . " BRL";
    }
}

// Verificar se o visitante está usando um dispositivo móvel
$isMobile = false;
$userAgent = $_SERVER["HTTP_USER_AGENT"];
if (preg_match("/Mobile|Android|iPhone|iPod|BlackBerry|Windows Phone/i", $userAgent)) {
    $isMobile = true;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Conversor de Moedas</title>
    <?php if ($isMobile): ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php endif; ?>
</head>
<body>
    <h1>Conversor de Moedas</h1>

    <h2>Real para Bitcoin</h2>
    <form action="" method="post">
        <label for="real_amount">Valor em Real (BRL):</label>
        <input type="number" step="0.01" name="real_amount" id="real_amount" required>
        <button type="submit" name="real_to_btc">Converter</button>
    </form>

    <h2>Bitcoin para Real</h2>
    <form action="" method="post">
        <label for="bitcoin_amount">Valor em Bitcoin (BTC):</label>
        <input type="number" step="0.00000001" name="bitcoin_amount" id="bitcoin_amount" required>
        <button type="submit" name="btc_to_real">Converter</button>
    </form>

    <?php if (isset($result)): ?>
        <h3>Resultado:</h3>
        <p><?php echo $result; ?></p>
    <?php endif; ?>
</body>
</html>
