<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 3/21/2019
 * Time: 12:14 PM
 */

require_once ('../../init.php');
//if(!isset($_SESSION['admin']) || !$_SESSION['admin']) die("unauthorized access");

$classes = [
    'Institutions' => 'Institution',
    'Colleges' => 'College',
    'Programs' => 'Program',
    'Contacts' => 'Contact',
    'Courses' =>  'Course',
    'Instructors' => 'Instructor',
    'Textbooks' => 'Textbook',
    'Software' => 'Software',
    'Datasets' => 'Dataset',
    'Case Studies' => 'CaseStudy',
];

$response = [];
$cat = filter_input( INPUT_GET, 'category');

if (!in_array($cat, array_keys($classes)) && $cat != "All") die("invalid value for category");
$search_term = filter_input( INPUT_GET, 'search_term');
$search_term = preg_replace('/[^a-zA-Z0-9 ]/', '', $search_term);
$statuses = filter_input( INPUT_GET, 'status', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
//$search_term = 'mary';
function createSQL( $category, $class, $search_term, $statuses ) {
    $primary_key = $class::$primary_key;
    $table_name = $class::$table;
    $full_text_cols = $class::$full_text_columns;
    $name_sql = $class::$name_sql;
    $match_phrase = "MATCH($full_text_cols) AGAINST ('$search_term' IN NATURAL LANGUAGE MODE)";
    $sql = <<<EOT
SELECT '$category' AS category, $name_sql AS name, $primary_key AS id, $match_phrase AS score 
FROM $table_name
WHERE $match_phrase
EOT;
    if (count($statuses)) $sql .= ' AND ApprovalStatusId IN ('.implode(",", $statuses).")";
    return $sql;
}

if ($cat == 'All') {
    $blocks = [];
    foreach( $classes as $category => $class ) {
        $sql = createSQL( $category, $class, $search_term, $statuses );
        $blocks[] = $sql;
    }
    $sql = implode(" UNION ALL ", $blocks);
}
else {
    $sql = createSQL( $cat, $classes[$cat], $search_term, $statuses );
}
$sql .= " ORDER BY score DESC, name ASC";
$db = new EduDB;
$rows = $db->query( $sql );
$result['data'] = $rows;
$result['msg'] = $sql;
echo json_encode( $result );
/*
 * SELECT 'institutions' tablename, InstitutionId id, MATCH(InstitutionName, InstitutionCity, InstitutionState, InstitutionRegion) AGAINST ('mary' IN NATURAL LANGUAGE MODE) AS score FROM institutions
WHERE MATCH(InstitutionName, InstitutionCity, InstitutionState, InstitutionRegion) AGAINST ('mary' IN NATURAL LANGUAGE MODE)
UNION ALL
SELECT 'courses' tablename, CourseId id, MATCH(CourseTitle, CourseText, AnalyticTag, BusinessTag) AGAINST ('mary' IN NATURAL LANGUAGE MODE) AS score FROM courses
WHERE MATCH(CourseTitle, CourseText, AnalyticTag, BusinessTag) AGAINST ('mary' IN NATURAL LANGUAGE MODE)
ORDER BY score DESC;
 *
 */