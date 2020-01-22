<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Repository;

use App\Model\Presentation;

interface PresentationRepositoryInterface
{
    public function get(int $id): Presentation;
}
