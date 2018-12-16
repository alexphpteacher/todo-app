<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AlbumRepository")
 */
class Album implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=50)
     */
    private $title;

    /**
     * @Assert\GreaterThan(0)
     * @ORM\Column(type="integer")
     */
    private $trackCount;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $releaseDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTrackCount(): ?int
    {
        return $this->trackCount;
    }

    public function setTrackCount(int $trackCount): self
    {
        $this->trackCount = $trackCount;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $utcDate = new \DateTime($this->getReleaseDate()->format(\DateTime::ATOM));
        $utcDate->setTimezone(new \DateTimeZone('UTC'));
        return [
            'id'           => $this->getId(),
            'title'        => $this->getTitle(),
            'release_date' => $utcDate->format(\DateTime::ATOM),
            'track_count'  => $this->trackCount,
        ];
    }
}
