<?php
/**
 * MySQL DB class using PDO
 */

class DB
{
    public $pdo;
    public $error;

    protected $host;
    protected $user;
    protected $pass;
    protected $dbname;
    protected $stmt;
    protected $options;

    public function __construct(array $options = array())
    {
        require_once NT_CONFIG_PATH.DIRECTORY_SEPARATOR.'db.php';

        $this->options = array();

        switch (DB_ERROR_MODE) {
            case 'SILENT':
                $this->options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_SILENT;
                break;
            case 'WARNING':
                $this->options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_WARNING;
                break;
            default:
                $this->options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;
                break;
        }

        if (!empty($options))
            $this->options = array_merge($this->options, $options);

        $this->host   = DB_HOST;
        $this->user   = DB_USER;
        $this->pass   = DB_PASS;
        $this->dbname = DB_NAME;

        try {
            $this->pdo = new \PDO('mysql:host='.$this->host.';dbname='.$this->dbname.';charset=utf8', $this->user, $this->pass, $this->options);
        } catch (\PDOException $e) {
            $this->error = 'Connection failed : ' . $e->getMessage();
        }
    }

    public function prepare(string $query)
    {
        try {
            $this->stmt = $this->pdo->prepare($query);
        } catch (\PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function bindValue($placeholder, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = \PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = \PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = \PDO::PARAM_NULL;
                    break;
                default:
                    $type = \PDO::PARAM_STR;
                    break;
            }
        }

        $this->stmt->bindValue($placeholder, $value, $type);
    }

    public function bindValueArray(array $params)
    {
        if (!empty($params)) {
            foreach ($params as $key => $val) {
                $this->bindValue($key, $val);
            }
        }
    }

    public function exec(string $query)
    {
        try {
            $affected = $this->pdo->exec($query);

            if ($affected === false) {
                $error = $this->pdo->errorInfo();
                $this->error = $error[0] . ' ' . $error[2];
                return false;
            }

            return $affected;
        } catch (\PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function execute(array $param = array())
    {
        if (is_array($param) && !empty($param)) {
            foreach($param as $key => $val) {
                $this->bindValue($key, $val);
            }
        }

        try {
            $result = $this->stmt->execute();

            if ($result === false) {
                $this->errorInfo();
                return false;
            }

            return true;
        } catch (\PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function query(string $query, $fetchMode = PDO::FETCH_ASSOC)
    {
        try {
            $result = $this->pdo->query($query, $fetchMode);

            if ($result === false) {
                $error = $this->pdo->errorInfo();
                $this->error = $error[0] . ' ' . $error[2];
                return false;
            }

            return $result;
        } catch (\PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function fetchAll($args = \PDO::FETCH_ASSOC)
    {
        return $this->stmt->fetchAll($args);
    }

    public function fetch($args = \PDO::FETCH_ASSOC)
    {
        return $this->stmt->fetch($args);
    }

    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    public function fetchColumn(int $index = 0)
    {
        return $this->stmt->fetchColumn($index);
    }

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    public function debugDumpParams()
    {
        return $this->stmt->debugDumpParams();
    }

    public function errorInfo()
    {
        $error = $this->stmt->errorInfo();
        $this->error = $error[0] . ' ' . $error[2];
    }

    public function close()
    {
        $this->pdo = null;
    }

    public function __call($method, $args)
    {
        if (method_exists($this->pdo, $method)) {
            return call_user_func_array(array(&$this->pdo, $method), $args);
        } else if (method_exists($this->stmt, $method)) {
            return call_user_func_array(array(&$this->stmt, $method), $args);
        } else {
            return 'Method \'' . $method . '\' not exists';
        }
    }
}