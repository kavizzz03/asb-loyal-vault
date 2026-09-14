<?php

$serverName = "192.168.1.16";

$connectionOptions = [
    "Database" => "Mypos_DB",
    "Uid" => "sa",
    "PWD" => "asb@123",
    "TrustServerCertificate" => true
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die(json_encode([
        "success" => false,
        "message" => "Database connection failed",
        "error" => sqlsrv_errors()
    ]));
}