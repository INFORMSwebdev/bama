<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/22/2019
 * Time: 1:47 PM
 */
//include the init file
require_once '../../init.php';

//set up response variables
$response = [];
$response['errors'] = [];

if(!isset($_SESSION['loggedIn'])){
    //user is not logged in
    $response['errors'][] = 'You must log in in order to get a list of editable programs.';
}
else {
    $user = new User($_SESSION['loggedIn']);

    //get info for all institutions this user is an editor of
    $insts = $user->getInstitutions();

    # ToDo: add the following stuff to what gets returned here
     # , ,

    if ($insts) {
        $response['success'] = 1;

        $helper = [];
        //check for and update the the response for null fields
        foreach($insts as $foo){
            if(empty($foo['InstitutionRegion'])){
                $foo['InstitutionRegion'] = 'Region information not set.';
            }

            if(empty($foo['InstitutionPhone'])){
                $foo['InstitutionPhone'] = 'Phone number not set.';
            }

            if(empty($foo['InstitutionEmail'])){
                $foo['InstitutionEmail'] = 'Email not set.';
            }

            if(empty($foo['InstitutionAccess'])){
                $foo['InstitutionAccess'] = 'Access link not set.';
            }

            if($user->id > 1){
                $progs = $user->getProgramAssignments(TRUE);
                $prog = $progs[0];
                $foo['ProgramName'] = $prog->Attributes['ProgramName'];
                $foo['ProgramType'] = $prog->Attributes['ProgramType'];
                $foo['ProgramDelivery'] = $prog->Attributes['DeliveryMethod'];

                if(empty($prog->Attributes['ProgramAccess'])){
                    $foo['ProgramAccess'] = 'Access information not set.';
                }
                else {
                    $foo['ProgramAccess'] = "<a href='{$prog->Attributes['ProgramAccess']}' target='_blank'>{$prog->Attributes['ProgramAccess']}</a>";
                }

                if(empty($prog->Attributes['ProgramObjectives'])){
                    $foo['ProgramObjectives'] = 'Objectives not set.';
                }
                else {
                    $foo['ProgramObjectives'] = $prog->Attributes['ProgramObjectives'];
                }

                if(empty($prog->Attributes['FullTimeDuration'])){
                    $foo['ProgramFullTime'] = 'Full time duration not set.';
                }
                else {
                    $foo['ProgramFullTime'] = $prog->Attributes['FullTimeDuration'];
                }

                if(empty($prog->Attributes['PartTimeDuration'])){
                    $foo['ProgramPartTime'] = 'Part time duration not set.';
                }
                else {
                    $foo['ProgramPartTime'] = $prog->Attributes['PartTimeDuration'];
                }

                if(empty($prog->Attributes['TestingRequirements'])){
                    $foo['ProgramTestingRequirements'] = 'Testing requirements not set.';
                }
                else {
                    $foo['ProgramTestingRequirements'] = $prog->Attributes['TestingRequirements'];
                }

                if(empty($prog->Attributes['OtherRequirements'])){
                    $foo['ProgramOtherRequirements'] = 'Other requirements not set.';
                }
                else {
                    $foo['ProgramOtherRequirements'] = $prog->Attributes['OtherRequirements'];
                }

                if(empty($prog->Attributes['Credits'])){
                    $foo['ProgramCredits'] = 'Credits not set.';
                }
                else {
                    $foo['ProgramCredits'] = $prog->Attributes['Credits'];
                }

                if(empty($prog->Attributes['YearEstablished'])){
                    $foo['ProgramEstablished'] = 'Year established not set.';
                }
                else {
                    $foo['ProgramEstablished'] = $prog->Attributes['YearEstablished'];
                }

                if(empty($prog->Attributes['Scholarship'])){
                    $foo['ProgramScholarship'] = 'Scholarship not set.';
                }
                else {
                    $foo['ProgramScholarship'] = $prog->Attributes['Scholarship'];
                }

                if(empty($prog->Attributes['EstimatedResidentTuition'])){
                    $foo['ProgramResidentTuition'] = 'Estimated resident tuition not set.';
                }
                else {
                    $foo['ProgramResidentTuition'] = $prog->Attributes['EstimatedResidentTuition'];
                }

                if(empty($prog->Attributes['EstimatedNonResidentTuition'])){
                    $foo['ProgramNonResidentTuition'] = 'Estimated non-resident tuition not set.';
                }
                else {
                    $foo['ProgramNonResidentTuition'] = $prog->Attributes['EstimatedNonResidentTuition'];
                }

                if(empty($prog->Attributes['CostPerCredit'])){
                    $foo['ProgramCostPerCredit'] = 'Cost per credit not set.';
                }
                else {
                    $foo['ProgramCostPerCredit'] = $prog->Attributes['CostPerCredit'];
                }

                //set up the appropriate message based on flags
                $analFlag = $prog->Attributes['AnalyticsFlag'];
                $orFlag = $prog->Attributes['ORFlag'];
                if($analFlag == 1 && $orFlag == 1){
                    $foo['ProgramAnalyticsOR'] = 'Both Analytics and O.R.';
                }
                else if($analFlag == 0 && $orFlag == 1){
                    $foo['ProgramAnalyticsOR'] = 'O.R.';
                }
                else if($analFlag == 1 && $orFlag == 0){
                    $foo['ProgramAnalyticsOR'] = 'Analytics';
                }
                else {
                    $foo['ProgramAnalyticsOR'] = 'Neither Analytics nor O.R.';
                }

                $foo['ProgramCreated'] = $prog->Attributes['CreateDate'];

                //need to pull more info for the last 2 things
                if(empty($prog->Attributes['ContactId'])){
                    $foo['ContactName'] = $foo['ContactTitle'] = $foo['ContactPhone'] = $foo['ContactEmail'] = 'Contact details not set.';
                }
                else {
                    $progContact = new Contact($prog->Attributes['ContactId']);

                    $foo['ContactName'] = $progContact->Attributes['ContactName'];

                    if(empty($progContact->Attributes['ContactTitle'])){
                        $foo['ContactTitle'] = 'Title not set.';
                    }
                    else {
                        $foo['ContactTitle'] = $progContact->Attributes['ContactTitle'];
                    }

                    if(empty($progContact->Attributes['ContactPhone'])){
                        $foo['ContactPhone'] = 'Phone not set.';
                    }
                    else {
                        $foo['ContactPhone'] = $progContact->Attributes['ContactPhone'];
                    }

                    if(empty($progContact->Attributes['ContactEmail'])){
                        $foo['ContactEmail'] = 'Email not set.';
                    }
                    else {
                        $foo['ContactEmail'] = "<a href='{$progContact->Attributes['ContactEmail']}' target='_blank'>{$progContact->Attributes['ContactEmail']}</a>";
                    }
                }

                if(empty($prog->Attributes['CollegeId'])){
                    $foo['CollegeName'] = $foo['CollegeType'] = $foo['CollegeCreated'] = 'College details not set.';
                }
                else {
                    $prodCollege = new College($prod->Attributes['CollegeId']);
                    $foo['CollegeName'] = $prodCollege->Attributes['CollegeName'];
                    $foo['CollegeType'] = $prodCollege->Attributes['CollegeType'];
                    $foo['CollegeCreated'] = $prodCollege->Attributes['CreateDate'];
                }
            }

            $helper[] = $foo;
        }


        $response['institutions'] = $helper;
    }
    else {
        $response['errors'][] = 'You are not assigned as an administrator of any institutions.';
    }
}

//echo the response (as JSON) so the page that needs it can get the info from this script
echo json_encode($response);