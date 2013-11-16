<?php
/**
 * @author  brooke.bryan
 */

namespace Cubex\Nrpe;

use Cubex\Nrpe\Enums\ServiceStatus;

class NrpeResponse
{
  protected $_returnCode;
  protected $_rawResponse;

  protected $_parsedRaw;
  protected $_serviceOutput;
  protected $_servicePerfData;
  protected $_detailedServiceOutput;
  protected $_detailedServicePerfData;

  protected $_performanceMetrics;

  public function __construct($raw, $returnCode)
  {
    $this->_rawResponse = $raw;
    $this->_returnCode  = $returnCode;
  }

  public function __toString()
  {
    return $this->_rawResponse;
  }

  /**
   * @return ServiceStatus
   */
  public function getServiceStatus()
  {
    return ServiceStatus::fromValue($this->_returnCode);
  }

  public function getReturnCode()
  {
    return $this->_returnCode;
  }

  public function isOk()
  {
    return $this->_returnCode === 0;
  }

  public function isWarning()
  {
    return $this->_returnCode === 1;
  }

  public function isCritical()
  {
    return $this->_returnCode === 2;
  }

  public function getRawResponse()
  {
    return $this->_rawResponse;
  }

  protected function _parseRaw()
  {
    if($this->_parsedRaw)
    {
      return null;
    }

    /**
     * SERVICE STATUS: First line of output | First part of performance data
     * Any number of subsequent lines of output, but note that buffers
     * may have a limited size | Second part of performance data, which
     * may have continuation lines, too
     */
    list($summary, $detail) = explode("\n", $this->_rawResponse, 2);
    list($serviceOutput, $perfData) = explode("|", $summary, 2);
    list($longOut, $longPerfData) = explode("|", $detail, 2);

    $this->_serviceOutput           = $serviceOutput;
    $this->_servicePerfData         = $perfData;
    $this->_detailedServiceOutput   = $longOut;
    $this->_detailedServicePerfData = $longPerfData;

    $this->_parsedRaw = true;
  }

  public function getServiceOutput()
  {
    $this->_parseRaw();
    return $this->_serviceOutput;
  }

  public function getPerformanceData()
  {
    $this->_parseRaw();
    return $this->_servicePerfData;
  }

  public function getDetailedServiceOutput()
  {
    $this->_parseRaw();
    return $this->_detailedServiceOutput;
  }

  public function getDetailedPerformanceData()
  {
    $this->_parseRaw();
    return $this->_detailedServicePerfData;
  }

  /**
   * @param bool $keyed return metrics in a keyed array by metric label
   *
   * @return PerformanceMetric[]
   */
  public function getPerformanceMetrics($keyed = false)
  {
    $this->_parseRaw();
    if($this->_performanceMetrics === null)
    {
      $parts = explode(
        " ",
        ($this->_servicePerfData . " " . $this->_detailedServicePerfData)
      );
      foreach($parts as $perfRaw)
      {
        $perfRaw = trim($perfRaw);
        if(!empty($perfRaw))
        {
          if(!$keyed)
          {
            $this->_performanceMetrics[] = new PerformanceMetric($perfRaw);
          }
          else
          {
            $metric = new PerformanceMetric($perfRaw);
            //Store the label
            if($metric->getLabel() !== null)
            {
              $this->_performanceMetrics[$metric->getLabel()] = $metric;
            }
          }
        }
      }
    }
    return $this->_performanceMetrics;
  }
}
