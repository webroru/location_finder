<?php

declare(strict_types=1);

namespace Drupal\location_finder\API\Dhl\Entity;

class OpeningHours
{
    private string $opens;
    private string $closes;
    private string $dayOfWeek;

    public function getOpens(): string
    {
        return $this->opens;
    }

    public function setOpens(string $opens): self
    {
        $this->opens = $opens;
        return $this;
    }

    public function getCloses(): string
    {
        return $this->closes;
    }

    public function setCloses(string $closes): self
    {
        $this->closes = $closes;
        return $this;
    }

    public function getDayOfWeek(): string
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(string $dayOfWeek): self
    {
        $this->dayOfWeek = $dayOfWeek;
        return $this;
    }
}
