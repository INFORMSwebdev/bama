<?php
	//define constants to use throughout
	# can set these up to use absolute paths, but currently they are all relative paths from this document: [/var/www/bama/htdocs]/scripts/conn.php
	# I think we can even build on already defined constants for other constants, but I'm not sure if that's true or not.
	define("ROOTDIR", "/var/www/bama/htdocs/");
	define("SETTINGSDIR", "../../settings/");
	define("IMAGESDIR", "images/");
	define("ICONSDIR", "images/statusIcons/");
	define("USERSDIR", "../users/");
	
	//auto load stuff?
	//require_once( "/common/classes/autoload.php" );
	
	//get the DB connection settings
	require_once(SETTINGSDIR . "settings.php");
	
	//set the global DB object up for functions to use
	try{
		# the only reason I didn't use the usual wrapper class is that it didn't have functions already in it for the prepare() function,
		# I didn't want to mess stuff up AND learn how to do this stuff at the same time.
		# ToDo (optional): update the wrapper class and add in a prepare method so that this system can use the wrapper class Dave made
		//$g_db = new pdo_db( "/common/settings/common.ini", "analytics_education_settings" );
		$g_db = new PDO($dsn, $user, $pass, $options);
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