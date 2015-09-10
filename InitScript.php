<?PHP

// Instantiate Class and variables
//
include_once('classes/classes.inc.php');

// Create Database 
//
#$db   = new SQLite3(DATABASE_FILE);
create_ipn_table();

echo "Done";

// END
//////////////////////////////////////////////

