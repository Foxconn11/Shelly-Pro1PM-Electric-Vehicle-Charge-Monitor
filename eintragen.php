<?php

include './config/config.php';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("DB Verbindung fehlgeschlagen: " . $conn->connect_error);

if (isset($_POST['kmstand'])) {
    $kmstand = (int)$_POST['kmstand'];

    // Shelly abrufen
    $url = "http://$shelly_ip/rpc/Shelly.GetStatus";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    $current_kwh = $data['switch:0']['aenergy']['total'] / 1000;

    // Letzten Eintrag abrufen
    $result = $conn->query("SELECT * FROM ladehistorie ORDER BY id DESC LIMIT 1");
    $last = $result->fetch_assoc();

    if ($last) {
        $geladen = $current_kwh - $last['kwh'];
        $verbrauch = ($geladen / ($kmstand - $last['kmstand'])) * 100;
    } else {
        $geladen = 0;
        $verbrauch = 0;
    }

    // Eintrag speichern
    $stmt = $conn->prepare("INSERT INTO ladehistorie (kmstand, kwh, geladene_kwh, verbrauch) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iddd", $kmstand, $current_kwh, $geladen, $verbrauch);
    $stmt->execute();
    $stmt->close();
}

// Tabelle aktualisieren
$result = $conn->query("SELECT * FROM ladehistorie ORDER BY datum DESC");

echo "<tr>
<th>Datum</th>
<th>km</th>
<th>kWh</th>
<th>Geladene kWh</th>
<th>Verbrauch kWh/100km</th>
</tr>";

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
