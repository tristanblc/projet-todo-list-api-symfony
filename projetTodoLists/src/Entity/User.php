<?php

namespace App\Entity;

use App\Entity\TodoList;
use App\Controller\LoginAction;
use App\Controller\LogoutAction;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;

use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email")
 * @ApiResource(
 *   normalizationContext  = {"groups" = {"user:read"}},
 *   denormalizationContext = {"groups" = {"user:write"}},
 *    itemOperations ={
 *        "get" ={
 *              "security" = "is_granted('ROLE_ADMIN')",
 *             "denormalization_context" = {"groups" = {"admin:read"}}
 *        }        
 *     },
 *    collectionOperations={ 
 *          "get" = {
 *                "security" = "is_granted('ROLE_ADMIN')",
 *                "denormalization_context" = {"groups" = {"user:read"}}
 *           },
 *          "post" ={
 *             "method" = "POST",
 *             "security" = "is_granted('ROLE_ADMIN')",
 *             "denormalization_context" = {"groups" = {"user:write"}},
 *             "validation_groups" = {"create"}
 *           },
 *          "logout" = {
 *             "method" = "POST",
 *             "deserialize" = false,
 *             "path" = "/logout",
 *             "controller" = LogoutAction::class,
 *             "denormalization_context" = {"groups" = {"user:logout"}},
 *             "read"=true
 *          }
 * }
 *  
 * );
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("admin:read")
     * @Groups("user:read")
     */
    private $id;

    /** 
     *
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user:read", "user:write","admin:read"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups("admin:read")
     * @Groups("user:read")
     * 
     */
    private $roles = [];

     /**
     * @Assert\Regex(
     *  pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *  message="Mot de passe pas assez fort"
     * ),
     * @var string The hashed password
     * @SerializedName("password")
     * @ORM\Column(type="string")
     * 
     */
    private $password;

     /**
     * 
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="todoLists")
     * 
     */
    private $users;
  

    /**
     * @ORM\ManyToMany(targetEntity=TodoList::class, inversedBy="users")
     *  * @Groups("user:read")
     * 
     */
    private $todolists;

    /**
     * @SerializedName("password")
     * @Assert\Regex(
     *  pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *  message="Mot de passe pas assez fort"
     * ),
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("user:write")
     */
    private $plainpassword;




    public function __construct()
    {
        $this->todolists = new ArrayCollection();
        $this->todoLists = new ArrayCollection();
        $this->users = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getTodoList(): ?TodoList
    {
        return $this->todoList;
    }

    public function setTodoList(?TodoList $todoList): self
    {
        $this->todoList = $todoList;

        return $this;
    }



    public function setTodolists(?TodoList $todoLists): self
    {
        $this->todoLists = $todoLists;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getTodoLists(): Collection
    {
        return $this->todolists;
    }

    public function addTodoList(TodoList $todoList): self
    {
        if (!$this->todolists->contains($todoList)) {
            $this->todolists->add($todoList);
        }

        return $this;
    }

    public function removeTodoList(TodoList $todoList): self
    {
        $this->todolists->removeElement($todoList);

        return $this;
    }

 
  



    /**
     * Get pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     */ 
    public function getPlainpassword()
    {
        return $this->plainpassword;
    }

    /**
     * Set pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *
     * @return  self
     */ 
    public function setPlainpassword($plainpassword)
    {
        $this->plainpassword = $plainpassword;

        return $this;
    }

    /**
     * Get the value of users
     */ 
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set the value of users
     *
     * @return  self
     */ 
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }
}