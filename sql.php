<?php
/***********************************************************************************************
 * Database.php
 * 
 * The Database class is meant to simplify the task of accessing
 * information from the website's database.
 *
 * sql utilities
 * search in the database utilities
 * Daqing Yun @ Bioinformatics
 * Email: dyun@memphis.edu
 * Last Updated: Aug 26, 2014 
 ***********************************************************************************************/

include_once("constants.php");

/**
 * search in the database according to the keyword
 * and its type (terms, genes, tfs)
 */
function search_in_db($key1, $type1, $key2, $type2, $key3, $type3)
{
		 $db_handle = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die(mysql_error());
		 $db_found = mysql_select_db(DB_NAME, $db_handle) or die(mysql_error());
		 
		 if ($db_found)
		 {
			// construct the sql statement
			if($key1!="")
				$SQL = "SELECT * FROM unstem_new WHERE find_in_set('$key1', "."$type1".")";
			if($key2!="")
				$SQL = $SQL . "and find_in_set('$key2', "."$type2".")";
			if($key3!="")
				$SQL = $SQL . "and find_in_set('$key3', "."$type3".")";

			$SQL = $SQL . " ORDER BY find_in_set('$key1', "."$type1".")";
		 
		    // $result = mysql_query($SQL) or die("Invalid query: " . mysql_error());
		 	$result = mysql_query($SQL);
		 
		 	while (($row = mysql_fetch_assoc($result))==TRUE)
		 		$array[]=$row;
		 	
		 	mysql_close($db_handle);
		 }
		 else
		 	//$ErrMsg = "Database NOT Found";
		 	mysql_close($db_handle);
	
	// 
	if (count($array) <= 0)
		return ;
	
	return $array;
}
?>
