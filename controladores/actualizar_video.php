<?php
session_start();
include "conect_db.php";

if (!isset($_SESSION['usId'])) {
    exit("Acceso denegado");
}

$id = $_SESSION['usId'];

if (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
    exit("No se recibió el video");
}

$video = $_FILES['video'];

$permitidos = ['video/mp4'];
$maxSize = 100 * 1024 * 1024; // 100MB

if (!in_array($video['type'], $permitidos)) {
    exit("Formato no permitido");
}

if ($video['size'] > $maxSize) {
    exit("El video excede el tamaño permitido");
}

/* 🔥 RUTA ABSOLUTA */
$carpeta = $_SERVER['DOCUMENT_ROOT'] . "/inmobiliaria/videos/";

if (!is_dir($carpeta)) {
    mkdir($carpeta, 0777, true);
}

$nombreVideo = "empresa_" . $id . ".mp4";
$rutaFinal = $carpeta . $nombreVideo;

if (!move_uploaded_file($video['tmp_name'], $rutaFinal)) {
    exit("Error al mover el archivo");
}

/* SOLO guardamos la ruta relativa */
$rutaBD = "videos/" . $nombreVideo;

$sql = "UPDATE usuario_acceso SET video=? WHERE id_user=?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("si", $rutaBD, $id);
$stmt->execute();

header("Location: ../adm/adm_perfil.php");
exit();

