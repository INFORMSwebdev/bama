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

    if ($insts) {
        $response['success'] = 1;

        $helper = [];
        //check for and update the the response for null fields
        foreach($insts as $foo){
            if(isset($foo['RegionId'])){
                $name = Dropdowns::getInstitutionRegionName($foo['RegionId']);
                if(!empty($name)){
                    $foo['InstitutionRegion'] = $name;
                } else {
                    $foo['InstitutionRegion'] = 'Region information not set.';
                }
            } else {
                $foo['InstitutionRegion'] = 'Region information not set.';
            }

            if(empty($foo['InstitutionPhone'])){
                $foo['InstitutionPhone'] = 'Phone number not set.';
            } else {
                $foo['InstitutionPhone'] = AOREducationObject::formatPhoneNumber($foo['InstitutionPhone']);
            }

            if(empty($foo['InstitutionEmail'])){
                $foo['InstitutionEmail'] = 'Email not set.';
            }

            //if(empty($foo['InstitutionAccess'])){
            //    $foo['InstitutionAccess'] = 'Access link not set.';
            //}

            if($user->id > 1){
                $instHelp = new Institution($foo['InstitutionId']);
                $instProgs = $instHelp->getPrograms(TRUE);
                $programHelper = [];
                if($instProgs) {
                    foreach ($instProgs as $ip) {
                        //get each program's info
                        $helperHelp = [];

                        //get the courses under each program
                        $programCourses = $ip->getCourses(TRUE, TRUE);
                        $courseHelper = [];
                        foreach($programCourses as $course){
                            //this is only to display on the index page, we don't need all the fields from the DB
                            $courseBuilder = [];
                            $courseBuilder['CourseId'] = $course->id;
                            $courseBuilder['CourseTitle'] = $course->Attributes['CourseTitle'];
                            //null/empty checks
                            if(empty($course->Attributes['CourseNumber'])){
                                $courseBuilder['CourseNumber'] = 'Not set';
                            }
                            else {
                                $courseBuilder['CourseNumber'] = $course->Attributes['CourseNumber'];
                            }

                            //instructor info, if applicable
                            $instructorHelp = [];
                            if(empty($course->Attributes['InstructorId'])){
                                //no instructor set
                                $instructorHelp['InstructorName'] = 'No instructor for this course.';
                            }
                            else {
                                $instructFoo = new Instructor($course->Attributes['InstructorId']);
                                $instructorHelp['InstructorId'] = $instructFoo->id;
                                $instructorHelp['InstructorName'] = $instructFoo->Attributes['InstructorFirstName'] . ' ' . $instructFoo->Attributes['InstructorLastName'];
                            }

                            $courseBuilder['instructor'] = $instructorHelp;

                            //add course info to courseHelper
                            $courseHelper[] = $courseBuilder;
                        }

                        //add the course info to the array
                        $helperHelp['courses'] = $courseHelper;

                        $helperHelp['ProgramId'] = $ip->id;
                        $helperHelp['ProgramName'] = $ip->Attributes['ProgramName'];
                        //$helperHelp['ProgramType'] = $ip->Attributes['ProgramType'];
                        $helperHelp['ProgramType'] = $ip->getType();
                        $curTags = $ip->getTagLabels();
                        if($curTags){
                            $helperHelp['ProgramClassification'] = '';
                            foreach($curTags as $c){
                                $helperHelp['ProgramClassification'] .= $c . '</br>';
                            }
                        } else {
                            $helperHelp['ProgramClassification'] = 'None set.';
                        }

                        //commented out below because of the new drop down list
                        //$helperHelp['ProgramDelivery'] = $ip->Attributes['DeliveryMethod'];
                        $helperHelp['ProgramDelivery'] = $ip->getDeliveryMethod();

                        if (empty($ip->Attributes['ProgramAccess'])) {
                            $helperHelp['ProgramAccess'] = 'Access information not set.';
                        } else {
                            $helperHelp['ProgramAccess'] = "<a href='{$ip->Attributes['ProgramAccess']}' target='_blank'>{$ip->Attributes['ProgramAccess']}</a>";
                        }

                        if (empty($ip->Attributes['ProgramObjectives'])) {
                            $helperHelp['ProgramObjectives'] = 'Objectives not set.';
                        } else {
                            $helperHelp['ProgramObjectives'] = $ip->Attributes['ProgramObjectives'];
                        }
                        $helperHelp['ProgramFullTime'] = $ip->getFullTimeDurationLabel();
//                        if (empty($ip->Attributes['FullTimeDuration'])) {
//                            $helperHelp['ProgramFullTime'] = 'Full time duration not set.';
//                        } else {
//                            $helperHelp['ProgramFullTime'] = $ip->Attributes['FullTimeDuration'];
//                        }

                        $helperHelp['ProgramPartTime'] = $ip->getPartTimeDurationLabel();
//                        if (empty($ip->Attributes['PartTimeDuration'])) {
//                            $helperHelp['ProgramPartTime'] = 'Part time duration not set.';
//                        } else {
//                            $helperHelp['ProgramPartTime'] = $ip->Attributes['PartTimeDuration'];
//                        }

                        $waive = $ip->Attributes['Waiver'];
                        if($waive === 1){
                            $waiver = 'Waivers for testing are available';
                        } else {
                            $waiver = 'No waivers have been selected for this program.';
                        }
                        $helperHelp['Waiver'] = $waiver;

                        $curReqs = $ip->getTestingRequirements(TRUE);
                        $reqs = '';
                        foreach($curReqs as $c){
                            $reqs .= $c . '<br/>';
                        }

                        if (empty($reqs)) {
                            $helperHelp['ProgramTestingRequirements'] = 'Testing requirements not set.';
                        }
                        else {
                            $helperHelp['ProgramTestingRequirements'] = $reqs;
                        }

                        if (empty($ip->Attributes['OtherRequirements'])) {
                            $helperHelp['ProgramOtherRequirements'] = 'Other requirements not set.';
                        } else {
                            $helperHelp['ProgramOtherRequirements'] = $ip->Attributes['OtherRequirements'];
                        }

                        if (empty($ip->Attributes['Credits'])) {
                            $helperHelp['ProgramCredits'] = 'Credits not set.';
                        } else {
                            $helperHelp['ProgramCredits'] = $ip->Attributes['Credits'];
                        }

                        if (empty($ip->Attributes['YearEstablished'])) {
                            $helperHelp['ProgramEstablished'] = 'Year established not set.';
                        } else {
                            $helperHelp['ProgramEstablished'] = $ip->Attributes['YearEstablished'];
                        }

                        if (empty($ip->Attributes['Scholarship'])) {
                            $helperHelp['ProgramScholarship'] = 'Scholarship not set.';
                        } else {
                            $helperHelp['ProgramScholarship'] = $ip->Attributes['Scholarship'];
                        }

                        if (empty($ip->Attributes['EstimatedResidentTuition'])) {
                            $helperHelp['ProgramResidentTuition'] = 'Estimated resident tuition not set.';
                        } else {
                            $helperHelp['ProgramResidentTuition'] = $ip->Attributes['EstimatedResidentTuition'];
                        }

                        if (empty($ip->Attributes['EstimatedNonResidentTuition'])) {
                            $helperHelp['ProgramNonResidentTuition'] = 'Estimated non-resident tuition not set.';
                        } else {
                            $helperHelp['ProgramNonResidentTuition'] = $ip->Attributes['EstimatedNonResidentTuition'];
                        }

//                        if (empty($ip->Attributes['CostPerCredit'])) {
//                            $helperHelp['ProgramCostPerCredit'] = 'Cost per credit not set.';
//                        } else {
//                            $helperHelp['ProgramCostPerCredit'] = $ip->Attributes['CostPerCredit'];
//                        }

                        //set up the appropriate message based on flags
//                        $analFlag = $ip->Attributes['AnalyticsFlag'];
//                        $orFlag = $ip->Attributes['ORFlag'];
//                        if ($analFlag == 1 && $orFlag == 1) {
//                            $helperHelp['ProgramAnalyticsOR'] = 'Both Analytics and O.R.';
//                        } else if ($analFlag == 0 && $orFlag == 1) {
//                            $helperHelp['ProgramAnalyticsOR'] = 'O.R.';
//                        } else if ($analFlag == 1 && $orFlag == 0) {
//                            $helperHelp['ProgramAnalyticsOR'] = 'Analytics';
//                        } else {
//                            $helperHelp['ProgramAnalyticsOR'] = 'Neither Analytics nor O.R.';
//                        }

                        $helperHelp['ProgramCreated'] = $ip->Attributes['CreateDate'];

                        if(empty($ip->Attributes['CollegeId'])){
                            $helperHelp['College'] = 'Not assigned.';
                        }
                        else {
                            $coll = new College($ip->Attributes['CollegeId']);
                            $helperHelp['College'] = $coll->Attributes['CollegeName'];
                        }

                        //need to pull more info for the last 2 things
                        //get all assigned contacts
                        $progContacts = $ip->getContacts();
                        if (!$progContacts) {
                            $helperHelp['Contacts'][] = null;
                        } else {
                            foreach($progContacts as $c){
                                if(empty($c->Attributes['ContactTitle'])){
                                    $cTitle = 'Contact title not set.';
                                }
                                else {
                                    $cTitle = $c->Attributes['ContactTitle'];
                                }

                                if(empty($c->Attributes['ContactPhone'])){
                                    $cPhone = 'Contact phone not set.';
                                }
                                else {
                                    //AOREducationObject::formatPhoneNumber($foo['InstitutionPhone']);
                                    $cPhone = AOREducationObject::formatPhoneNumber($c->Attributes['ContactPhone']);
                                }

                                if(empty($c->Attributes['ContactEmail'])){
                                    $cEmail = 'Contact email not set.';
                                }
                                else {
                                    $cEmail = '<a href="mailto:' . $c->Attributes['ContactEmail'] . '">' . $c->Attributes['ContactEmail'] .  '</a>';
                                }

                                $helperHelp['Contacts'][] = array(
                                    'ContactId' => $c->id,
                                    'ContactName' => $c->Attributes['ContactName'],
                                    'ContactTitle' => $cTitle,
                                    'ContactPhone' => $cPhone,
                                    'ContactEmail' => $cEmail
                                );
                            }
//                            $progContact = new Contact($ip->Attributes['ContactId']);
//
//                            $helperHelp['ContactName'] = $progContact->Attributes['ContactName'];
//
//                            if (empty($progContact->Attributes['ContactTitle'])) {
//                                $helperHelp['ContactTitle'] = 'Title not set.';
//                            } else {
//                                $helperHelp['ContactTitle'] = $progContact->Attributes['ContactTitle'];
//                            }
//
//                            if (empty($progContact->Attributes['ContactPhone'])) {
//                                $helperHelp['ContactPhone'] = 'Phone not set.';
//                            } else {
//                                $helperHelp['ContactPhone'] = $progContact->Attributes['ContactPhone'];
//                            }
//
//                            if (empty($progContact->Attributes['ContactEmail'])) {
//                                $helperHelp['ContactEmail'] = 'Email not set.';
//                            } else {
//                                $helperHelp['ContactEmail'] = "<a href='{$progContact->Attributes['ContactEmail']}' target='_blank'>{$progContact->Attributes['ContactEmail']}</a>";
//                            }
                        }
                        //$helperHelp['ApprovalStatusId'] = $ip->Attributes['ApprovalStatusId'];
                        //add record to the array
                        $programHelper[] = $helperHelp;
                    }
                    //add array of program info to what's getting passed back to the ajax script
                    $foo['programs'] = $programHelper;
                }
                else {
                    $foo['programs'] = 'No programs associated with the institution.';
                }

                //get array of colleges
                $instColleges = $instHelp->getColleges(TRUE);
                $collegeHelper = [];
                if($instColleges){
                    foreach($instColleges as $col){
                        $fooHelper = [];
                        $fooHelper['CollegeId'] = $col->id;
                        $fooHelper['CollegeName'] = $col->Attributes['CollegeName'];
                        $typeId = $col->Attributes['TypeId'];
                        if(!is_null($typeId)){
                            $fooHelper['CollegeType'] = Dropdowns::getCollegeTypeName($typeId);
                        } else {
                            $fooHelper['CollegeType'] = 'Type not set.';
                        }
                        if(stripos($fooHelper['CollegeType'], 'Other') !== FALSE){
                            $fooHelper['CollegeType'] .= '&mdash;' . $col->getOtherType();
                        }

                        //$fooHelper['CollegeType'] = $col->Attributes['CollegeType'];
                        $fooHelper['CollegeCreated'] = $col->Attributes['CreateDate'];
                        //add college info to array of colleges
                        $collegeHelper[] = $fooHelper;
                    }
                    //add array of colleges to what's getting passed back to the ajax script
                    $foo['colleges'] = $collegeHelper;
                }
                else {
                    $foo['colleges'] = 'No colleges associated with the institution.';
                }
            }

            //add the info to the response array
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