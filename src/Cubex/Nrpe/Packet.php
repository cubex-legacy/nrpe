<?php

namespace Cubex\Nrpe;

class Packet
{
  protected $_body;

  public function __construct($body)
  {
    $this->_body = $body;
  }

  public function setBody($body)
  {
    $this->_body = $body;
    return $this;
  }

  public function getBytes()
  {
    $data = $this->_body;
    for($i = strlen($data); $i < 1024; $i++)
    {
      $data .= "\x00";
    }
    $data .= "SR";
    $res    = pack("n", 2324);
    $packet = "\x00\x02\x00\x01";
    $crc    = crc32($packet . "\x00\x00\x00\x00" . $res . $data);
    $packet .= pack("N", $crc) . $res . $data;

    return $packet;
  }

  public function __toString()
  {
    return $this->_body;
  }
}
