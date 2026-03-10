<?php
require_once "functions.php";
require_once 'config.php';

$functions = new Functions;

$template = file_get_contents("templates/_edit_session.html");

$session = $functions->EditSession($_GET["id"]);

$template = str_replace("{sessionid}", $session["sessionId"], $template);
$template = str_replace("{duration}", $session["duration"], $template);
$template = str_replace("{summary}", $session["summary"], $template);

$disabled = $session["approved"] == 1 ? "disabled" : "";
$template = str_replace("{disabled}", $disabled, $template);

echo $template;