<?php
namespace Utils;

/**
 * Class DB
 * @package Utils
 */
class DB {
	/**
	 * @var null
	 */
	protected static $instance = null;
	/**
	 * @var bool|PDO
	 */
	protected $pdo;
	/**
	 * @var string
	 */
	protected $query;
	/**
	 * @var int
	 */
	protected $queryCount = 0;
	/**
	 * @var mixed
	 */
	private $startTime;

	/**
	 * @param array $connectionData
	 */
	public function __construct(array $connectionData) {
		$this->startTime = microtime(true);
		$this->pdo = $this->connect($connectionData['host'], $connectionData['user'], $connectionData['password'], $connectionData['database']);
	}

	/**
	 * @param $host
	 * @param $username
	 * @param $password
	 * @param $database
	 * @return bool|PDO
	 */
	public function connect($host, $username, $password, $database) {
		$dsn = "mysql:host={$host};dbname={$database}";
		$options = array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
		try {
			return new \PDO($dsn, $username, $password, $options);
		} catch(\PDOException $e) {
			echo 'Cannot connect to the database: ' . $e->getMessage();
			return false;
		}
	}

	/**
	 * @param array $connection
	 * @return null|DB
	 */
	public static function getInstance(array $connection = null) {
		if (self::$instance === null) {
			self::$instance = new DB($connection);
		}
		return self::$instance;
	}

	/**
	 * @param $message
	 */
	public function log($message) {
		if (true) {
			error_log($message);
		}
	}

	/**
	 * @param $query
	 * @param array $params
	 * @return bool
	 */
	public function query($query, $params = array()) {
		$start = microtime(true);
		$statement = $this->pdo->prepare($query);
		$success = $statement->execute($params);
		$this->queryCount++;
		if($success){
			$this->log('QTime :' . ($start - microtime(true)) . ' ' . $query);
			return $statement;
		} else {
			$this->log("QFail - " . $query);
		}
		return false;
	}


	/**
	 * @param \PDOStatement $statement
	 * @return bool|mixed
	 */
	public function fetchRow(\PDOStatement $statement) {
		$value = $statement->fetch(\PDO::FETCH_ASSOC);
		if ($value) {
			return $value;
		}

		return false;
	}

	/**
	 * @return mixed
	 */
	public function getLastInsertId() {
		return $this->pdo->lastInsertId();
	}
}