<?php
if (sizeof($argv) >= 2) {
	if ($argv[1] == '--help') {
	echo 
	"
	The PHP script will include these command line options (directives):

	•--file [csv file name]: This is the name of the CSV to be parsed and it will display the content of this file.

	The format of --file command should be:

	--file[filename.csv]

	•--create_table: This will cause the MySQL users table to be built (and no further action will be taken).

	The format of --create_table should be:

	--create_table -u -username -p -password -h -host

	•--dry_run: This will be used with the read content of file and create table. If the users accept the promopt, then it will insert the data into database.

	The format of this command should be:

	--dry_run --file[filename] -u -username -p -password -h -host

	•--help: This will output the above list of directives with details.\r\n";
	}
	else if ($argv[1] == '--dry_run' && sizeof($argv) != 9) {
		echo "The format of --dry_run command should be '--dry_run --file[filename] -u -username -p -password -h -host'  \r\n";
	}
	else if (sizeof($argv) != 2 && preg_match('/--file\[[\w%+\/-]+.csv\]/', $argv[1])) {
		echo "The format of --file command should be '--file[filename.csv]'  \r\n";
	}
	else if ($argv[1] == '--create_table' && sizeof($argv) != 8) {
		echo "The format of --create_table should be '--create_table -u -username -p -password -h -host'";
	}
	else if (($argv[1] != '--dry_run' 
			&& !preg_match('/--file\[[\w%+\/-]+.csv\]/', $argv[1])
			&& $argv[1] != '--create_table'
			&& $argv[1] != '--help')) {
		echo "Invalid Command!! \r\n";
	}
	else {
		switch ($argv[1]) {
			case '--dry_run':
				dryRun($argv);
				break;
			 case '--create_table':
			 	$user = substr($argv[3], 1);
				$pass = substr($argv[5], 1);
				$host = substr($argv[7], 1);
				if(createTable($host, $user, $pass, $db)) {
					echo "Create table successfully! \r\n";
				}
				break;
			 default:
				$file = substr($argv[1], 7, -1);
				if (getFile($file) != null) {
					print_r(getFile($file));
				}
				else {
					echo "File Not Exists!!\r\n";
				}
				
			 	break;
		}
	}
}
else {
	echo "Invalid Command!!\r\n";
}



function dryRun($command) {
	$isSuccessful = false;
	$file = substr($command[2], 7, -1);
	$user = substr($command[4], 1);
	$pass = substr($command[6], 1);
	$host = substr($command[8], 1);

	$records = getFile($file);
	if ($records != null) {
		$isSuccessful = createTable($host, $user, $pass, 'test');
		if ($isSuccessful) {
			echo "Create table successfully! \r\n";
			echo "Do you want to insert data? [Y/n]";
			$handle = fopen ("php://stdin","r");
			$line = fgets($handle);
			if (trim($line) == 'Y') {
				insertData($records, $host, $user, $pass, 'test');
			}
			if(trim($line) == 'n'){
	    		echo "Exit!!\n";
	    		exit;
			}
			fclose($handle);
		}	
	}
	else {
		echo "File Not Exists!!\r\n";
	}
	
}

function getFile($file) {
	if (file_exists($file)) {
		$data = fopen($file,"r");
 		$i = 0;
	 	$firstLine = true;
		while (($line = fgetcsv($data)) !== FALSE) {
			if($firstLine) {$firstLine = false; continue;}
	  		//$line is an array of the csv elements
	  		$records[$i] = $line;
	  		$i++;
		}
		fclose($data);
	}
	else {
		$records = null;
	}
	
	return $records;
}

function connect2Databse($host, $user, $pass, $db) {
	$charset = 'utf8';

	$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
	$opt = [
    	PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    	PDO::ATTR_EMULATE_PREPARES   => false,
    	PDO::ATTR_ERRMODE            => PDO::ERRMODE_WARNING,
	];
	$pdo = new PDO($dsn, $user, $pass, $opt);
	return $pdo;
}

function createTable($host, $user, $pass, $db) {
	$status = false;
	$pdo = connect2Databse($host, $user, $pass, $db);
	$sql = "CREATE TABLE IF NOT EXISTS USERS (ID INT(11) AUTO_INCREMENT PRIMARY KEY, NAME VARCHAR(50) NOT NULL, SURNAME VARCHAR(50) NOT NULL, EMAIL VARCHAR(100) NOT NULL UNIQUE);";
	$statement = $pdo->prepare($sql);
	$statement->execute();
	if ($statement->errorCode() == 0) {
		$status = true;
	}
	
	return $status;
}	

function insertData($data, $host, $user, $pass, $db) {
	$pdo = connect2Databse($host, $user, $pass, $db);
	$a = 1;
 	foreach($data as $record) {
		$sql = "INSERT INTO USERS (NAME, SURNAME, EMAIL) VALUES(:name, :surname, :email)";
		if (filter_var(trim($record[2]), FILTER_VALIDATE_EMAIL)) {
			$statement = $pdo->prepare($sql);
			$statement->execute(array(
			'name'    => ucfirst(trim($record[0])),
			'surname' => ucfirst(trim($record[1])),
			'email'   => strtolower(trim($record[2]))
			));
			if ($statement->errorCode() == 0) {
				echo $a.": New records inserted successfully \r\n";
				$a++;
			}
			else {
				echo $a.": ".$statement->errorInfo()[2]."\r\n";
				$a++;
				continue;
			}
		}
		else {
			echo $a.": $record[2] Email Invalid!! \r\n";
			$a++;
		}
	}
}
	
?>