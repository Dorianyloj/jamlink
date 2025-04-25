<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $experience = null;

    #[ORM\Column(length: 20)]
    private ?string $level = null;

    #[ORM\Column(length: 200)]
    private ?string $location = null;

    #[ORM\Column(length: 50)]
    private ?string $firstname = null;

    #[ORM\Column(length: 50)]
    private ?string $lastname = null;

    /**
     * @var Collection<int, Instrument>
     */
    #[ORM\ManyToMany(targetEntity: Instrument::class, mappedBy: 'users')]
    private Collection $instruments;

    /**
     * @var Collection<int, MusicGroup>
     */
    #[ORM\ManyToMany(targetEntity: MusicGroup::class, inversedBy: 'users')]
    private Collection $musicGroups;

    /**
     * @var Collection<int, MusicGroup>
     */
    #[ORM\OneToMany(targetEntity: MusicGroup::class, mappedBy: 'userLeader')]
    private Collection $leadingGroups;

    /**
     * @var Collection<int, Media>
     */
    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'uploadedBy')]
    private Collection $media;

    public function __construct()
    {
        $this->instruments = new ArrayCollection();
        $this->musicGroups = new ArrayCollection();
        $this->leadingGroups = new ArrayCollection();
        $this->media = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function setExperience(string $experience): static
    {
        $this->experience = $experience;

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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection<int, Instrument>
     */
    public function getInstruments(): Collection
    {
        return $this->instruments;
    }

    public function addInstrument(Instrument $instrument): static
    {
        if (!$this->instruments->contains($instrument)) {
            $this->instruments->add($instrument);
            $instrument->addUser($this);
        }

        return $this;
    }

    public function removeInstrument(Instrument $instrument): static
    {
        if ($this->instruments->removeElement($instrument)) {
            $instrument->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, MusicGroup>
     */
    public function getMusicGroups(): Collection
    {
        return $this->musicGroups;
    }

    public function addMusicGroup(MusicGroup $musicGroup): static
    {
        if (!$this->musicGroups->contains($musicGroup)) {
            $this->musicGroups->add($musicGroup);
        }

        return $this;
    }

    public function removeMusicGroup(MusicGroup $musicGroup): static
    {
        $this->musicGroups->removeElement($musicGroup);

        return $this;
    }

    /**
     * @return Collection<int, MusicGroup>
     */
    public function getLeadingGroups(): Collection
    {
        return $this->leadingGroups;
    }

    public function addLeadingGroup(MusicGroup $leadingGroup): static
    {
        if (!$this->leadingGroups->contains($leadingGroup)) {
            $this->leadingGroups->add($leadingGroup);
            $leadingGroup->setUserLeader($this);
        }

        return $this;
    }

    public function removeLeadingGroup(MusicGroup $leadingGroup): static
    {
        if ($this->leadingGroups->removeElement($leadingGroup)) {
            // set the owning side to null (unless already changed)
            if ($leadingGroup->getUserLeader() === $this) {
                $leadingGroup->setUserLeader(null);
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
            $medium->setUploadedBy($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): static
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getUploadedBy() === $this) {
                $medium->setUploadedBy(null);
            }
        }

        return $this;
    }
}
