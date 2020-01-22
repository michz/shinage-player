<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Repository;

use App\Model\ScheduledPresentation;

interface ScheduleRepositoryInterface
{
    /**
     * @return array|ScheduledPresentation[]
     */
    public function getScheduledPresentations(): array;

    public function getScheduledPresentationAt(\DateTime $dateTime): ?ScheduledPresentation;
}
