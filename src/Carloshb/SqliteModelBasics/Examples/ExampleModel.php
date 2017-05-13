<?php

namespace Carloshb\SqliteModelBasics\Examples;

use Carloshb\SqliteModelBasics\Model;

class ExampleModel extends Model {
    protected $table = "logs";
    /**
     * @param \SQLite3 $conn
     * @return bool
     */
    function createTable(\SQLite3 $conn) : bool
    {
        return $conn->exec('
            CREATE TABLE IF NOT EXISTS logs (
            id integer PRIMARY KEY AUTOINCREMENT,
            name varchar(100),
            content varchar,
            created_at timestamp
          )
        ');
    }
}