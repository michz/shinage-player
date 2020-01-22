<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Model;

use \JMS\Serializer\Annotation as JMS;

class Presentation
{
    /**
     * @var int
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @var string
     * @JMS\Type("string")
     */
    protected $title = 'Presentation';

    /**
     * @var string
     * @JMS\Type("string")
     */
    protected $notes = '';

    /**
     * @var \DateTime
     * @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     */
    protected $lastModified;

    /**
     * @var string
     * @JMS\Type("string")
     */
    protected $type = '';

    /**
     * @var string
     */
    protected $renderedPresentation = '';

    public function __construct()
    {
        $this->lastModified = new \DateTime();
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setNotes(string $notes): void
    {
        $this->notes = $notes;
    }

    public function getNotes(): string
    {
        return $this->notes;
    }

    public function getLastModified(): \DateTime
    {
        return $this->lastModified;
    }

    public function setLastModified(\DateTime $lastModified): void
    {
        $this->lastModified = $lastModified;
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getRenderedPresentation(): string
    {
        return $this->renderedPresentation;
    }

    public function setRenderedPresentation(string $renderedPresentation): void
    {
        $this->renderedPresentation = $renderedPresentation;
    }
}
