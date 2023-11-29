<?php

function setStatusCode(int $status) {
    header("HTTP/", true, $status);
}

function errorResponse(string $error, string $description) {
    return [
        "error" => $error,
        "description" => $description,
        "timestamp" => (new DateTime())->format("c")
    ];
}