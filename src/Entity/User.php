<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *   fields={"pseudo"},
 *   message = "Ce pseudo est déjà utilisé, veuillez en choisir un autre"
 * )
 * @ORM\HasLifecycleCallbacks
*/
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ ne doit pas être vide")
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ ne doit pas être vide")
     * @Assert\Email(message = "'{{ value }}' n'est pas une adresse valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url( message = "'{{ value }}' n'est pas une url valide", normalizer = "trim")
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=8, minMessage="Le mot de passe doit comporter au moins 8 caractères")
     */
    private $hash;

    /**
	* @Assert\EqualTo(propertyPath="hash", message="Les mots de passe ne sont pas identiques")
	*/
    public $passwordConfirm;
        
    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=8, minMessage="Ce champ doit comporter au moins 50 caractères")
     */
    private $introduction;

    /**
     * @ORM\OneToMany(targetEntity=Recipe::class, mappedBy="author")
     */
    private $recipes;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, mappedBy="users")
     */
    private $userRoles;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
    }

    
    /**
     * initialise le slug s'il n'existe pas
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * 
     * @return void
     */
    public function initSlug() {
        if( empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->pseudo);
        }
    }

    
    public function getFullName() {
        return "{$this->firstName} {$this->lastName}";
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }
    
    /* 
    * ajouter pour implémenter la classe UserInterface
    */
    // public function getRoles() {
    //     return ['ROLE_USER'];
    // }

      // retourne l'ensemble des rôles d'un utilisateur
    public function getRoles() {
        // récupérer les rôles sauvegardés dans la bdd
        $roles = $this->userRoles->map(function($role) {
            return $role->getTitle();
        })->toArray();
        // ajouter le rôle ROLE_USER
        $roles[] = 'ROLE_USER';
        return $roles;
    }

    public function getPassword() {
        return $this->hash;	// hash contient le mot de passe encodé
    }
    public function getSalt() {}   	// inutile avec bcrypt

    public function getUsername() {
        return $this->pseudo;	// l'identifiant de connexion
    }

    public function eraseCredentials() {}	// utile si stockage de données sensibles (p.ex. mdp)


    /**
     * @return Collection|Recipe[]
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(Recipe $recipe): self
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes[] = $recipe;
            $recipe->setAuthor($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): self
    {
        if ($this->recipes->contains($recipe)) {
            $this->recipes->removeElement($recipe);
            // set the owning side to null (unless already changed)
            if ($recipe->getAuthor() === $this) {
                $recipe->setAuthor(null);
            }
        }

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Role $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
            $userRole->addUser($this);
        }

        return $this;
    }

    public function removeUserRole(Role $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
            $userRole->removeUser($this);
        }

        return $this;
    }
}
