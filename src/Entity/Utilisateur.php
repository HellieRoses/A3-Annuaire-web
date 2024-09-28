<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_LOGIN', fields: ['login'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_CODE', fields: ['code'])]
#[UniqueEntity(fields: ['login'],message: 'Ce login est déjà pris')]
#[UniqueEntity(fields: ['email'],message: 'Cet email est déjà pris')]
#[UniqueEntity(fields: ['code'],message: 'ce code est déjà utilisé')]

#[ORM\HasLifecycleCallbacks]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private ?string $login = null;

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

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Email(message: "Email non valide")]
    private ?string $email = null;

    #[ORM\Column]
    private ?bool $visible = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(exactly: 8,exactMessage: 'Le code doit être de 8 caractères alphanumériques')]
    private ?string $code = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $first_name = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nationality = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $linkedin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profession = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastConnexion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastModification = null;

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }

    /**
     * @see UserInterface
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function isVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): static
    {
        $this->visible = $visible;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

   
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(?string $nationality): static
    {
        $this->nationality = $nationality;

        return $this;
    }

    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    public function setLinkedin(?string $linkedin): static
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(?string $profession): static
    {
        $this->profession = $profession;

        return $this;
    }
    public function addRole($role) : void {
        if(!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }
    public function removeRole($role) : void {
        $index = array_search($role, $this->roles);
        
        if ($index !== false) {
            unset($this->roles[$index]);
        }
    }

    public function getLastConnexion(): ?\DateTimeInterface
    {
        return $this->lastConnexion;
    }

    public function setLastConnexion(?\DateTimeInterface $lastConnexion): static
    {
        $this->lastConnexion = $lastConnexion;

        return $this;
    }

    #[ORM\PreUpdate]
    public function preUpdateDateModification(PreUpdateEventArgs $args): void
    {
        if(!$args->hasChangedField('lastConnexion')) {
            $this->lastModification = (new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        }
    }

    public function getLastModification(): ?\DateTimeInterface
    {
        return $this->lastModification;
    }

    public function setLastModification(?\DateTimeInterface $lastModification): static
    {
        $this->lastModification = $lastModification;

        return $this;
    }

    public function isAdmin() : bool
    {
        return in_array('ROLE_ADMIN', $this->roles);
    }


}
