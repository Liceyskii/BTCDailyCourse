<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BTCUSDT</title>
</head>
<body>
    <h1>Курс BTC</h1>
    <div class="tvchart"></div>
</body>
<script src="https://unpkg.com/lightweight-charts/dist/lightweight-charts.standalone.production.js"></script>

<script>
    const chart = LightweightCharts.createChart(document.body, { width: 1500, height: 600 });
    const lineSeries = chart.addCandlestickSeries();
    fetch('klines.php')
        .then(response => response.json())
        .then(data => {
            const cdata = data.map(d => {
                return { time: d['time'], open: d['open'], high: d['high'], low: d['low'], close: d['close'] }
            });
            console.log(cdata);
            lineSeries.setData(cdata);
        })
        .catch(error => {
            console.error("Ошибка при получении данных:", error);
        });

    chart.timeScale().fitContent();
</script>
</html>