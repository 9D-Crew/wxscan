<?php
$timeZone = date_default_timezone_get();
if (isset($_GET['lat']) && isset($_GET['lon'])) {

    $lat = floatval($_GET['lat']);
    $lon = floatval($_GET['lon']);

    $url = "https://timeapi.io/api/TimeZone/coordinate?latitude={$lat}&longitude={$lon}";
    $response = @file_get_contents($url);

    if ($response) {
        $data = json_decode($response, true);
        if (!empty($data['timeZone'])) {
            $timeZone = $data['timeZone'];
        }
    }
}
?>
<style>
@font-face {
    font-family: "font";
    src: url("/assets/fonts/inter_reg.ttf") format("truetype");
}
.font {
    font-family: "font";
}
</style>

<h1 style="text-align: right; font-size: 18px;" class="font" id="clock">
Loading...
</h1>

<script>
const timeZone = "<?= $timeZone ?>";

function updateClock() {
    const el = document.getElementById("clock");
    const now = new Date();

    const options = {
        timeZone,
        month: "short",
        day: "2-digit",
        hour: "numeric",
        minute: "2-digit",
        second: "2-digit",
        hour12: true
    };

    const formatter = new Intl.DateTimeFormat("en-US", options);
    const parts = formatter.formatToParts(now);

    const month = parts.find(p => p.type === "month").value;
    const day = parts.find(p => p.type === "day").value;
    const hour = parts.find(p => p.type === "hour").value;
    const minute = parts.find(p => p.type === "minute").value;
    const second = parts.find(p => p.type === "second").value;
    const dayPeriod = parts.find(p => p.type === "dayPeriod").value;

    el.innerHTML = `${month} ${day}<br>${hour}:${minute}:${second}${dayPeriod}`;
}

updateClock();
setInterval(updateClock, 1000);
</script>