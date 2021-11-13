<?php

// takes data out of CSV and puts it in JSON array

require_once( "/common/classes/common_autoload.php" );

$db = new pdo_db( "/common/settings/common.ini", "analytics_education_settings" );
$sql = "SELECT * FROM un_country_region_data WHERE Region_Code IS NOT NULL ORDER BY Country_or_Area";
$rows = $db->query( $sql );
$data = [];
foreach( $rows as $row ) {
    if (!isset($data[$row['Region_Code']])) $data[$row['Region_Code']] = ['RegionName' => $row['Region_Name'],'Subregions'=>[]];
    if (!isset($data[$row['Region_Code']]['Subregions'][$row['Sub_region_Code']]) && isset($row['Sub_region_Code'])) $data[$row['Region_Code']]['Subregions'][$row['Sub_region_Code']] = ['SubregionName' => $row['Sub_region_Name'],'IntermediateRegions'=>[]];
    if (!isset($data[$row['Region_Code']]['Subregions'][$row['Sub_region_Code']]['IntermediateRegions'][$row['Intermediate_Region_Code']]) && isset($row['Intermediate_Region_Code'])) $data[$row['Region_Code']]['Subregions'][$row['Sub_region_Code']]['IntermediateRegions'][$row['Intermediate_Region_Code']] = ['IntermediateRegionName'=> $row['Intermediate_Region_Name']];
}

echo json_encode( $data );

$fh = fopen("../htdocs/data/country_data.json", "w" );
fwrite(  $fh, json_encode( $data ) );
fclose($fh );