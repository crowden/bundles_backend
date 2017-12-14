<?php 

namespace J29Bundle\Entity\crud;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * **ENTITY**
 *
 * @ORM\Entity
 * @ORM\Table(name="crud_###'s")
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
     * @ORM\Column(type="string", length=255)
     * @Assert\File(
     *     maxSize = "500k",
     *     mimeTypes = {
     *         "image/jpeg", 
     *         "image/png",
     *         "image/svg+xml"
     *     },
     *     binaryFormat = false,
     *     mimeTypesMessage = "Please upload only .jpg, .png, or .svg files"
     * )
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
     */
    private $fileDescription;

    /**
     * @Assert\Callback
     */
    public function validateFileDescription(ExecutionContextInterface $context){
        if(($this->getFile() !== null) && ($this->getFileDescription() === null)){
            $context->buildViolation('You must provide a value for the \'Alt Description\' field.')
                ->atPath('imageAlt')
                ->addViolation();
        }
    }
}