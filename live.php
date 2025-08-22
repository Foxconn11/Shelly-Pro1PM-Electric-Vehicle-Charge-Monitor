<?php
$host = "localhost";
$user = "EVShelly";
$pass = "Fqt[.tyvBaeZY@Ts";
$db = "EVShelly";
$shelly_ip = "192.168.0.68";

// DB Verbindung
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("DB Verbindung fehlgeschlagen: " . $conn->connect_error);

// Shelly abrufen
$url = "http://$shelly_ip/rpc/Shelly.GetStatus";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$current_kwh = $data['switch:0']['aenergy']['total'] / 1000;

$current_power = $data['switch:0']['apower'];
$voltage = $data['switch:0']['voltage'];
$current = $data['switch:0']['current'];
$freq = $data['switch:0']['freq'];
$temp = $data['switch:0']['temperature']['tC'];
$pf = $data['switch:0']['pf'];
$switch = $data['switch:0']['output'] ? 'AN' : 'AUS';
$rssi = $data['wifi']['rssi'];
$cloud = $data['cloud']['connected'] ? 'verbunden' : 'nicht verbunden';
$mqtt = $data['mqtt']['connected'] ? 'verbunden' : 'nicht verbunden';
$ws = $data['ws']['connected'] ? 'verbunden' : 'nicht verbunden';
$uptime = $data['sys']['uptime'];
$ram = $data['sys']['ram_free'];
$fs = $data['sys']['fs_free'];

// Letzten Eintrag abrufen
$result = $conn->query("SELECT * FROM ladehistorie ORDER BY id DESC LIMIT 1");
$last = $result->fetch_assoc();
$conn->close();

$geladen_aktuell = $last ? $current_kwh - $last['kwh'] : 0;

echo json_encode([
    'current_kwh' => number_format($current_kwh, 2),
    'geladen_aktuell' => number_format($geladen_aktuell, 2),
    'power' => number_format($current_power, 0),
    'voltage' => number_format($voltage, 1),
    'current' => number_format($current, 2),
    'freq' => number_format($freq, 2),
    'temp' => number_format($temp,1),
    'pf' => number_format($pf,2),
    'switch' => $switch,
    'rssi' => $rssi,
    'cloud' => $cloud,
    'mqtt' => $mqtt,
    'ws' => $ws,
    'uptime' => $uptime,
    'ram' => $ram,
    'fs' => $fs
]);
?>
