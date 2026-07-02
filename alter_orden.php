<?php
$conn = new mysqli('192.168.60.194', 'root', '', 'gpoascen_congresos');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "ALTER TABLE premios_evento ADD COLUMN OrdenSorteo INT DEFAULT 0 AFTER TipoPremio";
if ($conn->query($sql) === TRUE) {
    echo "Table altered successfully\n";
} else {
    echo "Error altering table: " . $conn->error . "\n";
}
$conn->close();
?>
