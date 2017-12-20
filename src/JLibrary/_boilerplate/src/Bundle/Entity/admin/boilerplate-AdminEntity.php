<?php 

namespace J29Bundle\Entity\admin;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * **ENTITY**
 *
 * @ORM\Entity
 * @ORM\Table(name="admin_###'s")
 */

class **ENTITY**
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $published;
}