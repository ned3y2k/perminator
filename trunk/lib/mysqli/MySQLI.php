<?php
namespace lib\mysqli;

class MySQLI extends \mysqli {
	public function __construct($host = null, $username = null, $passwd = null, $dbname = null, $port = null, $socket = null) {
		$host = is_null($host) ? ini_get ( "mysqli.default_hos" ) : $host;
		$username = is_null($username) ? ini_get ( "mysqli.default_user" ) : $username;
		$passwd = is_null($passwd) ? ini_get ( "mysqli.default_pw" ) : $passwd;
		$dbname = is_null($dbname) ? "" : $dbname;
		$port = is_null($port) ? ini_get ( "mysqli.default_port" ) : $port;
		$socket = is_null($socket) ? ini_get ( "mysqli.default_socket" ) : $socket;
		parent::mysqli ($host, $username, $passwd, $dbname, $port, $socket);
		if($this->connect_errno)
			throw new \mysqli_sql_exception($this->connect_error, $this->connect_errno);
	}
	public function prepare($query) {
		return new MySQLIStatement ( $this, $query );
	}
}