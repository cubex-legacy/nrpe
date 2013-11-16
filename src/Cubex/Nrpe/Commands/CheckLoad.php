<?php
/**
 * @author  brooke.bryan
 */

namespace Cubex\Nrpe\Commands;

class CheckLoad implements INrpeCommand
{
  protected $_critical = [2, 2, 2];
  protected $_warning = [1, 1, 1];
  protected $_perCpu;

  public function setDivideByCpu($enabled = false)
  {
    $this->_perCpu = $enabled;
    return $this;
  }

  public function setCritical($load1, $load5, $load15)
  {
    $this->_critical = [$load1, $load5, $load15];
    return $this;
  }

  public function setWarning($load1, $load5, $load15)
  {
    $this->_warning = [$load1, $load5, $load15];
    return $this;
  }

  /**
   * @return string
   */
  public function getCommand()
  {
    return "check_load";
  }

  /**
   * @return null|array|string
   */
  public function getArguments()
  {
    $args = [];

    if($this->_perCpu)
    {
      $args[] = '--percpu';
    }

    $args[] = '--warning=' . implode(",", $this->_warning);
    $args[] = '--critical=' . implode(",", $this->_critical);

    return $args;
  }
}
