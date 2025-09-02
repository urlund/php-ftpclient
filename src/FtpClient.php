<?php

namespace Urlund\FtpClient;

use \FTP\Connection;

class FtpClient
{
    /**
     * The FTP connection resource
     * 
     * @var Connection
     */
    protected $conn;

    /**
     * Create a new FTP client instance
     *
     * @param string $host
     * @param string $username
     * @param string $password
     */
    public function __construct($host, $username, $password)
    {
        $this->conn = ftp_connect($host);
        if (!$this->conn) {
            throw new \Exception("Could not connect to FTP server");
        }

        if (!@ftp_login($this->conn, $username, $password)) {
            throw new \Exception("Could not log in to FTP server");
        }
    }

    /**
     * Call FTP functions dynamically
     *
     * @param string $function
     * @param array $arguments
     * @return mixed
     */
    public function __call($function, array $arguments = [])
    {
        // support aliases for some functions
        $aliases = [
            'list'     => 'nlist',
            'modified' => 'mdtm',
        ];

        // check if the function is an alias
        if (array_key_exists($function, $aliases)) {
            $function = $aliases[$function];
        }

        // check if the function exists
        $function = 'ftp_' . $function;
        if (function_exists($function)) {
            array_unshift($arguments, $this->conn);
            return @call_user_func_array($function, $arguments);
        }

        throw new \Exception("{$function} is not a valid FTP function");
    }
}
