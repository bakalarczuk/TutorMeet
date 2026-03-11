<?php

/* Database credentials - use environment variables in production */
define('DB_SERVER', getenv('DB_SERVER') ?: 'localhost');
define('DB_USERNAME', getenv('DB_USERNAME') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'root');
define('DB_NAME', getenv('DB_NAME') ?: 'myelab');


class Database
{

  public function connect()
  {
    /* Attempt to connect to MySQL database */
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Check connection
    if ($link === false) {
      error_log("DB connection failed: " . mysqli_connect_error());
      die("ERROR: Could not connect to database.");
    }
    return $link;
  }
}
