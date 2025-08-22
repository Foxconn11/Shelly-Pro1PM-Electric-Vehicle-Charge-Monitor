<?php
// Datenbank-Zugangsdaten anpassen
$host = "localhost";
$user = "EVShelly";
$pass = "Fqt[.tyvBaeZY@Ts";
$db = "EVShelly";

// Verbindung
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB-Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// SQL zum Erstellen der Tabelle
$sql = "CREATE TABLE IF NOT EXISTS ladehistorie (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    datum TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    kmstand INT(11) NOT NULL,
    kwh DOUBLE NOT NULL,
    geladene_kwh DOUBLE NOT NULL,
    verbrauch DOUBLE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

if ($conn->query($sql) === TRUE) {
    echo "Tabelle 'ladehistorie' erfolgreich erstellt oder existiert bereits.";
} else {
    echo "Fehler beim Erstellen der Tabelle: " . $conn->error;
}

$conn->close();
?>
