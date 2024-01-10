<?php

class DatabaseConnection
{
    private $server = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database;
    private $connection;
    private $type;

    public function __construct()
    {
        $this->connect();
    }

    public function prepare($server, $username, $password, $database, $port = 3306)
    {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->port = $port;
        $this->connect();
    }

    private function connect()
    {
        if (function_exists('mysqli_connect')) {
            $this->connection = mysqli_connect($this->server, $this->username, $this->password, $this->database);

            if (!$this->connection) {
                die('Could not connect:' . mysqli_error());
            } else {
                $this->type = 'mysqli';
            }
        } elseif (function_exists('mysql_connect')) {
            $this->connection = mysql_connect($this->server, $this->username, $this->password);

            if (!$this->connection) {
                die('Could not connect:' . mysql_error());
            } else {
                $this->type = 'mysql';
            }
        }
    }

    public function close()
    {
        if ($this->type == 'mysqli') {
            $this->connection = mysqli_close($this->connection);
        } elseif ($this->type == 'mysql') {
            $this->connection = mysql_close($this->connection);
        }
    }

    public function query($query)
    {
        $this->log('query: '.$query.'<br>');

        if ($this->type == 'mysqli') {
            $response = mysqli_query($this->connection, $query);
            if (mysqli_error($this->connection)) {
                return mysqli_error($this->connection);
            } else {
                if ($response->num_rows > 0) {
                    $index = 0;
                    $result = array();
                    while ($row = $response->fetch_assoc()) {
                        $result[$index] = $row;
                        $index++;
                    }
                    return $result;
                }
                return $response;
            }
        } elseif ($this->type == 'mysql') {
            $response = $this->connection->query($query);
        } else {
            return;
        }
        return $response;
    }

    public function log($log = '')
    {
        echo $log;
        echo 'server: '.$this->server.'<br>';
        echo 'username: '.$this->username.'<br>';
        echo 'password: '.$this->password.'<br>';
        echo 'database: '.$this->database.'<br>';
        echo 'type: '.$this->type.'<br>';
        echo '<hr>';
    }
}
