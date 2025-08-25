<?php

include './config/config.php';

// Verbindung
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("DB Verbindung fehlgeschlagen: " . $conn->connect_error);

// Tabelle abrufen
$result = $conn->query("SELECT * FROM ladehistorie ORDER BY datum DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ladehistorie Elektroauto</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="./style/main.css">
</head>
<body>
<h2>Ladehistorie Elektroauto</h2>

<div id="liveKwh" >
    Aktuelle Shelly-KWh: <span id="currentKwh">--</span> kWh <br>
    Geladen seit letztem Eintrag: <span id="geladenAktuell">--</span> kWh (<span id="geladenWh">--</span> Wh)
</div>

<div id="liveInfo">
    Aktuelle Ladeleistung: <span id="currentPower">--</span> W <br>
    Netzspannung: <span id="currentVoltage">--</span> V <br>
    Stromstärke: <span id="currentCurrent">--</span> A <br>
    Frequenz: <span id="currentFreq">--</span> Hz
</div>

<!-- Ausklappbare Details -->
<button id="toggleDetails">Weitere Details anzeigen</button>

<div id="extraDetails">
    Temperatur: <span id="currentTemp">--</span> °C <br>
    Leistungsfaktor: <span id="currentPf">--</span> <br>
    Schaltzustand: <span id="currentSwitch">--</span> <br>
    WLAN Signalstärke: <span id="currentRssi">--</span> dBm <br>
    Cloud Status: <span id="currentCloud">--</span> <br>
    MQTT Status: <span id="currentMqtt">--</span> <br>
    WebSocket Status: <span id="currentWs">--</span> <br>
    Uptime: <span id="currentUptime">--</span> s <br>
    Freier RAM: <span id="currentRam">--</span> B <br>
    Freier Flash-Speicher: <span id="currentFs">--</span> B <br>
</div>
<!--
<div id="ladeBalkenContainer">
     <div id="ladeBalken">0 kWh</div>
</div>
-->

<form id="ladeForm">
    <label>Kilometerstand:</label>
    <input type="number" name="kmstand" id="kmstand" required>
    <button type="button" id="abrufen">Shelly-KWh abrufen & speichern</button>
</form>

<table id="historie">
<tr>
    <th>Datum</th>
    <th>km</th>
    <th>kWh</th>
    <th>Geladene kWh</th>
    <th>Verbrauch kWh/100km</th>
</tr>
<?php
while($row = $result->fetch_assoc()) {
    echo "<tr>
    <td>{$row['datum']}</td>
    <td>{$row['kmstand']}</td>
    <td>".number_format($row['kwh'],2)."</td>
    <td>".number_format($row['geladene_kwh'],2)."</td>
    <td>".number_format($row['verbrauch'],2)."</td>
    </tr>";
}
$conn->close();
?>
</table>

<script>
$(document).ready(function() {

function updateLiveKwh() {
    $.getJSON("live.php", function(data) {
        let geladen = parseFloat(data.geladen_aktuell);
        $("#currentKwh").text(data.current_kwh);
        $("#geladenAktuell").text(data.geladen_aktuell);
        $("#geladenWh").text((geladen*1000).toFixed(0));

        $("#currentPower").text(data.power);
        $("#currentVoltage").text(data.voltage);
        $("#currentCurrent").text(data.current); // Stromstärke
        $("#currentFreq").text(data.freq);

        // Extra Details
        $("#currentTemp").text(data.temp);
        $("#currentPf").text(data.pf);
        $("#currentSwitch").text(data.switch);
        $("#currentRssi").text(data.rssi);
        $("#currentCloud").text(data.cloud);
        $("#currentMqtt").text(data.mqtt);
        $("#currentWs").text(data.ws);
        $("#currentUptime").text(data.uptime);
        $("#currentRam").text(data.ram);
        $("#currentFs").text(data.fs);
    });
}

// Toggle Button
$("#toggleDetails").click(function() {
    $("#extraDetails").slideToggle();
});

setInterval(updateLiveKwh, 1000);
updateLiveKwh();
    $("#abrufen").click(function() {
        let kmstand = $("#kmstand").val();
        if(kmstand == "") { alert("Bitte Kilometerstand eingeben"); return; }

        $.post("eintragen.php", {kmstand: kmstand}, function(data) {
            $("#historie").html(data);
            $("#kmstand").val("");
            updateLiveKwh();
        });
    });
});
</script>

</body>
</html>
