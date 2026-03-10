<?php
require_once "includes/config.php";

class Settings
{
    public $link;

    function connect()
    {
        $db = new Database;
        $this->link = $db->connect();
    }

    function read()
    {
        $this->connect();
        $sql = "SELECT * FROM settings";
        $content = '<div class="card"><div class="card-body"><div class="row">
<div class="col-lg-12">
        <div class="form-group">
            <label style="font-size: 19px; font-weight: bold;">Mail settings</label>
        </div>
    </div>
</div>
<form method="post" id="settings-form" class="settings-form">';
        if ($stmt = mysqli_prepare($this->link, $sql)) {

            mysqli_stmt_execute($stmt)
                or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            while ($setting = mysqli_fetch_array($rslt)) {

                $template = file_get_contents("templates/_settings.html");
                $template = str_replace("{settingname}", $setting['settingname'], $template);
                $template = str_replace("{fieldname}", $setting['name'], $template);
                $template = str_replace("{value}", $setting['value'], $template);

                $content .= $template;
            }
        }
        $content .= '</div></form></div>';

        echo $content;
    }
}