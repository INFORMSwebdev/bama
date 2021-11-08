<?php

// takes data out of CSV and puts it in JSON array

require_once( "/common/classes/common_autoload.php" );

$db = new pdo_db( "/common/settings/common.ini", "analytics_education_settings" );
$sql = "SELECT * FROM un_country_region_data WHERE Region_Code IS NOT NULL ORDER BY Country_or_Area";
$rows = $db->query( $sql );
$data = [];
foreach( $rows as $row ) {
    if (!isset($row['Region_Code'])) $data[$row['Region_Code']] = ['Region_Name' => $row['Region_Code'],'Subregions'=>[]];
    if (!isset($row['Region_Code']['Subregions'][$row['Sub_region_Code']])) $data[$row['Region_Code']]['SubRegions'][$row['Sub_region_Code']] = ['Sub_region_Name' => $row['Sub_region_Name'],'Subregions'=>[]];
    if (!isset($row['Region_Code']['Subregions'][$row['Sub_region_Code']]['IntermediateRegions'][$row['Intermediate_Region_Code']])) $data[$row['Region_Code']]['Subregions'][$row['Sub_region_Code']]['IntermediateRegions'][$row['Intermediate_Region_Code']] = ['Intermediate_Region_Name'=> $row['Intermediate_Region_Name'],'Countries'=>[]];
    $data[$row['Region_Code']]['Subregions'][$row['Sub_region_Code']]['IntermediateRegions'][$row['Intermediate_Region_Code']]['Countries'][]=['CountryName'=>$row['Country_or_Area'],'Code'=>$row['ISO_alpha3_Code']];

}

echo json_encode( $data );
/*
$fh = fopen("country_data.json", "w" );
fwrite(  $fh, json_encode( $data ) );
fclose($fh );*/