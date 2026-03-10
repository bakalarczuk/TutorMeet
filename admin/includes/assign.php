<?php

require_once 'includes/functions.php';
$functions = new Functions();
echo '
<div id="error">
</div>
<div class="card">
    <div class="card-body">
        <form method="post" id="assign-form" class="assign-form">
            <div class="row">
                <div class="form-group">
                    <label>Select Applicant</label>';
echo $functions->GetAllApplicants();
echo '</div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label>Select Tutor</label>';
echo $functions->GetMentors();
echo '</div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label>Set Hours</label>
 <input class="au-input au-input--full" type="text" name="hours" value="1">
                    </div>
            </div>
         <div class="row">
                <div class="form-group">
                    <label>Select Country</label>   
<select class="au-input--full au-select" name="country">
<option>Select</option>
<option value="0">Poland</option>
<option value="1">Italy</option>
</select>
                </div>
            </div>
 <div class="row">
                <div class="form-group">
<button type="button" class="btn btn-danger" onclick="window.location.href=\'/admin/\';">Cancel</button>
<button type="submit" class="btn btn-success">Save</button>
                </div>
            </div>
                   </form>
    </div>
</div>
';