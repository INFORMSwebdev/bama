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

$id = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );
$is_admin = isset($_SESSION['admin']);

if(!isset($_SESSION['loggedIn'])){
    //user is not logged in
    $response['errors'][] = 'You must log in in order to get a list of editable programs.';
}
else {
    $user = new User($_SESSION['loggedIn']);

    //get info for all institutions this user is an editor of
    if (!$is_admin) {
        $insts = $user->getInstitutions(true, false);
        if(array_search($id, array_column($insts, 'InstitutionId')) === false) {
            $response['errors'][] = "You are not authorized the edit this institution";
        }
    }
    $inst = new Institution( $id );
    $insts = [];
    $insts[] = $inst->Attributes;
    if (!$inst->valid) $response['errors'][] = "Invalid Institution ID indicated.";


    if (!count($response['errors'])) {
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
            }

            if(empty($foo['InstitutionEmail'])){
                $foo['InstitutionEmail'] = 'Email not set.';
            }

            if(empty($foo['InstitutionAccess'])){
                $foo['InstitutionAccess'] = 'Access link not set.';
            }

            if($user->id > 1 || $is_admin){
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
                        $helperHelp['ProgramType'] = $ip->Attributes['ProgramType'];
                        $helperHelp['ProgramDelivery'] = $ip->Attributes['DeliveryMethod'];

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

                        if (empty($ip->Attributes['FullTimeDuration'])) {
                            $helperHelp['ProgramFullTime'] = 'Full time duration not set.';
                        } else {
                            $helperHelp['ProgramFullTime'] = $ip->Attributes['FullTimeDuration'];
                        }

                        if (empty($ip->Attributes['PartTimeDuration'])) {
                            $helperHelp['ProgramPartTime'] = 'Part time duration not set.';
                        } else {
                            $helperHelp['ProgramPartTime'] = $ip->Attributes['PartTimeDuration'];
                        }

                        if (empty($ip->Attributes['TestingRequirements'])) {
                            $helperHelp['ProgramTestingRequirements'] = 'Testing requirements not set.';
                        } else {
                            $helperHelp['ProgramTestingRequirements'] = $ip->Attributes['TestingRequirements'];
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

                        if (empty($ip->Attributes['CostPerCredit'])) {
                            $helperHelp['ProgramCostPerCredit'] = 'Cost per credit not set.';
                        } else {
                            $helperHelp['ProgramCostPerCredit'] = $ip->Attributes['CostPerCredit'];
                        }

                        //set up the appropriate message based on flags
                        $analFlag = $ip->Attributes['AnalyticsFlag'];
                        $orFlag = $ip->Attributes['ORFlag'];
                        if ($analFlag == 1 && $orFlag == 1) {
                            $helperHelp['ProgramAnalyticsOR'] = 'Both Analytics and O.R.';
                        } else if ($analFlag == 0 && $orFlag == 1) {
                            $helperHelp['ProgramAnalyticsOR'] = 'O.R.';
                        } else if ($analFlag == 1 && $orFlag == 0) {
                            $helperHelp['ProgramAnalyticsOR'] = 'Analytics';
                        } else {
                            $helperHelp['ProgramAnalyticsOR'] = 'Neither Analytics nor O.R.';
                        }

                        $helperHelp['ProgramCreated'] = $ip->Attributes['CreateDate'];

                        if(empty($ip->Attributes['CollegeId'])){
                            $helperHelp['College'] = 'Not assigned.';
                        }
                        else {
                            $coll = new College($ip->Attributes['CollegeId']);
                            $helperHelp['College'] = $coll->Attributes['CollegeName'];
                        }

                        //get contacts associated with the program
                        $progContacts = $ip->getContacts();
                        if (!$progContacts) {
                            $helperHelp['Contacts'][] = null;
                        } else {
                            foreach ($progContacts as $c) {
                                if (empty($c->Attributes['ContactTitle'])) {
                                    $cTitle = 'Contact title not set.';
                                } else {
                                    $cTitle = $c->Attributes['ContactTitle'];
                                }

                                if (empty($c->Attributes['ContactPhone'])) {
                                    $cPhone = 'Contact phone not set.';
                                } else {
                                    $cPhone = $c->Attributes['ContactPhone'];
                                }

                                if (empty($c->Attributes['ContactEmail'])) {
                                    $cEmail = 'Contact email not set.';
                                } else {
                                    $cEmail = '<a href="mailto:' . $c->Attributes['ContactEmail'] . '">' . $c->Attributes['ContactEmail'] . '</a>';
                                }

                                $helperHelp['Contacts'][] = array(
                                    'ContactName' => $c->Attributes['ContactName'],
                                    'ContactTitle' => $cTitle,
                                    'ContactPhone' => $cPhone,
                                    'ContactEmail' => $cEmail
                                );
                            }
                        }

                        //add record to the array
                        $programHelper[] = $helperHelp;
                    }
                    //add array of program info to what's getting passed back to the ajax script
                    $foo['programs'] = $programHelper;
                }
                else {
                    $foo['programs'] = [];
                }

                //get array of colleges
                $instColleges = $instHelp->getColleges(TRUE);
                $collegeHelper = [];
                if($instColleges){
                    foreach($instColleges as $col){
                        $fooHelper = [];
                        $fooHelper['CollegeId'] = $col->id;
                        $fooHelper['CollegeName'] = $col->Attributes['CollegeName'];
                        //$fooHelper['CollegeType'] = $col->Attributes['CollegeType'];
                        if($col->Attributes['TypeId'] == 6){
                            $fooHelper['CollegeType'] = 'Other&mdash;' . $col->Attributes['OtherType'];
                        } else {
                            $fooHelper['CollegeType'] = Dropdowns::getCollegeTypeName($col->Attributes['TypeId']);
                        }

                        $fooHelper['otherType'] = $col->Attributes['OtherType'];
                        $fooHelper['CollegeCreated'] = $col->Attributes['CreateDate'];
                        //add college info to array of colleges
                        $collegeHelper[] = $fooHelper;
                    }
                    //add array of colleges to what's getting passed back to the ajax script
                    $foo['colleges'] = $collegeHelper;
                }
                else {
                    $foo['colleges'] = [];
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