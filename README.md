**Prior the script**

  **1. Install php 7.1**
  $ sudo apt-get install python-software-propertie  s
  $ sudo add-apt-repository ppa:ondrej/php
  $ sudo apt-get update
  $ sudo apt-get install -y php7.1

  **2. Install Mysql**
  $ sudo apt-get update
  $ sudo apt-get install mysql-server

  **3. Install PHP-MySQL extension for Linux**
  sudo apt install php7.1-mysql

**The PHP script will include these command line options (directives):**

•**--file [csv file name]:**
This is the name of the CSV to be parsed and it will display the content of this file. 

The format of --file command should be:

 **--file[filename.csv]**

•**--create_table:**
This will cause the MySQL users table to be built (and no further action will be taken).

The format of --create_table should be:

 **--create_table -u -username -p -password -h -host**

•**--dry_run:**
This will be used with the read content of file and create table. If the users accept the promopt, then it will insert the data into database.

The format of this command should be:

 **--dry_run --file[filename] -u -username -p -password -h -host**

•**--help:**
This will output the above list of directives with details.
