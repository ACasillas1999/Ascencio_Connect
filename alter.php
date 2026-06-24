<?php
$conn = new mysqli('192.168.60.194', 'root', '', 'gpoascen_congresos');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "ALTER TABLE premios_evento ADD COLUMN TipoPremio VARCHAR(50) DEFAULT 'sorteo'";
if ($conn->query($sql) === TRUE) {
    echo "Table altered successfully";
} else {
    echo "Error altering table: " . $conn->error;
}
$conn->close();
?>
