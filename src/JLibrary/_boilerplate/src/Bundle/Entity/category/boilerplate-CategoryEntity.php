<?php 

namespace J29Bundle\Entity\category;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="category_entity")
 */

class Entity
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