<?php

namespace Carloshb\SqliteModelBasics;

use Carloshb\SqliteModelBasics\Contract\ModelContract;

abstract class Model implements ModelContract
{
    use SQLiteDriver;
    protected $table = "";
    protected $condition = null;

    /**
     * @param \SQLite3 $conn
     * @return bool
     */
    abstract function createTable(\SQLite3 $conn) : bool;

    public function __construct()
    {
        if (method_exists($this, 'createTable')):
            $this->connect();
            $this->createTable($this->getConn());
            $this->disconnect();
        endif;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return bool|array
     */
    public function get()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE " .$this->condition['field'] . " " . $this->condition['operation'] . " :content LIMIT 1;";
        $this->connect();
        $stmt = $this->getConn()->prepare($query);
        $stmt->bindValue(':content', $this->condition['content']);
        $response = $stmt->execute();
        $result = $response->fetchArray(SQLITE3_ASSOC);
        $this->disconnect();
        if(count($result) >= 1)
            return $result;
        else
            return false;
    }

    /**
     * @param array $data = ["field" => "name", "operation" => "LIKE", "content" => "Carlos"]
     * @return ModelContract
     */
    public function where(array $data): ModelContract
    {
        $this->condition = $data;
        return $this;
    }

    /**
     * @param int $id
     * @return array
     */
    public function find(int $id): array
    {
        $this->connect();
        $statement = $this->getConn()->prepare('SELECT * FROM '.$this->table.' WHERE id = :id;');
        $statement->bindValue(':id', $id);
        $result = $statement->execute();
        $response = $result->fetchArray(SQLITE3_ASSOC);
        $this->disconnect();
        return $response;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function save(array $data): bool
    {
        $keys = "";
        $values = "";
        foreach ($data as $key => $value):
            $keys .= empty($keys) ? "`". $key . "`" : ", `" . $key. "`";
            $values .= empty($values) ? "'". $value . "'" : ", '". $value . "'";
        endforeach;
        $query = "INSERT INTO {$this->table} ({$keys}) VALUES ({$values})";
        $this->connect();
        $created = $this->getConn()->exec($query);
        $this->disconnect();
        return $created;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function update(array $data): bool
    {
        if ($this->condition === null) return false;
        $dataQuery = "";
        $values = array();
        foreach ($data as $key => $value):
            $values = array_merge($values, [":".$key => $value]);
            $dataQuery .= empty($dataQuery) ? "`{$key}` = :{$key}" : ", `{$key}` = :{$key}";
        endforeach;
        $query = "UPDATE ". $this->table . " SET " .$dataQuery . " WHERE `" . $this->condition['field'] . "` " . $this->condition['operation'] . " :search";
        $this->connect();
        $stmt = $this->getConn()->prepare($query);
        $stmt->bindValue(':search', $this->condition['content']);
        foreach ($values as $key => $value):
            $stmt->bindValue($key, $value);
        endforeach;
        $result = $stmt->execute();
        $this->disconnect();
        return ($result) ? true : false;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function destroy(int $id): bool
    {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $this->connect();
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $destroyed = $stmt->execute();
        return ($destroyed) ? true : false;
    }
}