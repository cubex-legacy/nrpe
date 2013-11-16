<?php

namespace Cubex\Nrpe;

use Cubex\Nrpe\Commands\INrpeCommand;

/**
 * NRPE - Nagios Remote Plugin Executor
 *
 * @package Cubex\Nrpe
 */
class NrpeCommander
{
  protected $_host;
  protected $_port = 5666;
  protected $_timeout = 20;
  protected $_connection;
  protected $_sslEnabled = true;
  protected $_lastReturnCode = null;

  public function __construct($host = null, $port = 5666, $timeout = 20)
  {
    $this->_host    = $host;
    $this->_port    = $port;
    $this->_timeout = $timeout;
  }

  public function setTimeout($timeout)
  {
    $this->_timeout = $timeout;
    return $this;
  }

  public function enableSsl()
  {
    $this->_sslEnabled = true;
    return $this;
  }

  public function disableSsl()
  {
    $this->_sslEnabled = false;
    return $this;
  }

  public function sendCommand(INrpeCommand $command, $disconnect = true)
  {
    return $this->sendRawCommand(
      $command->getCommand(),
      $command->getArguments(),
      $disconnect
    );
  }

  public function sendRawCommand($command, $args = null, $disconnect = true)
  {
    if($args !== null)
    {
      $command .= '!' . implode(' ', $args);
    }

    $raw = $this->sendPacket(new Packet($command), $disconnect);

    return new NrpeResponse($raw, $this->_lastReturnCode);
  }

  public function getLastReturnCode()
  {
    return $this->_lastReturnCode;
  }

  public function sendPacket(Packet $packet, $disconnect = true)
  {
    $connection = $this->_getConnection();
    $bytes      = $packet->getBytes();

    fputs($connection, $bytes, strlen($bytes));
    $result = fread($connection, 8192);

    if($result === false)
    {
      throw new \Exception("Error receiving data from nrpe agent");
    }
    else if(strlen($result) === 0)
    {
      throw new \Exception("Received 0 bytes from nrpe agent");
    }

    $returnCode            = unpack('n', substr($result, 8, 2));
    $this->_lastReturnCode = $returnCode[1];

    if($disconnect)
    {
      $this->disconnect();
    }

    return trim(substr($result, 10, -2));
  }

  protected function _connect($host = null, $port = null)
  {
    if($host !== null)
    {
      $this->_host = $host;
    }
    if($port !== null)
    {
      $this->_port = $port;
    }

    $context = stream_context_create();

    if($this->_sslEnabled)
    {
      stream_context_set_option($context, 'ssl', 'ciphers', 'ADH');
    }

    $this->_connection = stream_socket_client(
      sprintf(
        '%s://%s:%d',
        $this->_sslEnabled ? 'ssl' : 'tcp',
        $this->_host,
        $this->_port
      ),
      $errorNumber,
      $errorMessage,
      $this->_timeout,
      STREAM_CLIENT_CONNECT,
      $context
    );

    if(!$this->_connection)
    {
      throw new \Exception(
        "Connection failed to nrpe agent: $errorMessage ($errorNumber)"
      );
    }
  }

  protected function _getConnection()
  {
    if($this->_connection === null)
    {
      $this->_connect();
    }
    return $this->_connection;
  }

  public function disconnect()
  {
    if(is_resource($this->_connection))
    {
      fclose($this->_connection);
      $this->_connection = null;
    }
    return $this;
  }

  public function __destruct()
  {
    $this->disconnect();
  }
}
