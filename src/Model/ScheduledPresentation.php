<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Model;

use \JMS\Serializer\Annotation as JMS;

class ScheduledPresentation
{
    /**
     * @var int
     * @JMS\Type("int")
     */
    private $id = -1;

    /**
     * @var int
     * @JMS\Type("int")
     * @JMS\SerializedName("presentation")
     */
    private $presentationId = -1;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("screen")
     */
    private $screenGuid = '';

    /**
     * @var \DateTime|null
     * @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     */
    private $start;

    /**
     * @var \DateTime|null
     * @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     */
    private $end;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPresentationId(): int
    {
        return $this->presentationId;
    }

    public function setPresentationId(int $presentationId): void
    {
        $this->presentationId = $presentationId;
    }

    public function getScreenGuid(): string
    {
        return $this->screenGuid;
    }

    public function setScreenGuid(string $screenGuid): void
    {
        $this->screenGuid = $screenGuid;
    }

    public function getStart(): ?\DateTime
    {
        return $this->start;
    }

    public function setStart(?\DateTime $start): void
    {
        $this->start = $start;
    }

    public function getEnd(): ?\DateTime
    {
        return $this->end;
    }

    public function setEnd(?\DateTime $end): void
    {
        $this->end = $end;
    }
}
