<?php
/**
 * @author  brooke.bryan
 */

namespace Cubex\Nrpe;

class PerformanceMetric
{
  protected $_raw;

  protected $_label;
  protected $_unitOfMeasure;
  protected $_warning;
  protected $_critical;
  protected $_minimum;
  protected $_maximum;

  protected $_parsedRaw;

  public function __construct($raw)
  {
    $this->_raw = $raw;
    $this->_parseRaw();
  }

  public function __toString()
  {
    return $this->getUnitOfMeasure();
  }

  public function getLabel()
  {
    return $this->_label;
  }

  public function getUnitOfMeasure()
  {
    $this->_parseRaw();
    return $this->_unitOfMeasure;
  }

  public function getWarning()
  {
    $this->_parseRaw();
    return $this->_warning;
  }

  public function getCritical()
  {
    $this->_parseRaw();
    return $this->_critical;
  }

  public function getMinimum()
  {
    $this->_parseRaw();
    return $this->_minimum;
  }

  public function getMaximum()
  {
    $this->_parseRaw();
    return $this->_maximum;
  }

  protected function _parseRaw()
  {
    if($this->_parsedRaw)
    {
      return null;
    }
    /**
     * 'label'=value[UOM];[warn];[crit];[min];[max]
     *
     * Examples:
     * /=-2147483648B;-2147483648;;0;-2147483648
     * /dev/shm=0B;1060364278;;0;1060364288
     * /boot=108381184B;507744246;;0;507744256
     *
     * load1=0.000;15.000;30.000;0;
     * load5=0.000;10.000;25.000;0;
     * load15=0.000;5.000;20.000;0;
     */

    list($label, $proc) = explode("=", $this->_raw, 2);
    list($uom, $warning, $critical, $min, $max) = explode(";", $proc, 5);

    $this->_label         = str_replace("'", '', $label);
    $this->_unitOfMeasure = trim($uom);
    $this->_warning       = $warning;
    $this->_critical      = $critical;

    //Min/Max should default to %s when UOM defines
    if(substr($this->_unitOfMeasure, -1, 1) == '%')
    {
      $this->_minimum = 0;
      $this->_maximum = 100;
    }
    else
    {
      $this->_minimum = $min;
      $this->_maximum = $max;
    }

    $this->_parsedRaw = true;
  }
}
