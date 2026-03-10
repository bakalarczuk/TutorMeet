<?php
require_once 'includes/functions.php';

$functions = new Functions();

$template = file_get_contents("templates/_addmeeting.html");
$template = str_replace('{aplicant}', $functions->GetAplicants(false), $template);

$disabled = $_SESSION['privilege']<5 ? "" : $functions->CheckDaily($_SESSION['id']) == 0 ? "disabled" : "";
$template = str_replace('{disabled}', $disabled, $template);
if($_SESSION['privilege']<5):
                $mentors='<div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Get Mentor</label>';
                            $mentors .= $functions->GetMentors();
                        $mentors.='</div>
                    </div>
                </div>';
else:
                            $mentors = "";
endif;
$template = str_replace('{mentors}', $mentors, $template);
echo $template;
