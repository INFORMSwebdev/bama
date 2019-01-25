<?php
	//define constants to use throughout
	define("SETTINGSDIR", "../../../cgi-bin/settings/");
	define("IMAGESDIR", "images/");
	define("ICONSDIR", "images/statusIcons/");
	//set the global DB object up for functions to use
	try{
		$g_db = new pdo_db( "/common/settings/common.ini", "analytics_education_settings" );
	} catch(\PDOException $e){
		throw new \PDOException($e->getMessage(), (int)$e->getCode());
	}
	
	/**
	* Query the DB to get an array of all programs.
	*
	* @author Dan Herold
	* @param $db the database object to run the query with
	* @return the statement obj that can be fetched on to get the results of the query
	*/
	function getAllPrograms(){
		global $g_db;
		//set the query to run
		$sql = "SELECT a.InstitutionId, b.InstitutionName, a.CollegeName, c.ProgramName, c.ProgramId, c.ReferenceId, c.LastUpdate
			FROM colleges a
			INNER JOIN institutions b 
				ON a.InstitutionId = b.InstitutionId
			INNER JOIN programs c 
				ON a.InstitutionId = c.InstitutionId 
			WHERE c.ReferenceId IS NULL";
		//exec the statement and get the results
		$stmt = $g_db->query($sql);
		return $stmt;
	}
	
	/**
	 * Process all returned programs into an HTML table for display into tbody elements (excluding the tbody tags themselves)
	 *
	 * @author Dan Herold
	 */
	function processAllPrograms(){
		//get all variables defined in the global context for use in this function
		$results = getAllPrograms();
		foreach($results as $row){
			//want to give each record its own row
			echo "<tr>";
			//status column, containing a status icon
			echo "<td><div class='container-fluid'><img class='statusIcon' src='" . ICONSDIR . "baseline-warning-black-18/2x/baseline_warning_black_18dp.png' class='material-icons' /></div></td>";
			//other columns
			echo "<td>" .$row['InstitutionName']. "</td>";
			echo "<td>" .$row['CollegeName']. "</td>";
			echo "<td>" .$row['ProgramName']. "</td>";
			echo "<td>" .$row['LastUpdate']. "</td>";
			//button column
			echo "<td><button class='btn btn-small btn-primary' data-toggle='modal' data-target='#programModal' data-whatever=".$row['ProgramId'].">Edit</button></td>";
			echo "</tr>";
		}
	}
?>