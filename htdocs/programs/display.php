<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/21/2019
 * Time: 3:45 PM
 */
//require the init file
require_once '../../init.php';

//checks for messages?

//check for loggedin?
# ToDo: confer w/ Dave on whether this page should be locked down by logging in or not. My money is on leave it open.

//get list of all institutions
$progs = Program::getAllPrograms();
//turn that into an array of name/value pairs to pass to the optionsHTML.php file
$progListHelper = array();
foreach($progs as $prog){
    $progListHelper[] = array('text' => $prog['ProgramName'], 'value' => $prog['ProgramId']);
}
//pass the name/value pairs to the file to get the generated HTML for a select list
include_once('/common/classes/optionsHTML.php');
$progListHTML = optionsHTML($progListHelper);

//get the programID from the query string, if it was given
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if($id){
    # ToDo: need a way to get the info that the programs table links to in other tables
     # i.e. Institution name from InstitutionId, Contact info from ContactId, etc.
    $prog = new Program($id);
    $instId = $prog->Attributes['InstitutionId'];
    $contactId = $prog->Attributes['ContactId'];
    $name = $prog->Attributes['ProgramName'];
    $type = $prog->Attributes['ProgramType'];
    $delivery= $prog->Attributes['DeliveryMethod'];
    $access = $prog->Attributes['ProgramAccess'];
    $objectives = $prog->Attributes['ProgramObjectives'];
    $fullTime = $prog->Attributes['FullTimeDuration'];
    $partTime = $prog->Attributes['PartTimeDuration'];
    $reqs = $prog->Attributes['TestingRequirement'];
    $otherReqs = $prog->Attributes['OtherRequirement'];
    $credits = $prog->Attributes['Credits'];
    $year = $prog->Attributes['YearEstablished'];
    $scholarship = $prog->Attributes['Scholarship'];
    $res = $prog->Attributes['EstimatedResidentTuition'];
    $nonRes = $prog->Attributes['EstimatedNonresidentTuition'];
    $cost = $prog->Attributes['COstPerCredit'];
    $ops = $prog->Attributes['ORFlag'];
    $analytics = $prog->Attributes['AnalyticsFlag'];
    $collegeId = $prog->Attributes['CollegeId'];

    # ToDo: implement null checks and display appropriate information if a field is null

    $content = <<<EOT
<h2 class="display-3">{$name}</h2>
<div class="jumbotron">
    <form>
        <div class="form-row">
            <h3>Address Info</h3>
        </div>
        <div class="form-row">
            <div class="col-md-3">
                <label for="InstitutionAddress">Address</label>
                <input type="text" class="form-control" name="InstitutionAddress" value="{$addr}" id="InstitutionAddress" readonly />
            </div>
            <div class="col-md-3">
                <label for="InstitutionCity">City</label>
                <input type="text" class="form-control" name="InstitutionCity" value="{$city}" id="InstitutionCity" readonly />
            </div>
            <div class="col-md-3">
                <label for="InstitutionState">State</label>
                <input type="text" class="form-control" name="InstitutionState" value="{$state}" id="InstitutionState" readonly />
            </div>
            <div class="col-md-3">
                <label for="InstitutionZip">Zip Code</label>
                <input type="text" class="form-control" name="InstitutionZip" value="{$zip}" id="InstitutionZip" readonly />
            </div>
        </div>
        <br />
        <div class="form-row">
            <h3>Contact Info</h3>
        </div>
        <div class="form-row">
            <div class="col-md-6">
                <label for="InstitutionPhone">Phone</label>
                <input type="text" class="form-control" name="InstitutionPhone" value="{$phone}" id="InstitutionPhone" readonly />
            </div>
            <div class="col-md-6">
                <label for="InstitutionEmail">Email</label>
                <input type="text" class="form-control" name="InstitutionEmail" value="{$email}" id="InstitutionEmail" readonly />
            </div>
        </div>
        <br />
        <div class="form-row">
            <h3>Other Info</h3>
        </div>
        <div class="form-row">
            <div class="col-md-12">
                <label for="InstitutionRegion">Region</label>
                <input type="text" class="form-control" name="InstitutionRegion" value="{$region}" id="InstitutionRegion" readonly />
            </div>
        </div>
    </form>
</div>
<div class="row d-block">
    <h2>View Another Program's Info</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="Institution">Select an Program</label>
		    <select class="form-control" id="Institution" name="Institution" aria-describedby="InstitutionHelp" onchange="self.location='display.php?id='+this.options[this.selectedIndex].value">
		        $progListHTML
            </select>
            <p class="text text-muted" id="InstitutionHelp">The list may take a second or two to load, please be patient after clicking the field.</p>
        </div>
    </form>
</div>
EOT;
    # ToDo: determine if the appropriate way to handle this is a select list or table again, or redirect back to the dashboard? Should it
     # be just a repeat of the dashboard page, with a button and ajax to populate the table? Ask Dave what he thinks about it.
} else {
    //invalid input, either not there or not an integer

}