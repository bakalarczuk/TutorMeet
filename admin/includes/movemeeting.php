<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$db = new Database();
$functions = new Functions();

$template = file_get_contents("templates/_movemeeting.html");

$sql3 = "SELECT * FROM calendar WHERE id = ?";
$link = $db->connect();
$stmt3 = mysqli_prepare($link, $sql3)
    or die("Unable to prepare query.");
$calid_param = intval($_GET['calid']);
mysqli_stmt_bind_param($stmt3, "i", $calid_param);

mysqli_stmt_execute($stmt3)
    or die("Unable to execute query.");

$rslt3 = mysqli_stmt_get_result($stmt3);
$rowcount = mysqli_stmt_affected_rows($stmt3);

$c = $rowcount > 0 ? mysqli_fetch_array($rslt3) : null;

$template = str_replace('{calid}', intval($_GET['calid']), $template);
$template = str_replace('{aplicants}', $c['aplicants'], $template);
$template = str_replace('{hours}', $c['duration'] / 60, $template);
$template = str_replace('{daily}', $c['daily'], $template);

$template = str_replace('{title}', $c['title'], $template);
$template = str_replace('{date}', $c['date'], $template);
$template = str_replace('{time}', $c['time'], $template);

echo $template;