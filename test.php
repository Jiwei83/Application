<?php

$host = '127.0.0.1';
$user = 'root';
$pass = 'root';
$db   = 'test';
//createTable($host, $user, $pass, $db);
//connect2Databse($host, $user, $pass, $db);
$data = getFile('123.csv');
insertData($data, $host, $user, $pass, $db);
function getFile($file) {
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
	return $records;
}

function connect2Databse($host, $user, $pass, $db) {
	$charset = 'utf8';

	$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
	$opt = [
    	PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    	PDO::ATTR_EMULATE_PREPARES   => false,
	];
	$pdo = new PDO($dsn, $user, $pass, $opt);
	return $pdo;
}

function createTable($host, $user, $pass, $db) {
	$pdo = connect2Databse($host, $user, $pass, $db);
	$statement = "CREATE TABLE IF NOT EXISTS USERS (ID INT(11) AUTO_INCREMENT PRIMARY KEY, NAME VARCHAR(50) NOT NULL, SURNAME VARCHAR(50) NOT NULL, EMAIL VARCHAR(100) NOT NULL);";
	$table = $pdo->exec($statement);

	if ($table !== false) {
		echo "Succeed!!";
	}
	else {
		echo "Fail!";
	}
}	

function insertData($data, $host, $user, $pass, $db) {
	$pdo = connect2Databse($host, $user, $pass, $db);
	try {
     foreach($data as $record) {
		$sql = "INSERT INTO USERS (NAME, SURNAME, EMAIL) VALUES(:name, :surname, :email)";
		if (filter_var(trim($record[2]), FILTER_VALIDATE_EMAIL)) {
			$statement = $pdo->prepare($sql);
			$statement->execute(array(
			'name'    => ucfirst(trim($record[0])),
			'surname' => ucfirst(trim($record[1])),
			'email'   => strtolower(trim($record[2]))
			));
			echo "New records inserted successfully \r\n";
		}
		else {
			echo "$record[2] Email Invalid!! \r\n";
		}
	}
	} 
	catch (PDOException $e) {
	    if ($e->getCode() == 1062) {
	        // Take some action if there is a key constraint violation, i.e. duplicate name
	    } else {
	        throw $e;
	    }
	}
}
	
?>