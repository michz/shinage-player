<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Synchronization;

interface ScheduleSynchronizerInterface
{
    public function synchronize(\DateTime $from, \DateTime $to): void;
}
