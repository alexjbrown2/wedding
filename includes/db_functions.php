<?php

	#########################################################
	##         DATABASE CONNECTION AND MANIPULATION        ##
	#########################################################

	// Connect to the database server and open a database

	function db_connect($Server = DB_HOST, $User = DB_USER, $Password = DB_PASS, $Database = DB_BASE){
		if(function_exists('mysqli_connect')){
			$MySQLConnection = @mysqli_connect($Server, $User, $Password);
		}else{
			$MySQLConnection = @mysql_connect($Server, $User, $Password);
		}

		// Verify the connection was established

		if(!$MySQLConnection){
			add_message('error', $GLOBALS['errorMessage']['db_connect_failure']);
			return FALSE;
		}else{

			if(function_exists('mysqli_connect')){
				$SelectDatabase = @mysqli_query($MySQLConnection, "USE $Database");
			}else{
				$SelectDatabase = @mysql_query("USE $Database", $MySQLConnection);
			}

			if(!$SelectDatabase){
				$SQLErrorNum = (function_exists('mysqli_connect')) ? mysqli_errno($MySQLConnection) : mysql_errno($MySQLConnection);
				$SQLError    = (function_exists('mysqli_connect')) ? mysqli_error($MySQLConnection) : mysql_error($MySQLConnection);

				// Display connection error on screen

				if(ERRORS_SQL == 1){
					add_message('error', "Unable to access the requested database! <br /><br />
					<strong>Error Number</strong>: " . $SQLErrorNum . " <br />
					<strong>Error Message</strong>: " . $SQLError . "\n");
				}else{

					// Put the full connection error in the log file

					write_to_log("SQL_CONNECT", trim($SQLErrorNum) . " :: " . trim($SQLError));

					// Create a generic error message to display to the user

					add_message('error', $GLOBALS['errorMessage']['db_select_failure']);
				}

				return FALSE;
			}

			$GLOBALS['DBLink'] = $MySQLConnection;
			return TRUE;
		}
	}

	// Close database connection

	function db_close(){
		if(function_exists('mysqli_connect')){
			@mysqli_close($GLOBALS['DBLink']);
		}else{
			@mysql_close($GLOBALS['DBLink']);
		}
	}

	// Run a database query

	function db_query($Query, $LogQuery = TRUE){

		if($LogQuery){
			if(preg_match("/^(INSERT|DELETE|UPDATE|START|COMMIT|ROLLBACK)/", $Query, $Matches)) {
				//log_query(@$_COOKIE['user_id'], $Query);
				log_query(@$_REQUEST['user_id'], $Query);
			}
		}

		if(function_exists('mysqli_connect')){
			$Result = mysqli_query($GLOBALS['DBLink'], $Query);
		}else{
			$Result = mysql_query($Query, $GLOBALS['DBLink']);
		}

		if(!$Result){

			// Grab SQL error information

			$SQLErrorNum = (function_exists('mysqli_connect')) ? mysqli_errno($GLOBALS['DBLink']) : mysql_errno($GLOBALS['DBLink']);
			$SQLError    = (function_exists('mysqli_connect')) ? mysqli_error($GLOBALS['DBLink']) : mysql_error($GLOBALS['DBLink']);

			// Display syntax error on screen

			if(ERRORS_SQL == 1){
				add_message('error', "Your MySQL Query Failed!<br /><br />
				<strong>Error Number</strong>: " . $SQLErrorNum . "<br />
				<strong>Error Message</strong>: " . $SQLError . "<br />
				<strong>SQL Query</strong>: " . $Query);
			}else{

				// Put the full syntax error in the log file

				write_to_log("SQL_SYNTAX", trim($SQLErrorNum) . " :: " . trim($SQLError) . " :: " . trim($Query));

				// Create a generic error message to display to the user

				add_message('error', $GLOBALS['errorMessage']['db_syntax_error']);
			}

			print_messages();
		} else {
			return $Result;
		}
	}

	// Log all writeable database entries and user logins
        // defautl is set to log the db queries

	function log_query($UserID, $Query, $db_log=true){

		$IPv4   = validate("ip", @$_SERVER['REMOTE_ADDR'], "ipv4");
                if ($db_log) {
		    $Query  = preg_replace("/\\n/", " ", $Query);
		    $Query  = validate("regex", $Query, "query", 1, "alltext");
		    $Query  = preg_replace("/\s+/", " ", $Query);
		    $Script = validate("regex", $_SERVER['SCRIPT_FILENAME'], "script name", 1, "text");

		    $LogQuery = "INSERT INTO " . DB_TABLE_DBLOG . " SET user_id='$UserID', ipv4='$IPv4', date=NOW(), script='$Script', query='$Query'";

                // log the user login
                }else {
		     $Script= validate("url", $Query, "url" );
                     if (preg_match('/shportal.php/', $Script)) {
                        $Script="SH PORTAL";
                     }elseif (preg_match('/pma_portal.php/', $Script)) {
                        $Script="PMA PORTAL";
                     }elseif (preg_match('/home.php/', $Script)) {
                        $Script="CBO HOME";
                     }

                     $LogQuery = "INSERT INTO " . DB_TABLE_LOGIN. " SET user_name='$UserID', src_ip='$IPv4', date=NOW(), script='$Script'";
                }

		if(function_exists('mysqli_connect')){
			$Result = mysqli_query($GLOBALS['DBLink'], $LogQuery);
		}else{
			$Result = mysql_query($LogQuery, $GLOBALS['DBLink']);
		}

		if(!$Result){
			$SQLErrorNum = (function_exists('mysqli_connect')) ?  mysqli_errno($GLOBALS['DBLink']) :  mysql_errno($GLOBALS['DBLink']);
			$SQLError = (function_exists('mysqli_connect')) ? mysqli_error($GLOBALS['DBLink']) : mysql_error($GLOBALS['DBLink']);
			add_message('error', "Your MySQL Query Failed!<br /><br />
			<strong>Error Number</strong>: " . $SQLErrorNum . "<br />
			<strong>Error Message</strong>: " . $SQLError . "<br />
			<strong>SQL Query</strong>: $LogQuery");
		}
	}

	// Begin database transaction

	function begin_db_transaction(){
		// $Query = "BEGIN"; // Supported alias for the following line
		$Query = "START TRANSACTION";
		$Result = db_query($Query);

		return $Result;
	}

	// Commit database transaction

	function commit_db_transaction(){
		$Query = "COMMIT";
		$Result = db_query($Query);

		return $Result;
	}

	// Rollback database transaction

	function rollback_db_transaction(){
		$Query = "ROLLBACK";
		$Result = db_query($Query);

		return $Result;
	}

	// Returns the number of rows

	function row_count($Result){
		if(function_exists('mysqli_connect')){
			$NumberOfRows = mysqli_num_rows($Result);
		}else{
			$NumberOfRows = mysql_num_rows($Result);
		}

		return $NumberOfRows;
	}

	// Returns the current row as a numerical array

	function row_fetch($Result){
		if(function_exists('mysqli_connect')){
			$FetchRow = @mysqli_fetch_row($Result);
		}else{
			$FetchRow = @mysql_fetch_row($Result);
		}

		return $FetchRow;
	}

	// Returns the current row as an associative array

	function row_fetch_assoc($Result){
		if(function_exists('mysqli_connect')){
			$FetchRow = mysqli_fetch_assoc($Result);
		}else{
			$FetchRow = mysql_fetch_assoc($Result);
		}

		return $FetchRow;
	}

	// Returns ALL rows as an associative array or numeric array
	// 1 = Assoc, 2 = Numeric

	function row_fetch_all($Result, $Type = 1){

		if($Type == 1){
			while($Row = row_fetch_assoc($Result)){
				$AllRows[] = $Row;
			}
		}else{
			while($Row = row_fetch($Result)){
				$AllRows[] = $Row;
			}
		}

		return @$AllRows;
	}

	// Returns the number of fields returned from a query

	function count_fields($Result){
		if(function_exists('mysqli_connect')){
			$Count = mysqli_num_fields($Result);
		}else{
			$Count = mysql_num_fields($Result);
		}

		return $Count;
	}

	// Returns the field names from a query

	function fetch_field_names($Result){
		if(function_exists('mysqli_connect')){
			$FieldSet = mysqli_fetch_fields($Result);
			foreach($FieldSet as $Field){
				$Fields[] = $Field->name;
			}
		}else{
			for($i = 0; $i < mysql_num_fields($Result); $i++) {
				$FieldInfo = mysql_fetch_field($Result, $i);
				$Fields[] = $FieldInfo->name;
			}
		}

		return $Fields;
	}

	// Retrieve the ID generated by the last query

	function last_query_id(){
		if(function_exists('mysqli_connect')){
			$ID = mysqli_insert_id($GLOBALS['DBLink']);
		}else{
			$ID = mysql_insert_id();
		}

		return $ID;
	}

	// Free the mysql stored result memory

	function free_result($Result){
		if(function_exists('mysqli_connect')){
			$Freed = mysqli_free_result($Result);
		}else{
			$Freed = mysql_free_result($Result);
		}

		return $Freed;
	}

	// Clean a value before writing it to the database

	function clean_sql_value($Value){

		// Clean the value if it is not null

		if($Value){

			// Remove excess white space

			$Value = trim($Value);

			// If magic quotes is enabled strip any slashes that may have already be added

			if(get_magic_quotes_gpc()){
				$Value = stripslashes($Value);
			}

			// Escape the string for writing to a MySQL database
			// Not using addslashes() due to SQL injection possibility

			if(!is_numeric($Value)){
				if(function_exists('mysqli_real_escape_string')){
					$Value = mysqli_real_escape_string($GLOBALS['DBLink'], $Value);
				}else{
					$Value = mysql_real_escape_string($Value, $GLOBALS['DBLink']);
				}
			}

		}

		return $Value;
	}

	#########################################################
	##                 MYSQL QUERY CREATION                ##
	#########################################################

	// Generates a MySQL INSERT command from an array of fields and data
	// Note: Array values wrapped in @'s will be taken literally

	function create_sql_insert($Info, $Table){
		if(is_array($Info)){

			$SQL = "INSERT INTO " . $Table . " SET ";

			for($i=0; $i < count($Info); $i++){
				$SQL .= key($Info);
				$SQL .= "=";

				if(is_int(current($Info))){
					$SQL .= current($Info);
				}elseif(is_null(current($Info)) || strlen(current($Info)) == 0){
					$SQL .= "NULL";
				}elseif(current($Info) == "NOW()"){
					$SQL .= "NOW()";
				}elseif(current($Info) == "CURDATE()"){
					$SQL .= "CURDATE()";
				}elseif(preg_match('/^@.*@$/', current($Info))){
					$SQL .= preg_replace('/@/', '', current($Info));
				}else{
					$SQL .= "'" . current($Info) . "'";
				}

				if ($i < (count($Info)-1)){ $SQL .= ", "; }

				next($Info);
			}

			return $SQL;

		}else{
			return FALSE;
		}
	}

	// Generates a MySQL UPDATE command from an array of fields and data
	// The condition (WHERE) can be a string to accommodate complex conditions or an array for simple (=/AND) conditions
	// Note: Array values wrapped in @'s will be taken literally

	function create_sql_update($Info, $Where, $Table){

		if(is_array($Info)){
			$SQL = "UPDATE " . $Table . " SET ";

			for($i=0; $i < count($Info); $i++){
				$SQL .= key($Info);
				$SQL .= "=";

				if(is_int(current($Info))){
					$SQL .= current($Info);
				}elseif(is_null(current($Info)) || strlen(current($Info)) == 0){
					$SQL .= "NULL";
				}elseif(current($Info) == "NOW()"){
					$SQL .= "NOW()";
				}elseif(current($Info) == "CURDATE()"){
					$SQL .= "CURDATE()";
				}elseif(preg_match('/^@.*@$/', current($Info))){
					$SQL .= preg_replace('/@/', '', current($Info));
				}else{
					$SQL .= "'" . current($Info) . "'";
				}

				if ($i < (count($Info)-1)){ $SQL .= ", "; }

				next($Info);
			}

			if(is_array($Where)){
				$SQL .= " WHERE ";

				for($i=0; $i < count($Where); $i++){
					$SQL .= key($Where);
					$SQL .= "=";

					if(is_int(current($Where))){
						$SQL .= current($Where);
					}elseif(is_null(current($Where))){
						$SQL .= "NULL";
					}elseif(current($Where) == "NOW()"){
						$SQL .= "NOW()";
					}elseif(current($Where) == "CURDATE()"){
						$SQL .= "CURDATE()";
					}elseif(current($Where) == "TRUE"){
						$SQL .= "TRUE";
					}elseif(current($Where) == "FALSE"){
						$SQL .= "FALSE";
					}elseif(preg_match('/^@.*@$/', current($Where))){
						$SQL .= preg_replace('/@/', '', current($Where));
					}else{
						$SQL .= "'" . current($Where) . "'";
					}

					if ($i < (count($Where)-1)){ $SQL .= " AND "; }

					next($Where);
				}
			}else{
				$SQL .= " WHERE $Where";
			}

			return $SQL;

		}else{
			return FALSE;
		}
	}

	// Generates a MySQL DELETE command
	// The condition (WHERE) can be a string to accommodate complex conditions or an array for simple (=/AND) conditions
	// Note: Array values wrapped in @'s will be taken literally

	function create_sql_delete($Where, $Table){
		$SQL = "DELETE FROM " . $Table;

		if(is_array($Where)){
			$SQL .= " WHERE ";

			for($i=0; $i < count($Where); $i++){
				$SQL .= key($Where);
				$SQL .= "=";

				if(is_int(current($Where))){
					$SQL .= current($Where);
				}elseif(is_null(current($Where))){
					$SQL .= "NULL";
				}elseif(current($Where) == "NOW()"){
					$SQL .= "NOW()";
				}elseif(current($Where) == "CURDATE()"){
					$SQL .= "CURDATE()";
				}elseif(current($Where) == "TRUE"){
					$SQL .= "TRUE";
				}elseif(current($Where) == "FALSE"){
					$SQL .= "FALSE";
				}elseif(preg_match('/^@.*@$/', current($Where))){
					$SQL .= preg_replace('/@/', '', current($Where));
				}else{
					$SQL .= "'" . current($Where) . "'";
				}

				if ($i < (count($Where)-1)){ $SQL .= " AND "; }

				next($Where);
			}
		}else{
			$SQL .= " WHERE $Where";
		}

		return $SQL;
	}

	// Create an SQL IN caluse from an array of values

	function create_sql_in_clause($ArrayOfValues, $ColumnName){
		if(is_array(@$ArrayOfValues)){

			// Wrap strings in single quotes

			foreach($ArrayOfValues as $Key => $Value){
				if(!is_numeric($Value)){
					$ArrayOfValues[$Key] = "'" . clean_sql_value($Value) . "'";
				}
			}

			if(!in_array("all", $ArrayOfValues)){
				return "$ColumnName IN (" . implode(",", $ArrayOfValues) . ")";
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	####################
	## MISC FUNCTIONS ##
	####################

	// Generate an array of values from a database table

	function generate_db_array($KeyColumn, $ValueColumn, $Table, $Where = NULL, $OrderByColumn = NULL){
		$SQL = "SELECT $KeyColumn, $ValueColumn FROM $Table ";

		if(is_array($Where)){
			$SQL .= " WHERE ";

			for($i=0; $i < count($Where); $i++){
				$SQL .= key($Where);
				$SQL .= "=";

				if(is_int(current($Where))){
					$SQL .= current($Where);
				}elseif(is_null(current($Where))){
					$SQL .= "NULL";
				}elseif(current($Where) == "NOW()"){
					$SQL .= "NOW()";
				}elseif(current($Where) == "TRUE"){
					$SQL .= "TRUE";
				}elseif(current($Where) == "FALSE"){
					$SQL .= "FALSE";
				}elseif(preg_match('/^@.*@$/', current($Where))){
					$SQL .= preg_replace('/@/', '', current($Where));
				}else{
					$SQL .= "'" . current($Where) . "'";
				}

				if ($i < (count($Where)-1)){ $SQL .= " AND "; }

				next($Where);
			}

		}elseif(strlen($Where) > 0){
			$SQL .= " WHERE " . $Where;
		}

		$SQL .= " ORDER BY " . ($OrderByColumn ? $OrderByColumn : $ValueColumn);
		$Result = db_query($SQL);
		$Count = row_count($Result);

		if($Count > 0){
			while($Row = row_fetch($Result)){
				$Array[$Row[0]] = $Row[1];
			}
			return $Array;
		}else{
			return FALSE;
		}
	}

	// Generate an array of values from a database table

	function lookup_db_value($IDColumn, $ValueColumn, $Value, $Table){
		$SQL = "SELECT $ValueColumn FROM $Table WHERE $IDColumn='$Value'";
		$Result = db_query($SQL);
		$Count = row_count($Result);

		if($Count > 0){
			$Row = row_fetch($Result);
			return $Row[0];
		}else{
			return FALSE;
		}
	}

	####################
	## USER FUNCTIONS ##
	####################

	// Lookup a user's username based off the sharehodler_ID

	function username_lookup($Shareholder_ID){
                $Query = "SELECT username FROM " . DB_TABLE_SHAREHOLDERS . " WHERE shareholder_id='$Shareholder_ID'";
                $Result = db_query($Query);
                $Shareholder_ID = row_fetch($Result);

                return @$Shareholder_ID[0];
        }

	// Lookup a user's user_id based off of the username	

	function user_id_lookup($Username){
		$Query = "SELECT user_id FROM " . DB_TABLE_USERS . " WHERE username='$Username'";
		$Result = db_query($Query);
		$UserID = row_fetch($Result);

		return @$UserID[0];
	}

	// Check to see if a user's account exists

	function account_check($Username){
		$Query  = "SELECT user_id FROM " . DB_TABLE_USERS . " WHERE username='$Username'";
		$Result = db_query($Query);
		$Exists = row_count($Result);

		return $Exists;
	}

	// Check to see if an account is disbaled by username

	function is_account_disabled($Username){
		$Query  = "SELECT user_id FROM " . DB_TABLE_USERS . " WHERE username='$Username' AND disabled=0";
		$Result = db_query($Query);
		$NumberOfRows = row_count($Result);

		return $NumberOfRows;
	}

	##############################
	## GENERATE ARRAYS OF ITEMS ##
	##############################

	function list_access_pool_types(){
		return  generate_db_array("access_pool_type_id", "access_pool_type_name", DB_TABLE_ACCESS_POOL_TYPE);
	}

	function list_boxes(){
		return generate_db_array("box_id", "box_number", DB_TABLE_BOXES, array('deleted' => 0));
	}

	function list_cabinets(){
		return generate_db_array("cabinet_id", "cabinet_name", DB_TABLE_CABINETS, array('deleted' => 0));
	}

	function list_destruction_methods(){
		return generate_db_array("destruction_method_id", "destruction_method_name", DB_TABLE_DESTRUCTION_METHODS, array('deleted' => 0));
	}

	function list_email_templates(){
		return generate_db_array("email_template_id", "email_template_name", DB_TABLE_EMAIL_TEMPLATES, array('deleted' => 0));
	}

	function list_environments(){
		return generate_db_array("environment_id", "environment_name", DB_TABLE_ENVIRONMENTS, array('deleted' => 0));
	}

	function list_escrow_locations(){
		return generate_db_array("escrow_location_id", "escrow_location_name", DB_TABLE_ESCROW_LOCATIONS, array('deleted' => 0));
	}

	function list_escrow_locations_traka(){
		return generate_db_array("escrow_location_id", "escrow_location_name", DB_TABLE_ESCROW_LOCATIONS, array('deleted' => 0, 'escrow_location_type_id' => 3));
	}

	function list_escrow_locations_not_traka(){
		return generate_db_array("escrow_location_id", "escrow_location_name", DB_TABLE_ESCROW_LOCATIONS, "deleted=0 AND escrow_location_type_id!=3");
	}

	function list_escrow_location_types(){
		return generate_db_array("escrow_location_type_id", "escrow_location_type_name", DB_TABLE_ESCROW_LOCATION_TYPES, array('deleted' => 0));
	}

	function list_equipment_versions(){
		return generate_db_array("equipment_version_id", "equipment_version_name", DB_TABLE_EQUIP_VERSIONS, array('deleted' => 0));
	}

	function list_share_types(){
		return generate_db_array("share_type_id", "share_type_name", DB_TABLE_SHARE_TYPES, array('deleted' => 0));
	}

	function list_shares(){
		$Query = "SELECT share_id, share_label FROM " . DB_TABLE_SHARES . " WHERE deleted=0 AND date_destroyed IS NULL ORDER BY share_label";
		$Result = db_query($Query);
		$Count = row_count($Result);

		if($Count > 0){
			while($Row = row_fetch($Result)){
				$Array[$Row[0]] = $Row[1];
			}
			return $Array;
		}else{
			return FALSE;
		}
	}

        function list_shares_audit () {
               //include the destroyed shares to show the audit trail
               $Query = "SELECT share_id, share_label FROM " . DB_TABLE_SHARES . " WHERE deleted=0 ORDER BY share_label";
               $Result = db_query($Query);
               $Count = row_count($Result);
               if($Count > 0){
                        while($Row = row_fetch($Result)){
                                $Array[$Row[0]] = $Row[1];
                        }
                        return $Array;
                }else{
                        return FALSE;
                }
        }

	function list_passwords(){
		return generate_db_array("share_id", "share_label", DB_TABLE_SHARES, "deleted=0 AND share_type_id IN (7,8) AND date_destroyed IS NULL");
	}

	function list_shareholders(){
		//return generate_db_array("shareholder_id", "shareholder_name", DB_TABLE_SHAREHOLDERS, array('deleted' => 0, 'approved' => 1));
                return generate_db_array("shareholder_id", "shareholder_name", DB_TABLE_SHAREHOLDERS, "deleted=0 AND approved=1 AND (function_type_id=1 OR function_type_id=2 OR function_type_id=3)");
	}

	function list_non_sysadmin_shareholders(){
		return generate_db_array("shareholder_id", "shareholder_name", DB_TABLE_SHAREHOLDERS, "deleted=0 AND approved=1 AND function_type_id!=4");
	}

	function list_pma_voting_members(){
		return generate_db_array("shareholder_id ", "shareholder_name", DB_TABLE_SHAREHOLDERS, array('function_id' => 31, 'deleted' => 0));
	}

	function list_shareholders_with_function(){
		return generate_db_array("shareholder_id", "shareholder_name", DB_TABLE_SHAREHOLDERS, "deleted=0 AND approved=1 AND ((function_id_secondary!=4 OR (function_id_secondary IS NULL AND function_id IS NOT NULL)) AND (function_id!=4 OR (function_id IS NULL AND function_id_secondary IS NOT NULL)))");	// Shareholders who are assigned to a function
	}

	function list_physical_key_shareholders(){
		return  generate_db_array("shareholder_id", "shareholder_name", DB_TABLE_SHAREHOLDERS, array( "function_type_id" => 1, "deleted" => 0, 'approved' => 1));
	}

	function list_access_shareholders(){
		return  generate_db_array("shareholder_id", "shareholder_name", DB_TABLE_SHAREHOLDERS, array( "function_type_id" => 2, "deleted" => 0, 'approved' => 1));
	}

	function list_sysadmin_shareholders(){
		return  generate_db_array("shareholder_id", "shareholder_name", DB_TABLE_SHAREHOLDERS, array( "function_type_id" => 4, "deleted" => 0, 'approved' => 1));
	}

	function list_cbo_shareholders(){
		return  generate_db_array("shareholder_id", "shareholder_name", DB_TABLE_SHAREHOLDERS, array( "cbo_user" => 1, "deleted" => 0, 'approved' => 1));
	}

	function list_shareholder_managers (){
		return  generate_db_array("shareholder_id", "shareholder_name", DB_TABLE_SHAREHOLDERS, array( "function_type_id" => 6, "deleted" => 0, 'approved' => 1));
	}
	function list_shareholder_pending(){
		//return  generate_db_array("shareholder_id", "shareholder_name", DB_TABLE_SHAREHOLDERS, array( "deleted" => 0, 'approved' => 0));
                return generate_db_array("shareholder_id", "shareholder_name", DB_TABLE_SHAREHOLDERS, "deleted=0 AND (function_type_id=1 OR function_type_id=2 OR function_type_id=3)");
	}

	function list_distributed_shares(){
		return generate_db_array("share_id", "share_label", DB_TABLE_SHARES, "deleted=0 AND escrow_location_id IS NULL AND date_destroyed IS NULL");
	}
        
	function list_zeroizedbag_list(){
               $QRY="select DISTINCT(s.bag_id), b.bag_serial from share s LEFT JOIN tamper_evident_bag b ON s.bag_id=b.bag_id where s.zeroized=1 and s.deleted=0 ORDER BY s.bag_id";
               $Result = db_query($QRY);
               $Count = row_count($Result);

                if($Count > 0){
                        while($Row = row_fetch($Result)){
                                $Array[$Row[0]] = $Row[1];
                        }
                        return $Array;
                }else{
                        return FALSE;
                }
	}

	function list_shares_in_escrow(){
		return generate_db_array("share_id", "share_label", DB_TABLE_SHARES, "deleted=0 AND zeroized=0 AND (escrow_location_id IS NOT NULL OR always_bagged=1) AND date_destroyed IS NULL");
	}

	function list_sites(){
		return generate_db_array("site_id", "site_name", DB_TABLE_SITES, array('deleted' => 0));
	}

	function list_vendors(){
		return generate_db_array("vendor_id", "vendor_name", DB_TABLE_VENDORS, array('deleted' => 0));
	}

	function list_users(){
		return generate_db_array("user_id", "name", DB_TABLE_USERS);
	}

	function list_equipment_types(){
		return generate_db_array("equipment_type_id", "equipment_type_name", DB_TABLE_EQUIP_TYPES, array('deleted' => 0));
	}

	function list_functions(){
		return generate_db_array("function_id", "function_name", DB_TABLE_FUNCTIONS, array('deleted' => 0));
	}

	function list_physical_key_functions(){
		return generate_db_array("function_id", "function_name", DB_TABLE_FUNCTIONS, "deleted=0 AND function_type_id IN (1,3)");
	}

	function list_access_functions(){
		return generate_db_array("function_id", "function_name", DB_TABLE_FUNCTIONS, "deleted=0 AND function_type_id IN (2,3)");
	}

	function list_sysadmin_functions(){
		return generate_db_array("function_id", "function_name", DB_TABLE_FUNCTIONS, "deleted=0 AND function_type_id IN (4)");
	}

	function list_function_types(){
		return generate_db_array("function_type_id", "function_type_name", DB_TABLE_FUNCTION_TYPES, array('deleted' => 0));
	}

	function list_key_types(){
		return generate_db_array("key_type_id", "key_type_name", DB_TABLE_KEY_TYPES, array('deleted' => 0));
	}

	function list_media_types(){
		return generate_db_array("media_type_id", "media_type_name", DB_TABLE_MEDIA_TYPES, array('deleted' => 0));
	}

	function list_physical_keys(){
		return generate_db_array("physical_key_id", "label", DB_TABLE_KEYS, array('deleted' => 0));
	}

	function list_distributed_physical_keys(){
		return generate_db_array("physical_key_id", "label", DB_TABLE_KEYS, "deleted=0 AND shareholder_id IS NOT NULL");
	}

	function list_escrowed_physical_keys(){
		return generate_db_array("physical_key_id", "label", DB_TABLE_KEYS, "deleted=0 AND escrow_location_id IS NOT NULL");
	}

	function list_physical_keys_traka(){
		return generate_db_array("physical_key_id", "label", DB_TABLE_KEYS, "deleted=0 AND (label REGEXP '[0-9]+b$' OR label REGEXP 'CMb')");
	}

	function list_distributed_physical_keys_traka(){
		return generate_db_array("physical_key_id", "label", DB_TABLE_KEYS, "deleted=0 AND shareholder_id IS NOT NULL AND (label REGEXP '[0-9]+b$' OR label REGEXP 'CMb')");
	}

	function list_escrowed_physical_keys_traka(){
		return generate_db_array("physical_key_id", "label", DB_TABLE_KEYS, "deleted=0 AND escrow_location_id IS NOT NULL AND (label REGEXP '[0-9]+b$' OR label REGEXP 'CMb')");
	}

	function list_physical_keys_not_traka(){
		return generate_db_array("physical_key_id", "label", DB_TABLE_KEYS, "deleted=0 AND (label REGEXP '[0-9]+a$' OR label REGEXP 'CMa')");
	}

	function list_distributed_physical_keys_not_traka(){
		return generate_db_array("physical_key_id", "label", DB_TABLE_KEYS, "deleted=0 AND shareholder_id IS NOT NULL AND (label REGEXP '[0-9]+a$' OR label REGEXP 'CMa')");
	}

	function list_escrowed_physical_keys_not_traka(){
		return generate_db_array("physical_key_id", "label", DB_TABLE_KEYS, "deleted=0 AND escrow_location_id IS NOT NULL AND (label REGEXP '[0-9]+a$' OR label REGEXP 'CMa')");
	}

	function list_physical_key_types(){
		return generate_db_array("physical_key_type_id", "physical_key_type_name", DB_TABLE_PHYSICAL_KEY_TYPES, array('deleted' => 0));
	}

	function list_products(){
		return generate_db_array("product_id", "product_name", DB_TABLE_PRODUCTS, array('deleted' => 0));
	}

	function list_dps_products(){
		return generate_db_array("dps_product_id", "dps_product_name", DB_TABLE_DPS_PRODUCTS, array('deleted' => 0));
	}

	function list_roles(){
		return generate_db_array("role_id", "role_name", DB_TABLE_ROLES, array('deleted' => 0));
	}

	function list_forms(){
		return generate_db_array("form_id", "form_name", DB_TABLE_FORMS, array('deleted' => 0));
	}

	function list_support_contracts(){
		return generate_db_array("support_contract_id", "contract_number", DB_TABLE_SUPPORT_CONTRACTS, array('deleted' => 0));
	}

	function list_model_types(){
		return generate_db_array("model_type_id", "model_type_name", DB_TABLE_MODEL_TYPES, array('deleted' => 0));
	}

	function list_quarters(){
		return generate_db_array("quarter_id", "quarter_name", DB_TABLE_QUARTERS, array('deleted' => 0));
	}

	function list_work_locations(){
		return generate_db_array("work_location_id", "location", DB_TABLE_WORK_LOCATIONS, array('deleted' => 0));
	}

	function list_access_security_notifications($Type = NULL){	// NULL = all, 1 = start only, 2 = completed (start and end)
		if($Type === NULL){
			$Query = "SELECT security_notification_id, CONCAT(DATE(n.date_created), ': ', location, ' work at ', site_name) AS description FROM " . DB_TABLE_SECURITY_NOTIFICATIONS . " n LEFT JOIN " . DB_TABLE_SITES . " s ON s.site_id=n.site_id LEFT JOIN " . DB_TABLE_WORK_LOCATIONS . " w ON w.work_location_id=n.work_location_id WHERE n.security_notification_type_id=1 AND n.deleted=0";
		}elseif($Type == 1){
			$Query = "SELECT security_notification_id, CONCAT(DATE(n.date_created), ': ', location, ' work at ', site_name) AS description FROM " . DB_TABLE_SECURITY_NOTIFICATIONS . " n LEFT JOIN " . DB_TABLE_SITES . " s ON s.site_id=n.site_id LEFT JOIN " . DB_TABLE_WORK_LOCATIONS . " w ON w.work_location_id=n.work_location_id WHERE n.security_notification_type_id=1 AND n.end_send_time IS NULL AND n.deleted=0";
		}elseif($Type == 2){
			$Query = "SELECT security_notification_id, CONCAT(DATE(n.date_created), ': ', location, ' work at ', site_name) AS description FROM " . DB_TABLE_SECURITY_NOTIFICATIONS . " n LEFT JOIN " . DB_TABLE_SITES . " s ON s.site_id=n.site_id LEFT JOIN " . DB_TABLE_WORK_LOCATIONS . " w ON w.work_location_id=n.work_location_id WHERE n.security_notification_type_id=1 AND n.end_send_time IS NOT NULL AND n.deleted=0";
		}
		$Result = db_query($Query);
		$Count = row_count($Result);

		if($Count > 0){
			while($Row = row_fetch($Result)){
				$Array[$Row[0]] = $Row[1];
			}
			return $Array;
		}
		return array();
	}

	function list_security_notification_types(){
		$Query = "SELECT type_id, type_name FROM " . DB_TABLE_SECURITY_NOTIFICATION_TYPES . " WHERE deleted=0";
		$Result = db_query($Query);
		$Count = row_count($Result);

		if($Count > 0){
			while($Row = row_fetch($Result)){
				$Array[$Row[0]] = $Row[1];
			}
			return $Array;
		}
		return array();
	}

	function list_tamper_evident_bags(){
		$AllBags =  generate_db_array("bag_id", "bag_serial", DB_TABLE_TAMPER_EVIDENT_BAGS, array('deleted' => 0, 'used' => 0));
		$RemoveBags = list_pending_tamper_evident_bags();

		if(count($RemoveBags) > 0){

			// Remove pending bags from the "master" list of un-used bags

			return array_diff($AllBags, $RemoveBags);

		}else{
			return $AllBags;
		}
	}

	function list_used_tamper_evident_bags(){
		return  generate_db_array("bag_id", "bag_serial", DB_TABLE_TAMPER_EVIDENT_BAGS, array('deleted' => 0, 'used' => 1));
	}

	function list_all_tamper_evident_bags(){
		$Query = "SELECT bag_id, CONCAT(bag_serial, ' (', IF(used=0, 'New', 'Used') ,')') FROM " . DB_TABLE_TAMPER_EVIDENT_BAGS . " WHERE deleted=0 AND used IN (0, 1) ORDER BY bag_serial";
		$Result = db_query($Query);
		$Count = row_count($Result);

		if($Count > 0){
			while($Row = row_fetch($Result)){
				$Array[$Row[0]] = $Row[1];
			}
			return $Array;
		}else{
			return FALSE;
		}
	}

	// Generate a list of tamper evident bags that are in pending status

	function list_pending_tamper_evident_bags(){

		// Generate an array of bags that are currently pending approval

		$Query = "SELECT f.form_table, l.table_column_name FROM " . DB_TABLE_FORM_LAYOUT . " l LEFT JOIN " . DB_TABLE_FORMS . " f ON f.form_id=l.form_id WHERE (dropdown_option='tamper_evident_bag' OR dropdown_option='tamper_evident_bag_all') AND l.deleted=0 AND f.deleted=0";
		$Result = db_query($Query);
		$Count = row_count($Result);

		if($Count > 0){

			while($Current = row_fetch_assoc($Result)){
				$TableInfo[] = array("form_table" => $Current['form_table'], "table_column_name" => $Current['table_column_name']);
			}

			// Grab the serial numbers of all bags from all tables that have a form in "pending" status

			if(@$TableInfo){
				foreach($TableInfo as $Info){
					$Query = "SELECT " . $Info['table_column_name'] . ", bag_serial FROM " . $Info['form_table'] . " f LEFT JOIN " . DB_TABLE_TAMPER_EVIDENT_BAGS . " b ON b.bag_id=f." . $Info['table_column_name'] . " WHERE f.deleted=0 AND status_id=1";

					$Result = db_query($Query);
					$Count = row_count($Result);

					if($Count > 0){
						while($Current = row_fetch($Result)){
							$PendingBags[$Current[0]] = $Current[1];
						}
					}
				}

				return (@$PendingBags ? $PendingBags : NULL);

			}else{
				return NULL;
			}

		}else{
			return NULL;
		}

	}

	function list_equipment(){
		return generate_db_array("equipment_id", "CONCAT(label_name, ' (', serial_number, ')') AS serial_number", DB_TABLE_EQUIP, "deleted=0 AND status!='Destroyed' AND equipment_type_id!=3", "label_name, serial_number");
	}

	function list_non_escrowed_equipment(){
		return generate_db_array("equipment_id", "CONCAT(label_name, ' (', serial_number, ')') AS serial_number", DB_TABLE_EQUIP, "deleted=0 AND status!='Destroyed' AND escrow_location_id IS NULL AND equipment_type_id!=3", "label_name, serial_number");
	}

	function list_escrowed_equipment(){
		return generate_db_array("equipment_id", "CONCAT(label_name, ' (', serial_number, ')') AS serial_number", DB_TABLE_EQUIP, "deleted=0 AND status!='Destroyed' AND escrow_location_id IS NOT NULL AND equipment_type_id!=3", "label_name, serial_number");
	}

	function list_hsms(){
		return generate_db_array("equipment_id", "CONCAT(label_name, ' (', serial_number, ')') AS serial_number", DB_TABLE_EQUIP, "deleted=0 AND status!='Destroyed' AND equipment_type_id=3", "label_name, serial_number");
	}
	function list_hsms_zeroized(){
		return generate_db_array("equipment_id", "CONCAT(label_name, ' (', serial_number, ')') AS serial_number", DB_TABLE_EQUIP, "deleted=0 AND zeroized=1 AND status!='Destroyed' AND equipment_type_id=3", "label_name, serial_number");
	}

	function list_escrowed_hsms(){
		return generate_db_array("equipment_id", "CONCAT(label_name, ' (', serial_number, ')') AS serial_number", DB_TABLE_EQUIP, "deleted=0 AND status!='Destroyed' AND escrow_location_id IS NOT NULL AND equipment_type_id=3", "label_name, serial_number");
	}

	function list_non_escrowed_hsms(){
		return generate_db_array("equipment_id", "CONCAT(label_name, ' (', serial_number, ')') AS serial_number", DB_TABLE_EQUIP, "deleted=0 AND status!='Destroyed' AND escrow_location_id IS NULL AND equipment_type_id=3", "label_name, serial_number");
	}

	function list_unmapped_forms(){

		// Find all filenames that are already mapped

		$MappedForms = generate_db_array("form_id", "filename", DB_TABLE_FORMS, array('deleted' => 0));

		// Find all filenames on the server

		$Glob = glob(PATH_FORMS . "/*" . FROMS_FILE_EXTENSION);
		foreach($Glob as $File){
			$AllFiles[basename($File)] = basename($File);
		}

		// Remove the mapped filenames from the list of all files

		if($MappedForms && $AllFiles){
			return array_diff(@$AllFiles, @$MappedForms);
		}else{
			return @$AllFiles;
		}
	}

	function list_form_tables(){

		// Find all form_* tables that are in use

		$UsedTables = generate_db_array("form_id", "form_table", DB_TABLE_FORMS, array('deleted' => 0));

		// Find all form_* tables

		$Query  = "SHOW TABLES LIKE 'form_%'";
		$Result = db_query($Query);

		while($Row = row_fetch($Result)){
			$AvaliableTables[$Row[0]] = $Row[0];
		}

		// Remove form_* tables in use and misc tables that begin with form_*

		return array_diff(@$AvaliableTables, $UsedTables, array("form_exec", "form_layout", "form_roles", "form_statuses", "form_safe_deposit_box_audit_contents", "form_equip_courier_attestation_items", "form_hsm_courier_attestation_items", "form_physical_key_courier_attestation_items", "form_share_courier_attestation_items", "form_hsm_out_of_band_verification_attestation_items"));
	}

	// Get a list of recipients for a given event

	function list_event_recipients($EventID){
		$Query = "SELECT cc_recipients, default_recipients, GROUP_CONCAT(username, '@verisign.com') AS recipients FROM " . DB_TABLE_EVENTS . " e LEFT JOIN " . DB_TABLE_EVENT_SHAREHOLDERS . " es ON e.event_id=es.event_id LEFT JOIN " . DB_TABLE_SHAREHOLDERS . " s ON s.shareholder_id=es.shareholder_id LEFT JOIN " . DB_TABLE_EMAIL_TEMPLATES . " t ON t.email_template_id=e.email_template_id WHERE e.event_id='" . $EventID . "' AND es.deleted=0";
		$Result = db_query($Query);
		$Count = row_count($Result);

		$AllRecipients = array();

		if($Count > 0){
			$Row = row_fetch($Result);
			foreach($Row as $Key => $Value){
				$Recipients = explode(",", $Value);
				$AllRecipients = array_merge($AllRecipients, $Recipients);
			}
		}

		return array_unique(array_map("trim", $AllRecipients));
	}

	##########################
	## LOOKUP VALUES OF IDS ##
	##########################

	function lookup_access_pool_type($ID){
		return lookup_db_value("access_pool_type_id", "access_pool_type_name", $ID, DB_TABLE_ACCESS_POOL_TYPE);
	}

	function lookup_box($ID){
		return lookup_db_value("box_id", "box_number", $ID, DB_TABLE_BOXES);
	}

	function lookup_cabinet($ID){
		return lookup_db_value("cabinet_id", "cabinet_name", $ID, DB_TABLE_CABINETS);
	}

	function lookup_destruction_method($ID){
		return lookup_db_value("destruction_method_id", "destruction_method_name", $ID, DB_TABLE_DESTRUCTION_METHODS);
	}

	function lookup_email_template($ID){
		return lookup_db_value("email_template_id", "email_template_name", $ID, DB_TABLE_EMAIL_TEMPLATES);
	}

	function lookup_environment($ID){
		return lookup_db_value("environment_id", "environment_name", $ID, DB_TABLE_ENVIRONMENTS);
	}

	function lookup_escrow_location($ID){
		return lookup_db_value("escrow_location_id", "escrow_location_name", $ID, DB_TABLE_ESCROW_LOCATIONS);
	}

	function lookup_escrow_location_type($ID){
		return lookup_db_value("escrow_location_type_id", "escrow_location_type_name", $ID, DB_TABLE_ESCROW_LOCATION_TYPES);
	}

	function lookup_equipment_version($ID){
		return lookup_db_value("equipment_version_id", "equipment_version_name", $ID, DB_TABLE_EQUIP_VERSIONS);
	}

	function lookup_share($ID){
		return lookup_db_value("share_id", "share_label", $ID, DB_TABLE_SHARES);
	}

	function lookup_shareholder($ID){
		return lookup_db_value("shareholder_id", "shareholder_name", $ID, DB_TABLE_SHAREHOLDERS);
	}

	function lookup_share_type($ID){
		return lookup_db_value("share_type_id", "share_type_name", $ID, DB_TABLE_SHARE_TYPES);
	}

	function lookup_site($ID){
		return lookup_db_value("site_id", "site_name", $ID, DB_TABLE_SITES);
	}

	function lookup_vendor($ID){
		return lookup_db_value("vendor_id", "vendor_name", $ID, DB_TABLE_VENDORS);
	}

	function lookup_user($ID){
		return lookup_db_value("user_id", "name", $ID, DB_TABLE_USERS);
	}

	function lookup_equipment_type($ID){
		return lookup_db_value("equipment_type_id", "equipment_type_name", $ID, DB_TABLE_EQUIP_TYPES);
	}

	function lookup_function($ID){
		return lookup_db_value("function_id", "function_name", $ID, DB_TABLE_FUNCTIONS);
	}

	function lookup_function_type($ID){
		return lookup_db_value("function_type_id", "function_type_name", $ID, DB_TABLE_FUNCTION_TYPES);
	}

	function lookup_key_type($ID){
		return lookup_db_value("key_type_id", "key_type_name", $ID, DB_TABLE_KEY_TYPES);
	}

	function lookup_media_type($ID){
		return lookup_db_value("media_type_id", "media_type_name", $ID, DB_TABLE_MEDIA_TYPES);
	}

	function lookup_model_type($ID){
		return lookup_db_value("model_type_id", "model_type_name", $ID, DB_TABLE_MODEL_TYPES);
	}

	function lookup_physical_key($ID){
		return lookup_db_value("physical_key_id", "label", $ID, DB_TABLE_KEYS);
	}

	function lookup_physical_key_type($ID){
		return lookup_db_value("physical_key_type_id", "physical_key_type_name", $ID, DB_TABLE_PHYSICAL_KEY_TYPES);
	}

	function lookup_product($ID){
		return lookup_db_value("product_id", "product_name", $ID, DB_TABLE_PRODUCTS);
	}
	function lookup_dps_product($ID){
		return lookup_db_value("dps_product_id", "dps_product_name", $ID, DB_TABLE_DPS_PRODUCTS);
	}

	function lookup_role($ID){
		return lookup_db_value("role_id", "role_name", $ID, DB_TABLE_ROLES);
	}

	function lookup_tamper_evident_bag($ID){
		return lookup_db_value("bag_id", "bag_serial", $ID, DB_TABLE_TAMPER_EVIDENT_BAGS);
	}

	function lookup_equipment($ID){
		return lookup_db_value("equipment_id", "serial_number", $ID, DB_TABLE_EQUIP);
	}

	function lookup_support_contract($ID){
		return lookup_db_value("support_contract_id", "contract_number", $ID, DB_TABLE_SUPPORT_CONTRACTS);
	}

	function lookup_shareholder_function_type($ID){
		return lookup_db_value("shareholder_id", "function_type_id", $ID, DB_TABLE_SHAREHOLDERS);
	}

	function lookup_quarter($ID){
		return lookup_db_value("quarter_id", "quarter_name", $ID, DB_TABLE_QUARTERS);
	}

	function lookup_work_location($ID){
		return lookup_db_value("work_location_id", "location", $ID, DB_TABLE_WORK_LOCATIONS);
	}

	#############################
	## MISCELLANEOUS FUNCTIONS ##
	#############################

	// Lookup user information

	function lookup_user_information($UserID){
		$Query = "SELECT * FROM " . DB_TABLE_USERS . " WHERE user_id=". $UserID;
		$Result = db_query($Query);
		if(row_count($Result)){
			return row_fetch_assoc($Result);
		}
		return array();
	}

	// Generate an array of forms pending approval/denial
        // 2.0.5.1: Give only shareholder's pending forms if called from shareholder portal

	function pending_forms($portal=false){

                // if the request is from portal then load the portal formids
                if ($portal) {
                      
                     //get username from the cookie
                     $Uname= validate("alphanum", $_GET['u_name'], "User Name", 1);
                     /// find the matching shareholder IDS for this username
                     $Sh_Query="SELECT shareholder_id  from " . DB_TABLE_SHAREHOLDERS . " WHERE username='" . $Uname. "' AND deleted=0";
                      
                      $Result = db_query($Sh_Query);
                      $r_count=row_count($Result);
                      $sh_ids=array();
                       
                      // store the shareholder ids in array
                      while($this_row = row_fetch_assoc($Result)) {
                              array_push($sh_ids, $this_row['shareholder_id']);
                      }


                     $form_specs_raw = file_get_contents(PATH_ROOT_DIR . '/' . PATH_WWW .'/ajax/form_specs.json');
                     $protal_form_info = json_decode($form_specs_raw, true)['restricted_login_info'];
                     $portal_formids = array_merge($protal_form_info['restricted_login_dr_forms'],$protal_form_info['restricted_login_forms']);
                 }
                //  Generate an arry of form table => form_id 
		$FormTables = generate_db_array("form_table", "form_id", DB_TABLE_FORMS, array('deleted' => 0));

		foreach($FormTables as $Table => $form_id){

                      if ($portal) {
                           // check if the form_id in the portal form_id list 
                           if (in_array($form_id, $portal_formids)) {
                               // there could be more than one id for a shareholder
                               foreach ($sh_ids as $UserID) {

                                $Queries[] = "(SELECT '" . $Table . "' AS form_table,  ft.id  AS row_id, ft.filename, ft.activity_date, ft.date_created, ft.created_by FROM " . $Table . "  AS ft LEFT JOIN form_roles as fr ON ft.id = fr.execution_id WHERE ft.deleted=0 AND ft.status_id=1 AND fr.form_id=" . $form_id . " AND fr.shareholder_id =" . $UserID . " )";
                               }
                           }
                      }else { 
                             
                             //$Queries[] = "(SELECT '" . $Table . "' AS form_table, '" . $Name . "' AS form_name, id AS row_id, filename, activity_date, date_created, created_by FROM " . $Table . " WHERE deleted=0 AND status_id=1)";
                             $Queries[] = "(SELECT '" . $Table . "' AS form_table, id AS row_id, filename, activity_date, date_created, created_by FROM " . $Table . " WHERE deleted=0 AND status_id=1)";
                      }
		}

		if(@$Queries){


			$Query = implode(" UNION ", $Queries);
			$Query .= " ORDER BY activity_date, date_created, created_by";

			$Result = db_query($Query);
			$Count = row_count($Result);

			if($Count > 0){
				while($Row = row_fetch_assoc($Result)){
                                        // show the filename but remove the docx extentsion
                                        $All[$Row['form_table'] . "--" . $Row['row_id']] = str_replace('.docx', '', $Row['filename']);
				}
			}

			return @$All;

		}else{
			return FALSE;
		}

	}

	// Generate an array of forms pending a signed document upload

	function forms_pending_upload(){
		$FormTables = generate_db_array("form_table", "form_name", DB_TABLE_FORMS, array('deleted' => 0));

		foreach($FormTables as $Table => $Name){
			$Queries[] = "(SELECT '" . $Table . "' AS form_table, '" . $Name . "' AS form_name, id AS row_id, activity_date, date_created, created_by FROM " . $Table . " WHERE deleted=0 AND status_id=2 AND filename_signed IS NULL)";
		}

		if(@$Queries){


			$Query = implode(" UNION ", $Queries);
			$Query .= " ORDER BY activity_date, date_created, created_by";

			$Result = db_query($Query);
			$Count = row_count($Result);

			if($Count > 0){
				while($Row = row_fetch_assoc($Result)){
					$All[$Row['form_table'] . "--" . $Row['row_id']] = $Row['form_name'] . " (" . $Row['activity_date'] . ")";
				}
			}

			return @$All;

		}else{
			return FALSE;
		}

	}

	// Lookup the name of a form given its ID

	function lookup_form_name($FormID){
		$Query = "SELECT form_name FROM " . DB_TABLE_FORMS . " WHERE form_id='$FormID'";
		$Result = db_query($Query);
		$Row = row_fetch($Result);

		return @$Row[0];
	}
	// Lookup the form table of a form given its ID

	function lookup_form_table($FormID){
		$Query = "SELECT form_table FROM " . DB_TABLE_FORMS . " WHERE form_id='$FormID'";
		$Result = db_query($Query);
		$Row = row_fetch($Result);

		return @$Row[0];
	}

	// Lookup the filename of a form given its ID

	function lookup_form_filename($FormID){
		$Query = "SELECT filename FROM " . DB_TABLE_FORMS . " WHERE form_id='$FormID'";
		$Result = db_query($Query);
		$Row = row_fetch($Result);

		return @$Row[0];
	}

	// Create an audit entry
	// Assumptions:
	// - Audit table has a field called "audit_created_by"
	// - Audit table has identical fields as base table along with a few audit table fields
	// - The audit table is named "audit_{$BaseTableName}"
	// - The base table ID column is called "{$BaseTableName}_id";
	// - The audit table has a trigger to update the audit table timestamps

	function create_audit_entry($Table, $RowID, $UserID, $ExtraAuditInfo = NULL){

		// Grab the lastest changes from the table

		$Query = "SELECT * FROM " . $Table . " WHERE " . $Table . "_id=" . $RowID;
		$Result = db_query($Query);
		$RowInfo = row_fetch_all($Result);
		$AuditInfo['audit_created_by']   = $UserID;
		$AuditInfo['audit_date_created'] = "NOW()";

		// Add to the audit table

		return db_query(create_sql_insert((is_array($ExtraAuditInfo) ? array_merge($RowInfo[0], $AuditInfo, $ExtraAuditInfo) : array_merge($RowInfo[0], $AuditInfo)), "audit_" . $Table));
	}

	// Check to see if a given bag has been used yet

	function can_bag_be_used($ID){

		// Get a list of bags not currently used but that are pending approval

		$PendingBags = list_pending_tamper_evident_bags();

		// Check to see if the specific bag is marked as used

		$Query = "SELECT used FROM " . DB_TABLE_TAMPER_EVIDENT_BAGS . " WHERE bag_id=" . $ID;
		$Result = db_query($Query);
		$Row = row_fetch($Result);

		if(@$PendingBags){
			return ($Row[0] == 0 && !array_key_exists($ID, $PendingBags) ? TRUE : FALSE);
		}else{
			return ($Row[0] == 0 ? TRUE : FALSE);
		}
	}

	// Lookup the form ID for a given table name

	function lookup_from_id_from_table_name($TableName){
		$Query = "SELECT form_id FROM " . DB_TABLE_FORMS . " WHERE form_table='" . $TableName . "' AND deleted=0";
		$Result = db_query($Query);
		$Row = row_fetch($Result);

		return @$Row[0];
	}

	// Find what site a box belongs to

	function lookup_box_site($BoxID){
		$Query = "SELECT site_id FROM " . DB_TABLE_BOXES . " WHERE box_id='" . $BoxID . "'";
		$Result = db_query($Query);
		$Row = row_fetch($Result);

		return @$Row[0];
	}

	// Find what site a box belongs to

	function lookup_key_box($KeyID){
		$Query = "SELECT box_id FROM " . DB_TABLE_KEYS . " WHERE physical_key_id='" . $KeyID . "'";
		$Result = db_query($Query);
		$Row = row_fetch($Result);

		return @$Row[0];
	}

	// Lookup the filename for a given form execution

	function lookup_filename_for_form_execution($FormID, $ExecutionID, $SignedFile = FALSE){
		$Query = "SELECT form_table FROM " . DB_TABLE_FORMS . " WHERE form_id=". $FormID;
		$Result = db_query($Query);
		$Table = row_fetch($Result);

		$Query = "SELECT filename, filename_signed FROM " . $Table[0] . " WHERE id=" . $ExecutionID;
		$Result = db_query($Query);
		$Filename = row_fetch($Result);

		if($SignedFile){
			return @$Filename[1];
		}else{
			return @$Filename[0];
		}
	}

	// Check to see if a piece of equipment is marked as desroyed

	function is_eqiupment_destroyed($EquipmentID){
		$Query = "SELECT status FROM " . DB_TABLE_EQUIP . " WHERE equipment_id=". $EquipmentID;
		$Result = db_query($Query);
		$Row = row_fetch($Result);

		return ($Row[0] == "Destroyed" ? TRUE : FALSE);
	}

	// Check to see if a share is marked as desroyed

	function is_share_destroyed($ShareID){
		$Query = "SELECT date_destroyed FROM " . DB_TABLE_SHARES . " WHERE share_id=". $ShareID;
		$Result = db_query($Query);
		$Row = row_fetch($Result);

		return ($Row[0] ? TRUE : FALSE);
	}

	// Check to see if a shareholder can be assigned to a given site
	// Note: This function does not return any value, but if a conflict is found it will display an error message

	function can_shareholder_be_assigned_to_site($ShareholderID, $SiteID){
		$Query = "SELECT cbo_user FROM " . DB_TABLE_SHAREHOLDERS . " WHERE shareholder_id=" . $ShareholderID;
		$Result = db_query($Query);
		list($CBOUser) = row_fetch($Result);

		if($CBOUser == 1 && $SiteID != 1){
			add_message('error', $GLOBALS['errorMessage']['site_cbo_users']);
		}

		if($CBOUser == 0 && $SiteID == 1){
			add_message('error', $GLOBALS['errorMessage']['site_non_cbo_users']);
		}

		if(!preg_match('/^(1|2|3|5)$/', $SiteID)){
			add_message('error', $GLOBALS['errorMessage']['not_valid_sharehholder_site']);
		}
	}

	// Mark a tamper evident bag as used => opened (2)

	function mark_bag_as_opened($BagID){
		return db_query(create_sql_update(array("used" => 2), array("bag_id" => $BagID), DB_TABLE_TAMPER_EVIDENT_BAGS));
	}

	// Check to see if a username is backlisted

	function is_shareholder_blacklisted($Username){
		$Query = "SELECT username FROM " . DB_TABLE_SHAREHOLDER_BLACKLIST . " WHERE username='" . $Username . "' AND deleted=0";
		$Result = db_query($Query);
		$Count = row_count($Result);

		return ($Count == 0 ? FALSE : TRUE);
	}

	// Lookup a shareholders functions

	function lookup_shareholder_functions($ShareholderID, $IgnoreUnassigned = FALSE){
		$Query = "SELECT function_id, function_id_secondary FROM " . DB_TABLE_SHAREHOLDERS . " WHERE shareholder_id='" . $ShareholderID . "'";
		$Result = db_query($Query);
		$Count = row_count($Result);

		if($Count > 0){
			$Row = row_fetch($Result);

			$Functions = array();

			if($IgnoreUnassigned){
				if($Row[0] && $Row[0] != 4){ $Functions['primary']   = $Row[0]; }
				if($Row[1] && $Row[0] != 4){ $Functions['secondary'] = $Row[1]; }
			}else{
				if($Row[0]){ $Functions['primary']   = $Row[0]; }
				if($Row[1]){ $Functions['secondary'] = $Row[1]; }
			}

			return $Functions;
		}else{
			return array();
		}
	}

	// Determine how many boxes are assigned to a given shareholder

	function shareholder_box_count($SharehholderID, $ExcludeBox = NULL){
		if($ExcludeBox){
			$Query = "SELECT box_id FROM " . DB_TABLE_KEYS . " WHERE deleted=0 AND shareholder_id='" . $SharehholderID . "' AND box_id!='" . $ExcludeBox . "' GROUP BY box_id";
		}else{
			$Query = "SELECT box_id FROM " . DB_TABLE_KEYS . " WHERE deleted=0 AND shareholder_id='" . $SharehholderID . "' GROUP BY box_id";
		}
		$Result = db_query($Query);
		return row_count($Result);
	}

	// If the 2nd physical key is being distributed to a shareholder verify that it is the same shareholder who has possession of the first key
	// Note: This check should be performed prior to updating the database as it is exlcuding the given key and will return a false positive if the changes have been committed to the database prior to this check

	function can_key_be_distributed_to_shareholder($KeyID, $TargetShareholder){
		$Query = "SELECT shareholder_id FROM " . DB_TABLE_KEYS . " WHERE box_id=(SELECT box_id FROM " . DB_TABLE_KEYS . " WHERE physical_key_id=" . $KeyID . ") AND shareholder_id IS NOT NULL AND physical_key_id!=" . $KeyID;
		$Result = db_query($Query);
		$Count = row_count($Result);

		if($Count > 0){
			$ShareholderID = row_fetch($Result);

			if($ShareholderID[0] != $TargetShareholder){
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	// Check if a CBO shareholder already has a key for a given site (based on their function)
	// Note: This function does not return any value, but if a conflict is found it will display an error message

	function can_cbo_shareholder_obtain_key($ShareholderID, $DesiredKeyID){
		$Query = "SELECT b.site_id, k.physical_key_type_id FROM " . DB_TABLE_KEYS . " k LEFT JOIN " . DB_TABLE_BOXES . " b ON b.box_id=k.box_id WHERE k.physical_key_id='" . $DesiredKeyID . "'";
		$Result = db_query($Query);
		list($DesiredKeySite, $DesiredKeyType) = row_fetch($Result);

		$Query = "SELECT function_id, box_number, b.site_id, physical_key_type_id FROM " . DB_TABLE_SHAREHOLDERS . " s LEFT JOIN " . DB_TABLE_KEYS . " pk ON pk.shareholder_id=s.shareholder_id LEFT JOIN " . DB_TABLE_BOXES . " b ON b.box_id=pk.box_id WHERE pk.deleted=0 AND cbo_user=1 AND s.shareholder_id=" . $ShareholderID;
		$Result = db_query($Query);

		while(list($FunctionID, $BoxNumber, $BoxSite, $KeyType) = row_fetch($Result)){
			if($FunctionID == 9){	// HSM
				if(preg_match(REGEX_BOXES_HSM, $BoxNumber)){
					if($BoxSite == $DesiredKeySite){
						add_message('error', $GLOBALS['errorMessage']['already_have_hsm_key_for_site']);
					}
				}
			}elseif($FunctionID == 10){	// App Cards/Passwords

				/* Ignoring this check as technically a CBO user can obtain 4 app card/password boxes (per CBO on 2013-11-07) */

				// if(preg_match(REGEX_BOXES_PASSWORDS, $BoxNumber)){
					// if($BoxSite == $DesiredKeySite){
						// add_message('error', $GLOBALS['errorMessage']['already_have_password_key_for_site']);
					// }
				// }
			}elseif($FunctionID == 11){	// Common
				if($KeyType == $DesiredKeyType){
					if($BoxSite == $DesiredKeySite){
						add_message('error', $GLOBALS['errorMessage']['already_have_common_key_for_site']);
					}
				}
			}
		}
	}

	// Check if a physical key can be assigned to a given shareholder
	// Note: This function does not return any value, but if a conflict is found it will display an error message

	function check_key_movement($ShareholderID, $KeyID){

		// Lookup shareholder info

		$Query = "SELECT cbo_user, function_id, site_id, function_type_id FROM " . DB_TABLE_SHAREHOLDERS . " WHERE shareholder_id='" . $ShareholderID . "'";
		$Result = db_query($Query);
		list($CBOUser, $FunctionID, $SiteID, $FunctionTypeID) = row_fetch($Result);

		// Lookup key info

		$Query = "SELECT k.box_id, k.physical_key_type_id, k.shareholder_id, b.box_number, b.site_id FROM " . DB_TABLE_KEYS . " k LEFT JOIN " . DB_TABLE_BOXES . " b ON b.box_id=k.box_id WHERE k.physical_key_id='" . $KeyID . "'";
		$Result = db_query($Query);
		list($BoxID, $KeyTypeID, $ExistingShareholderID, $BoxNumber, $KeySite) = row_fetch($Result);

		// Verify that the selected user is has the "Physical Key" function type

		if($FunctionTypeID == 1){

			// If the key is not changing shareholders then no further checks are needed

			if($ExistingShareholderID != $ShareholderID){

				// D/SO (Non-CBO users only)

				if($FunctionID == 2){
					if(shareholder_box_count($ShareholderID, $BoxID) > 0){ add_message('error', $GLOBALS['errorMessage']['only_one_box_at_time']); }	// Can only have one box at a time
					if($CBOUser == 1){ message_substitution('error', $GLOBALS['errorMessage']['function_cant_be_cbo'], "D/SO"); } // D/SO shareholders cannot be part of CBO  (should already be enforce via shareholder panel)
					if(!preg_match(REGEX_BOXES_DSO, $BoxNumber)){ add_message('error', $GLOBALS['errorMessage']['dso_boxes']); }		// Only boxes 17-22 in BRN or ILG
				}

				// M of N (Non-CBO users only)

				if($FunctionID == 3){
					if(shareholder_box_count($ShareholderID, $BoxID) > 0){ add_message('error', $GLOBALS['errorMessage']['only_one_box_at_time']); }	// Can only have one box at a time
					if($CBOUser == 1){ message_substitution('error', $GLOBALS['errorMessage']['function_cant_be_cbo'], "MofN"); } // MofN shareholders cannot be part of CBO  (should already be enforce via shareholder panel)
					if(!preg_match(REGEX_BOXES_MOFN, $BoxNumber)){ add_message('error', $GLOBALS['errorMessage']['mofn_boxes']); }		// Only boxes 1-16 in BRN or ILG
				}

				// HSM

				if($FunctionID == 9){
					if($CBOUser == 1){ can_cbo_shareholder_obtain_key($ShareholderID, $KeyID); }
					if($CBOUser == 0){ if(shareholder_box_count($ShareholderID, $BoxID) > 0){ add_message('error', $GLOBALS['errorMessage']['only_one_box_at_time']); } }	// Non-CBO users can only have one box at a time
					if(!preg_match(REGEX_BOXES_HSM, $BoxNumber)){ add_message('error', $GLOBALS['errorMessage']['hsm_boxes']); }		// Only boxes 23 in BRN or ILG
				}

				// App Cards/Passwords

				if($FunctionID == 10){
					if($CBOUser == 1){ can_cbo_shareholder_obtain_key($ShareholderID, $KeyID); }
					if($CBOUser == 0){ if(shareholder_box_count($ShareholderID, $BoxID) > 0){ add_message('error', $GLOBALS['errorMessage']['only_one_box_at_time']); } }	// Non-CBO users can only have one box at a time
					if(!preg_match(REGEX_BOXES_PASSWORDS, $BoxNumber)){ add_message('error', $GLOBALS['errorMessage']['key_password_boxes']); }		// Only box 24-25 in BRN or ILG
				}

				// Common

				if($FunctionID == 11){
					if($CBOUser == 1){ can_cbo_shareholder_obtain_key($ShareholderID, $KeyID); }
					if($CBOUser == 0){ if(shareholder_box_count($ShareholderID, $BoxID) > 0){ add_message('error', $GLOBALS['errorMessage']['only_one_box_at_time']); } }	// Non-CBO users can only have one box at a time
					if($KeyTypeID != 2){ add_message('error', $GLOBALS['errorMessage']['common_shareholders_keys']); }		// Only common keys
				}

			}

		}else{
			add_message('error', $GLOBALS['errorMessage']['cannot_obtain_physical_key']);
		}
	}

	// Checks to see if a given function is in the list of functions that a CBO user can be assigned to

	function is_permitted_cbo_function($FunctionID){
		return preg_match(REGEX_CBO_FUNCTIONS, $FunctionID); // Permits Unassigned, HSM, Passwords, Common, Pool A (x2), Pool B (x2)
	}

	// Checks to see if a given function is in the list of functions that a user with "data center access" privileges can be assigned to

	function is_permitted_data_center_access_function($FunctionID){
		return preg_match(REGEX_DCA_FUNCTIONS, $FunctionID);	// Permits Unassigned, MegaSafe (x2)
	}

	// Check if a physical key shareholder can change to another physical key function (based on what is alredy written to the database)
	// Note: This function does not return any value, but if a conflict is found it will display an error message

	function can_shareholder_take_on_physical_key_function($ShareholderID, $DesiredFunctionID){

		// Grab some shareholder information

		$Query = "SELECT function_type_id, cbo_user, has_data_center_access FROM " . DB_TABLE_SHAREHOLDERS . " WHERE shareholder_id=" . $ShareholderID;
		$Result = db_query($Query);
		list($FunctionTypeID, $CBOUser, $DCAUser) = row_fetch($Result);

		// Verify that the user is assigned to the physical key "function type"

		if($FunctionTypeID != 1){
			add_message('error', $GLOBALS['errorMessage']['not_physical_key_function_type']);
		}

		// Verify that the selected "function" is in fact a physical key function

		if(array_key_exists($DesiredFunctionID, list_access_functions())){
			add_message('error', $GLOBALS['errorMessage']['function_type_disagree_function']);
		}

		// If the user is CBO, verify they are not taking on a forbidden function

		if($CBOUser == 1){
			if(!is_permitted_cbo_function($DesiredFunctionID)){
				add_message('error', $GLOBALS['errorMessage']['cbo_function_violation']);
			}
		}

		// If the user has data center access, verify they are not taking on a forbidden function

		if($DCAUser == 1){
			if(!is_permitted_data_center_access_function($DesiredFunctionID)){
				add_message('error', $GLOBALS['errorMessage']['dca_function_violation']);
			}
		}

		// Verify the user has no keys in their posession

		if(shareholder_box_count($ShareholderID) > 0){
			add_message('error', $GLOBALS['errorMessage']['shareholder_still_possesses_key']);
		}

	}

	// Check if an access shareholder can add a secondary access function (based on what is alredy written to the database)
	// Note: This function does not return any value, but if a conflict is found it will display an error message

	function can_shareholder_take_on_access_function($ShareholderID, $FunctionID){

		// Grab some shareholder information

		$Query = "SELECT cbo_user, has_data_center_access FROM " . DB_TABLE_SHAREHOLDERS . " WHERE shareholder_id=" . $ShareholderID;
		$Result = db_query($Query);
		list($CBOUser, $DCAUser) = row_fetch($Result);

		// Verify the shareholder is assigned to the "access" function type

		if(lookup_shareholder_function_type($ShareholderID) == 1){
			add_message('error', $GLOBALS['errorMessage']['not_access_function_type']);
		}

		// If the user is CBO, verify they are not taking on a forbidden function

		if($CBOUser == 1){
			if(!is_permitted_cbo_function($FunctionID)){
				add_message('error', $GLOBALS['errorMessage']['cbo_function_violation']);
			}
		}

		// If the user has data center access, verify they are not taking on a forbidden function

		if($DCAUser == 1){
			if(!is_permitted_data_center_access_function($FunctionID)){
				add_message('error', $GLOBALS['errorMessage']['dca_function_violation']);
			}
		}

		// Verify that an access function was selected

		if(array_key_exists($FunctionID, list_physical_key_functions())){
			add_message('error', $GLOBALS['errorMessage']['function_type_disagree_function']);
		}

		// Verify that the selected shareholder has an "unassigned" spot

		$CurrentFunctions = lookup_shareholder_functions($ShareholderID);

		if(!in_array(4, $CurrentFunctions)){
			add_message('error', $GLOBALS['errorMessage']['no_avaliable_function_slots']);
		}

		// Verify that the user is not being assigned to a function they already have

		if(in_array($FunctionID, $CurrentFunctions)){
			add_message('error', $GLOBALS['errorMessage']['same_function']);
		}

		// If the user is being assigned to one of the "pool" functions make sure they don't already possess the other one for the same stie

		if(preg_match(REGEX_POOL_ALL_FUNCTIONS, $FunctionID)){

			// If the desired function is part of the BRN pool make sure the shareholder does not have the other BRN pool

			if(preg_match(REGEX_POOL_BRN_FUNCTIONS, $FunctionID)){
				if(($FunctionID == 24 && in_array(26, $CurrentFunctions)) || ($FunctionID == 26 && in_array(24, $CurrentFunctions))){
					add_message('error', $GLOBALS['errorMessage']['both_pools_same_site']);
				}
			}

			// If the desired function is part of the ILG pool make sure the shareholder does not have the other ILG pool

			if(preg_match(REGEX_POOL_ILG_FUNCTIONS, $FunctionID)){
				if(($FunctionID == 25 && in_array(27, $CurrentFunctions)) || ($FunctionID == 27 && in_array(25, $CurrentFunctions))){
					add_message('error', $GLOBALS['errorMessage']['both_pools_same_site']);
				}
			}
		}

		// Verify the user has no keys in their posession

		if(shareholder_box_count($ShareholderID) > 0){
			add_message('error', $GLOBALS['errorMessage']['shareholder_still_possesses_key']);
		}

	}

	// Create a snapshot of a safe deposit box

	function create_box_snapshot($BoxID, $FormID = NULL, $ExecutionID = NULL, $UserID = NULL){
		$Info['form_id']      = $FormID;
		$Info['execution_id'] = $ExecutionID;
		$Info['box_id']       = $BoxID;
		$Info['created_by']   = ($UserID ? $UserID : 1);	// If no user given set to "cbo_admin" user

		if(db_query(create_sql_insert($Info, DB_TABLE_BOX_SNAPSHOT))){

			$ID = last_query_id();

			$Query = "INSERT " . DB_TABLE_BOX_SNAPSHOT_CONTENTS . "(box_snapshot_id, share_id) SELECT " . $ID . ", share_id FROM " . DB_TABLE_SHARES . " WHERE box_id=" . $BoxID . " AND deleted=0";
			$Result = db_query($Query);

			return $Result;

		}else{
			return FALSE;
		}
	}

	// Find the box that a share is currently placed in

	function lookup_box_for_share($ShareID){
		$Query = "SELECT box_id FROM " . DB_TABLE_SHARES . " WHERE share_id=" . $ShareID;
		$Result = db_query($Query);
		$Row = row_fetch($Result);

		return ($Row[0] ? $Row[0] : NULL);
	}

        // get the site_id value
        function get_site_id($this_table,$this_field, $this_field_val) {
             $Query = "SELECT site_id from " . $this_table . " WHERE " . $this_field . "=" . $this_field_val;
             $Result = db_query($Query);
             $Row = row_fetch($Result);

             return ($Row[0] ? $Row[0] : NULL);
         }
        // get site_id using the escrow location table
       function get_site_id_from_escrow_location($this_table,$this_field, $this_field_val) {
              $Query = "SELECT escrow_location.site_id from escrow_location LEFT JOIN " . $this_table . " ON " . $this_table . ".escrow_location_id=escrow_location.escrow_location_id where " . $this_table ."." . $this_field . "=" .  $this_field_val;
             $Result = db_query($Query);
             $Row = row_fetch($Result);

             return ($Row[0] ? $Row[0] : NULL);
        }

	// Check to see if a share is currently in escrow

	function is_share_in_escrow($ShareID){
		$Query = "SELECT escrow_location_id, bag_id, box_id FROM " . DB_TABLE_SHARES . " WHERE escrow_location_id IS NOT NULL AND bag_id IS NOT NULL AND box_id IS NULL AND share_id=" . $ShareID;
		$Result = db_query($Query);
		$Count = row_count($Result);

		return ($Count == 1 ? TRUE : FALSE);
	}

	// Check to see if a share is currently distributed

	function is_share_distributed($ShareID){
		$Query = "SELECT escrow_location_id, box_id FROM " . DB_TABLE_SHARES . " WHERE escrow_location_id IS NULL AND box_id IS NOT NULL AND share_id=" . $ShareID;
		$Result = db_query($Query);
		$Count = row_count($Result);

		return ($Count == 1 ? TRUE : FALSE);
	}

	// Check to see if a share should always be bagged

	function is_share_always_bagged($ShareID){
		$Query = "SELECT always_bagged FROM " . DB_TABLE_SHARES . " WHERE share_id=" . $ShareID;
		$Result = db_query($Query);
		$Row = row_fetch($Result);

		return ($Row[0] == 1 ? TRUE : FALSE);
	}

	// Check to see if a key is currently in escrow

	function is_key_in_escrow($KeyID){
		$Query = "SELECT escrow_location_id, bag_id, shareholder_id FROM " . DB_TABLE_KEYS . " WHERE escrow_location_id IS NOT NULL AND bag_id IS NOT NULL AND shareholder_id IS NULL AND physical_key_id=" . $KeyID;
		$Result = db_query($Query);
		$Count = row_count($Result);

		return ($Count == 1 ? TRUE : FALSE);
	}

	// Check to see if a trka key is currently in escrow

	function is_traka_key_in_escrow($KeyID){
		$Query = "SELECT escrow_location_id, shareholder_id FROM " . DB_TABLE_KEYS . " WHERE escrow_location_id IS NOT NULL AND shareholder_id IS NULL AND physical_key_id=" . $KeyID;
		$Result = db_query($Query);
		$Count = row_count($Result);

		return ($Count == 1 ? TRUE : FALSE);
	}

	// Check to see if a key is currently distributed

	function is_key_distributed($KeyID){
		$Query = "SELECT escrow_location_id, bag_id, shareholder_id FROM " . DB_TABLE_KEYS . " WHERE escrow_location_id IS NULL AND bag_id IS NULL AND shareholder_id IS NOT NULL AND physical_key_id=" . $KeyID;
		$Result = db_query($Query);
		$Count = row_count($Result);

		return ($Count == 1 ? TRUE : FALSE);
	}

	// Check to see if a trka key is currently distributed

	function is_traka_key_distributed($KeyID){
		$Query = "SELECT escrow_location_id, shareholder_id FROM " . DB_TABLE_KEYS . " WHERE escrow_location_id IS NULL AND shareholder_id IS NOT NULL AND physical_key_id=" . $KeyID;
		$Result = db_query($Query);
		$Count = row_count($Result);

		return ($Count == 1 ? TRUE : FALSE);
	}

	// Check to see if a piece of equipment is currently in escrow

	function is_equipment_in_escrow($EquipmentID){
		$Query = "SELECT escrow_location_id, bag_id FROM " . DB_TABLE_EQUIP . " WHERE escrow_location_id IS NOT NULL AND bag_id IS NOT NULL AND equipment_id=" . $EquipmentID;
		$Result = db_query($Query);
		$Count = row_count($Result);

		return ($Count == 1 ? TRUE : FALSE);
	}

	// Check to see if a piece of equipment is currently NOT in escrow

	function is_equipment_not_in_escrow($EquipmentID){
		$Query = "SELECT escrow_location_id, bag_id FROM " . DB_TABLE_EQUIP . " WHERE escrow_location_id IS NULL AND bag_id IS NULL AND equipment_id=" . $EquipmentID;
		$Result = db_query($Query);
		$Count = row_count($Result);

		return ($Count == 1 ? TRUE : FALSE);
	}

	// Lookup equipment info

	function lookup_equipment_info($ID){
		$Query = "SELECT * FROM " . DB_TABLE_EQUIP . " WHERE equipment_id='" . $ID . "'";
		$Result = db_query($Query);
		$Count = row_count($Result);

		if($Count == 1){
			return row_fetch_assoc($Result);
		}else{
			return FALSE;
		}
	}

	// Lookup physical key info

	function lookup_physical_key_info($ID){
		$Query = "SELECT * FROM " . DB_TABLE_KEYS . " WHERE physical_key_id='" . $ID . "'";
		$Result = db_query($Query);
		$Count = row_count($Result);

		if($Count == 1){
			return row_fetch_assoc($Result);
		}else{
			return FALSE;
		}
	}

	// Lookup share info

	function lookup_share_info($ID){
		$Query = "SELECT * FROM " . DB_TABLE_SHARES . " WHERE share_id='" . $ID . "'";
		$Result = db_query($Query);
		$Count = row_count($Result);

		if($Count == 1){
			return row_fetch_assoc($Result);
		}else{
			return FALSE;
		}
	}

	// Lookup the status of a piece of equipment

	function equipment_status($ID){
		$Info = lookup_equipment_info($ID);
		return $Info['status'];
	}

	// Lookup the type of location for a given escrow location

	function lookup_type_of_escrow_location($ID){
		$Query  = "SELECT escrow_location_type_id FROM " . DB_TABLE_ESCROW_LOCATIONS . " WHERE escrow_location_id='" . $ID . "'";
		$Result = db_query($Query);
		$Row = row_fetch($Result);

		return $Row[0];
	}

	// Check to see if a given serial number exists

	function does_serial_number_exist($SerialNumber, $Table, $IgnoreID = NULL){
		if($Table == DB_TABLE_SHARES){
			$Query  = "SELECT * FROM " . DB_TABLE_SHARES . " WHERE serial_number='" . $SerialNumber . "'" . ($IgnoreID ? " AND share_id!='" . $IgnoreID . "'" : NULL);
		}elseif($Table == DB_TABLE_KEYS){
			$Query  = "SELECT * FROM " . DB_TABLE_KEYS . " WHERE serial_number='" . $SerialNumber . "'" . ($IgnoreID ? " AND physical_key_id!='" . $IgnoreID . "'" : NULL);
		}elseif($Table == DB_TABLE_EQUIP){
			$Query  = "SELECT * FROM " . DB_TABLE_EQUIP . " WHERE serial_number='" . $SerialNumber . "'" . ($IgnoreID ? " AND equipment_id!='" . $IgnoreID . "'" : NULL);
		}else{
			return TRUE;
		}

		$Result = db_query($Query);
		$Count  = row_count($Result);

		if($Count == 0){
			return FALSE;
		}else{
			return TRUE;
		}
	}

	// Get a list of physical keys that a given shareholder has in their possesion

	function shareholder_physical_keys($ShareholderID){
		return generate_db_array("physical_key_id", "label", DB_TABLE_KEYS, array('deleted' => 0, 'shareholder_id' => $ShareholderID));
	}

	// Get a list of physical keys that a given shareholder has had in their possesion at one time or another

	function shareholder_past_physical_keys($ShareholderID, $EventStartDate){
		$Query = "SELECT physical_key_id AS id, label FROM " . DB_TABLE_AUDIT_KEYS . " WHERE deleted=0 AND shareholder_id=" . $ShareholderID . " AND audit_date_created BETWEEN DATE_SUB('" . $EventStartDate . "', INTERVAL 1 YEAR) AND '" . $EventStartDate . " 23:59:59' GROUP BY physical_key_id ORDER BY label";
		$Result = db_query($Query);
		$Count = row_count($Result);

		if($Count > 0){
			while($Row = row_fetch_assoc($Result)){
				$All[$Row['id']] = $Row['label'];
			}
			return $All;
		}
		return array();
	}

	// Get a list of event reminders that need to be sent today

	function todays_event_email_reminders(){
		$Query = "SELECT e.*, GROUP_CONCAT(DISTINCT sh.username) AS usernames, ep.product_id, GROUP_CONCAT(DISTINCT p.product_name) AS product_names, s.site_name, ees.event_schedule_id, ees.days_notice, t.subject, t.body FROM
		" . DB_TABLE_EVENTS . " e
		LEFT JOIN " . DB_TABLE_EVENT_SHAREHOLDERS . " es ON es.event_id=e.event_id
		LEFT JOIN " . DB_TABLE_EVENT_PRODUCTS . " ep ON ep.event_id=e.event_id
		LEFT JOIN " . DB_TABLE_EVENT_SCHEDULE . " ees ON ees.event_id=e.event_id
		LEFT JOIN " . DB_TABLE_EMAIL_TEMPLATES . " t ON t.email_template_id=e.email_template_id
		LEFT JOIN " . DB_TABLE_SITES . " s ON s.site_id=e.site_id
		LEFT JOIN " . DB_TABLE_PRODUCTS . " p ON p.product_id=ep.product_id
		LEFT JOIN " . DB_TABLE_SHAREHOLDERS . " sh ON sh.shareholder_id=es.shareholder_id
		WHERE e.deleted=0 AND e.cancelled=0 AND ees.deleted=0 AND ees.trigger_date=CURDATE() AND ees.sent=0
		GROUP BY e.event_id";
		$Result = db_query($Query);
		$Count = row_count($Result);

		if($Count > 0){
			while($Row = row_fetch_assoc($Result)){
				$AllEvents[] = $Row;
			}
			return $AllEvents;
		}
		return array();
	}

	// Get a list of event details given an event ID

	function lookup_event_infromation($ID){
		$Query = "SELECT e.*, GROUP_CONCAT(DISTINCT sh.username) AS usernames, ep.product_id, GROUP_CONCAT(DISTINCT p.product_name) AS product_names, s.site_name, ees.event_schedule_id, ees.days_notice, t.subject, t.body FROM
		" . DB_TABLE_EVENTS . " e
		LEFT JOIN " . DB_TABLE_EVENT_SHAREHOLDERS . " es ON es.event_id=e.event_id
		LEFT JOIN " . DB_TABLE_EVENT_PRODUCTS . " ep ON ep.event_id=e.event_id
		LEFT JOIN " . DB_TABLE_EVENT_SCHEDULE . " ees ON ees.event_id=e.event_id
		LEFT JOIN " . DB_TABLE_EMAIL_TEMPLATES . " t ON t.email_template_id=e.email_template_id
		LEFT JOIN " . DB_TABLE_SITES . " s ON s.site_id=e.site_id
		LEFT JOIN " . DB_TABLE_PRODUCTS . " p ON p.product_id=ep.product_id
		LEFT JOIN " . DB_TABLE_SHAREHOLDERS . " sh ON sh.shareholder_id=es.shareholder_id
		WHERE e.event_id='" . $ID . "'
		GROUP BY e.event_id";
		$Result = db_query($Query);
		$Count = row_count($Result);

		if($Count > 0){
			return row_fetch_assoc($Result);
		}
		return array();
	}

	// Get a list of all upcoming/future events

	function list_upcoming_events(){
		return generate_db_array("event_id", "CONCAT(start_date, ' - ', (SELECT site_name FROM " . DB_TABLE_SITES . " WHERE site_id=event.site_id), ' - ', event_name) AS event_name", DB_TABLE_EVENTS, "deleted=0 AND cancelled=0 AND confirmed=0 AND CONCAT(start_date, ' ', start_time) >= NOW()", "start_date");
	}

	// Get a list of all past events that need confirmation

	function list_events_needing_confirmed(){
		return generate_db_array("event_id", "CONCAT(start_date, ' - ', (SELECT site_name FROM " . DB_TABLE_SITES . " WHERE site_id=event.site_id), ' - ', event_name) AS event_name", DB_TABLE_EVENTS, "deleted=0 AND cancelled=0 AND confirmed=0 AND CONCAT(start_date, ' ', start_time) < NOW()", "start_date");
	}

	// Get a list of all physical key sharehholders and the last time they were used in an event

	function list_event_shareholder_usage(){
		$Query = "SELECT s.shareholder_id AS id, s.function_type_id as function_type, shareholder_name AS name, s.site_id AS site, s.manager_name as manager_name, s.deleted AS deleted, s.approved AS approved, IF(MAX(e.start_date), MAX(e.start_date), 0) AS last_used FROM " . DB_TABLE_SHAREHOLDERS . " s LEFT JOIN " . DB_TABLE_EVENT_SHAREHOLDERS . " es ON s.shareholder_id=es.shareholder_id AND es.deleted=0 LEFT JOIN " . DB_TABLE_EVENTS . " e ON es.event_id=e.event_id AND e.deleted=0 AND e.cancelled=0 WHERE s.deleted=0 and s.approved=1 and s.function_type_id < 4 GROUP BY s.shareholder_id ORDER BY MAX(e.start_date), shareholder_name ASC";
		$Result = db_query($Query);
		$Count = row_count($Result);

		if($Count){
			while($Row = row_fetch_assoc($Result)){
				$Info[] = $Row;
			}
			return $Info;
		}
		return array();
	}
        // function to create share in the share table and in the audit_share table
        function create_share($table_name, $exec_id) {

           $form_passwd=false;

           if ($table_name == 'form_password_creation_attestation'){
               $get_data="SELECT activity_date, site_id, product_id,share_label, box_id, tamper_bag_id,serial_number,key_type_id, share_type_id, date_created,created_by,updated_by from " . $table_name . " WHERE id=" . $exec_id;
               $form_passwd=true;
                
           }else{ # share creation attestaion table
                $get_data="SELECT activity_date, site_id, share_label, product_id,key_type_id,media_type_id, share_type_id, mofn_val, share_set, tamper_bag_id,serial_number,escrow_location_id,date_created,created_by,updated_by from " . $table_name . " WHERE id=" . $exec_id;
               
                // query for the share creation
           }

           $Result = db_query($get_data);
           $NumItems = row_count($Result);

           if($NumItems < 1){
                  add_message('error', "No share data Available to Insert");
           }

           $formd_data=row_fetch_assoc($Result);


           if ($form_passwd) {
                $share_info['box_id']= $formd_data['box_id'];
                //$share_info['key_type_id']=$formd_data['key_type_id'];
                $share_info['box_id']=$formd_data['box_id'];
                //$share_info['share_type_id']= 7;
                $share_info['media_type_id']= 3;
                $share_info['mofn_threshold']= "N/A";
                $share_info['always_bagged']= 1;
               
           }else {
               $share_info['mofn_threshold']= $formd_data['mofn_val']; 
               $share_info['share_set']= $formd_data['share_set'];
               $share_info['escrow_location_id']= $formd_data['escrow_location_id'];
               //$share_info['key_type_id']= $formd_data['key_type_id'];
               //$share_info['share_type_id']= $formd_data['key_type_id'];
               $share_info['media_type_id']= $formd_data['media_type_id'];
           } 

           //common values
           $share_info['key_type_id']=$formd_data['key_type_id'];
           $share_info['share_type_id']= $formd_data['share_type_id'];
           $share_info['share_label']= $formd_data['share_label']; 
           $share_info['date_created']= $formd_data['activity_date'];
           $share_info['share_creation_date']= $formd_data['activity_date'];
           $share_info['serial_number']= $formd_data['serial_number'];
           $share_info['product_id']= $formd_data['product_id'];
           $share_info['environment_id']= 4;
           $share_info['bag_id']= $formd_data['tamper_bag_id'];
           $share_info['created_by']= $formd_data['created_by'];
           //$share_info['updated_by']= $formd_data['updated_by'];

           //echo '<pre>' . print_r($share_info, true) . '</pre>';

           // Insert this information into the  share table
           if(!db_query(create_sql_insert($share_info, DB_TABLE_SHARES))){ $Success = FALSE; }

           // get the share_id created 
           $get_share_data="SELECT share_id from share where share_label='" .$share_info['share_label'] . "' and serial_number='" . $share_info['serial_number'] . "'";

           $Result = db_query($get_share_data);
           
           $share_id=row_fetch_assoc($Result)['share_id'];

           return $share_id;
             
        }

        // check if a row exists
        function is_row_exists($table_name, $key_field, $key_val) {
           $Query="SELECT * from " . $table_name . " WHERE " . $key_field ."='" .  $key_val . "'";

           $Result = db_query($Query);
           $Count = row_count($Result);
           if($Count > 0){ 
              return TRUE;
           }else {        
            return FALSE;
         }
       }
        // check if a row in form table but not rejected 
        function is_row_exists_not_rejected($table_name, $key_field, $key_val) {
           $Query="SELECT * from " . $table_name . " WHERE " . $key_field ."='" .  $key_val . "' and status_id != 3";

           $Result = db_query($Query);
           $Count = row_count($Result);
           if($Count > 0){ 
              return TRUE;
           }else {        
            return FALSE;
         }
       }

        // check the site type
        function check_site_type($site_id) {
           $Query="SELECT site_type from  site WHERE site_id ='" .  $site_id. "'";
           $Result = db_query($Query);
           $site_type=row_fetch_assoc($Result)['site_type'];
           
            return $site_type;
       }

       // function to enable the restricted login
       function enable_restrict_login($user_list, $form_id, $prod_id) {

            $ret_stat=-1;
                
            foreach ($user_list as $user_id) {

               // for pma portal logins  
               if ($form_id == 54) {
                  
                  $Query="SELECT username FROM shareholder where shareholder_id=" . $user_id;
                  $Result = db_query($Query);
                  $uname=row_fetch_assoc($Result)['username'];  
                  
                  // check if this user is in the ALL Voting member
                   $Query="SELECT * FROM pma where product_id=1 and username='". $uname . "'";
                   $Result = db_query($Query);
                   if (row_count($Result) > 0) {
                       $prod_id=1;
                   }
                   $Query= "UPDATE pma set portal_login=1 WHERE deleted=0 and username='" . $uname ."' AND product_id=" . $prod_id;
                  $ret_stat=1;
               } else {
               $Query= "UPDATE shareholder set restricted=1 WHERE cbo_user=0 and shareholder_id=". $user_id;
                  $ret_stat=0;
               }
               $Result = db_query($Query);

            }
            return $ret_stat;
       }
      
      // function to check if the HSM decativation is pending
      function check_pending_deactivation($equip_id) {
           $Query="SELECT * from form_hsm_deactivation_attestation where equipment_id=" . $equip_id . " AND status_id=1";
           $Result = db_query($Query);
           $Count = row_count($Result);
           if($Count > 0){
            return true;
           }else {
            return false;
           } 
      }

      function get_sh_agreement_info($id) {
            $Query = "SELECT * FROM " . DB_TABLE_SHAREHOLDERS . " WHERE shareholder_id='" . $id . "'";
            $Result = db_query($Query);
            $Count = row_count($Result);
            $Row = row_fetch_assoc($Result);
            
            if ($Count == 1) {
             $agreement_data=array(
              'has_data'=> true,
              'SH_NAME' => $Row['shareholder_id'],
              ); 
            } else {
              $agreement_data = array (
                 'has_data' => false
               );
            }
             return $agreement_data;
      }

      function get_manager_id($mg_name) {
              $ret_array=array();
           
              $M_Query = "SELECT shareholder_id FROM " . DB_TABLE_SHAREHOLDERS . " WHERE shareholder_name='" . $mg_name . "' AND function_type_id=6";
              $M_Result = db_query($M_Query);
              $M_Count = row_count($M_Result);

              if ($M_Count == 1 ) {
                  $M_Row = row_fetch_assoc($M_Result);
                  $mg_id=$M_Row['shareholder_id'];
                  $ret_array['has_manager']= true;
                  $ret_array['mg_id']= $mg_id;
              } else { 
                  $ret_array['has_manager']= false;
              }
              return $ret_array;
      }
?>

