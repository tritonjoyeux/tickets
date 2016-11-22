<?php


namespace UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use TicketsBundle\Entity\Messages;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="TicketsBundle\Entity\Messages", mappedBy="user")
     */
    private $messages;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Add message
     *
     * @param \TicketsBundle\Entity\Messages $message
     *
     * @return User
     */
    public function addMessage(\TicketsBundle\Entity\Messages $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Remove message
     *
     * @param \TicketsBundle\Entity\Messages $message
     */
    public function removeMessage(\TicketsBundle\Entity\Messages $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
