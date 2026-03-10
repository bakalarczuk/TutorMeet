<?php
require_once "functions.php";
$functions = new Functions;

$template = file_get_contents("templates/_session.html");

$template = str_replace("{displayname}", htmlspecialchars($_GET['displayname'], ENT_QUOTES, 'UTF-8'), $template);
$template = str_replace("{roomname}", htmlspecialchars($_GET['roomname'], ENT_QUOTES, 'UTF-8'), $template);

$db = new Database();
$link = $db->connect();
$sql = "SELECT * FROM calendar WHERE id = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_GET['calid']);
    mysqli_stmt_execute($stmt)
        or die("Unable to execute query: " . $stmt->error);

    $rslt = mysqli_stmt_get_result($stmt);

    $result = mysqli_fetch_array($rslt);
}

mysqli_stmt_close($stmt);
mysqli_close($link);

$aplicants = isset($_SESSION["ap"]) ? $_SESSION["ap"] : $result["aplicants"];

$_SESSION["starttime"] = time();

$functions->StartSession(date("Y-m-d H:i:s"), $_SESSION["recid"], $aplicants, $result['id']);

echo $template;