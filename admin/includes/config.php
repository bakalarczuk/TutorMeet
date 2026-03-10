<?php

/* Database credentials. Assuming you are running MySQL
  server with default setting (user 'root' with no password) */
define('DB_SERVER', 'localhost'); // sql.tutormeet.nazwa.pl
define('DB_USERNAME', 'root'); // tutormeet_db
define('DB_PASSWORD', 'root'); //
define('DB_NAME', 'myelab'); // tutormeet_db


class Database
{

  public function connect()
  {
    /* Attempt to connect to MySQL database */
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Check connection
    if ($link === false) {
      die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    return $link;
  }
}
