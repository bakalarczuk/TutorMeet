<?php

require_once '../includes/functions.php';
$functions = new Functions();
if ($_POST) {
    $content = file_get_contents("../view/" . $_POST['filename'] . ".html");
    $content = str_replace('{aplikant}', $functions->GetAllApplicants(), $content);
    echo $content;
}