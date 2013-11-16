<?php
/**
 * @author  brooke.bryan
 */

namespace Cubex\Nrpe\Commands;

class CheckDummy implements INrpeCommand
{
  protected $_text;
  protected $_state;

  public function __construct($state)
  {
    $this->setState($state);
  }

  /**
   * @param $text string
   *
   * @return $this
   */
  public function setText($text)
  {
    $this->_text = $text;
    return $this;
  }

  /**
   * @param $state int
   *
   * @return $this
   */
  public function setState($state)
  {
    $this->_state = $state;
    return $this;
  }

  /**
   * Command to run e.g. check_nrpe
   *
   * @return string
   */
  public function getCommand()
  {
    return "check_dummy";
  }

  /**
   * Array of arguments to send to the command
   *
   * @return null|array|string
   */
  public function getArguments()
  {
    $args = [$this->_state];
    if(isset($this->_text))
    {
      $args[] = '"' . $this->_text . '"';
    }
    return $args;
  }
}
