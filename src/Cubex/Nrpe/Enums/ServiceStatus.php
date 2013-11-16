<?php
/**
 * @author  brooke.bryan
 */

namespace Cubex\Nrpe\Enums;

use Cubex\Type\Enum;

class ServiceStatus extends Enum
{
  const __default = 0;
  const OK        = 0;
  const WARNING   = 1;
  const CRITICAL  = 2;
  const UNKNOWN   = 3;
}
