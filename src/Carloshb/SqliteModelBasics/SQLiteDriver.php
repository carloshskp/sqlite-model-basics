<?php

namespace Carloshb\SqliteModelBasics;

trait SQLiteDriver
{
    protected $conn = null;

    /**
     * Open connection
     */
    protected function connect(){
        $path = getenv('sqlite_path') ?? '/tmp';
        $this->conn = new \SQLite3(realpath($path) . '/database.sqlite');
    }

    /**
     * @return \SQLite3|null
     */
    protected function getConn(){
        return $this->conn;
    }

    /**
     * Close connection
     */
    protected function disconnect(){
        if (null !== $this->conn)
            $this->conn->close();
    }
}