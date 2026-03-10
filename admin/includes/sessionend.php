<?php
require_once "functions.php";
require_once 'config.php';

$functions = new Functions;

$template = file_get_contents("templates/_end_session.html");

$durationsec = time() - $_SESSION["starttime"];

$durationh = $durationsec/(60*60);

$duration = $functions->ceilToFraction($durationh, 4);
//echo $durationsec.", ".$durationh.", ".$duration;

$functions->EndSession($_SESSION["session_last_id"], date("Y-m-d H:i:s"), $duration);

$template = str_replace("{sessionid}", $_SESSION["session_last_id"], $template);
$template = str_replace("{duration}", $duration, $template);

echo $template;