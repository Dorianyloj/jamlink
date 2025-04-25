<?php

namespace App\Entity;

use App\Repository\MusicGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MusicGroupRepository::class)]
class MusicGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 500)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column(length: 20)]
    private ?string $level = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column]
    private ?int $maxMembers = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'musicGroups')]
    private Collection $users;

    #[ORM\ManyToOne(inversedBy: 'leadingGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userLeader = null;

    /**
     * @var Collection<int, MusicStyle>
     */
    #[ORM\ManyToMany(targetEntity: MusicStyle::class, mappedBy: 'groups')]
    private Collection $musicStyles;

    /**
     * @var Collection<int, Advertisement>
     */
    #[ORM\OneToMany(targetEntity: Advertisement::class, mappedBy: 'creator')]
    private Collection $advertisements;

    /**
     * @var Collection<int, Media>
     */
    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'musicGroup')]
    private Collection $media;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->musicStyles = new ArrayCollection();
        $this->advertisements = new ArrayCollection();
        $this->media = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getMaxMembers(): ?int
    {
        return $this->maxMembers;
    }

    public function setMaxMembers(int $maxMembers): static
    {
        $this->maxMembers = $maxMembers;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addMusicGroup($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeMusicGroup($this);
        }

        return $this;
    }

    public function getUserLeader(): ?User
    {
        return $this->userLeader;
    }

    public function setUserLeader(?User $userLeader): static
    {
        $this->userLeader = $userLeader;

        return $this;
    }

    /**
     * @return Collection<int, MusicStyle>
     */
    public function getMusicStyles(): Collection
    {
        return $this->musicStyles;
    }

    public function addMusicStyle(MusicStyle $musicStyle): static
    {
        if (!$this->musicStyles->contains($musicStyle)) {
            $this->musicStyles->add($musicStyle);
            $musicStyle->addGroup($this);
        }

        return $this;
    }

    public function removeMusicStyle(MusicStyle $musicStyle): static
    {
        if ($this->musicStyles->removeElement($musicStyle)) {
            $musicStyle->removeGroup($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Advertisement>
     */
    public function getAdvertisements(): Collection
    {
        return $this->advertisements;
    }

    public function addAdvertisement(Advertisement $advertisement): static
    {
        if (!$this->advertisements->contains($advertisement)) {
            $this->advertisements->add($advertisement);
            $advertisement->setCreator($this);
        }

        return $this;
    }

    public function removeAdvertisement(Advertisement $advertisement): static
    {
        if ($this->advertisements->removeElement($advertisement)) {
            // set the owning side to null (unless already changed)
            if ($advertisement->getCreator() === $this) {
                $advertisement->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): static
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
            $medium->setMusicGroup($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): static
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getMusicGroup() === $this) {
                $medium->setMusicGroup(null);
            }
        }

        return $this;
    }
}
