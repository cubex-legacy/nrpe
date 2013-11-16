<?php
/**
 * @author  brooke.bryan
 */

namespace Cubex\Nrpe\Commands;

interface INrpeCommand
{
  /**
   * Command to run e.g. check_nrpe
   *
   * @return string
   */
  public function getCommand();

  /**
   * Array of arguments to send to the command
   *
   * @return null|array|string
   */
  public function getArguments();
}
