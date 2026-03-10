<?php
require_once "functions.php";
require_once 'config.php';

$functions = new Functions;

$template = file_get_contents("templates/_approve_session.html");

$session = $functions->EditSession($_GET["id"]);

$template = str_replace("{sessionid}", $session["sessionId"], $template);
$template = str_replace("{duration}", $functions->GetHours($session["duration"]), $template);
$template = str_replace("{durationsec}", $session["duration"], $template);

$template = str_replace("{summary}", $session["summary"], $template);
$recipient = $functions->GetUser($session['mentorid'], "recordId");
//var_dump($recipient);

$template = str_replace("{recipient}", $recipient["id"], $template);
$template = str_replace("{sender}", $_SESSION['id'], $template);


echo $template;