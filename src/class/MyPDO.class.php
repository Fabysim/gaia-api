<?php


class MyPDO extends PDO
{
    private $driver = 'mysql';
    private $host = '127.0.0.1';
    private $db = 'gaia';
    private $user = 'root';
    private $password = '';
    private $port = '3306';
    private $charset = 'utf8mb4';
    private $online = false;

    private $options = [
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode="TRADITIONAL"',
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];


    public function __construct()
    {

        $bdd = null;
        $dsn = "$this->driver:host=$this->host;dbname=$this->db; port=$this->port; charset=$this->charset";

        //$dsn = "$this->driver:host=$this->host;dbname=$this->db; charset=$this->charset";

        try {
            //$bdd = new PDO("mysql:host=$this->host;dbname=$this->db;charset=$this->charset", $this->user, $this->password);
            $bdd = parent::__construct($dsn, $this->user, $this->password, $this->options);



            return $bdd;

        } catch (Exception $e) {
            if ($this->online === false) {
                die('Erreur : ' . $e->getMessage());
            } else {
                die('Erreur avec la BD contacter le support technique...');
            }
        }
    }
}