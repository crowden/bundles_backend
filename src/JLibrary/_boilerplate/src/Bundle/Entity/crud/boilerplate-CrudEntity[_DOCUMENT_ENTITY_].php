<?php 

namespace J29Bundle\Entity\crud;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

use J29Bundle\Entity\category\DocumentCategory;

/**
 * Document entity
 *
 * @ORM\Entity
 * @ORM\Table(name="crud_documents")
 */
class Document
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
     *     maxSize = "2500k",
     *     mimeTypes = {
     *         "application/pdf",
     *         "application/x-pdf",
     *         "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
     *         "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
     *         "application/msword",
     *         "application/vnd.ms-excel"
     *     },
     *     binaryFormat = false,
     *     mimeTypesMessage = "Allowed file types are '.pdf', '.docx', '.doc', '.xls', and '.xlsx'."
     * )
     */
    private $document;
    
    /**
     * @ORM\ManyToOne(targetEntity="\J29Bundle\Entity\category\DocumentCategory")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="This value cannont be empty.")
     */
    private $documentCategory;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
     * @Assert\NotBlank()
     */
    private $documentDescription;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
     */
    private $documentName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
     */
    private $linkText;
    
    /**
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published;

    /**
     * @Assert\Callback
     */
    public function validateDocumentDescription(ExecutionContextInterface $context){
        if(($this->getDocument() !== null) && ($this->getDocumentDescription() === null)){
            $context->buildViolation('You must provide a value for the \'Document Description\' field.')
                ->atPath('documentDescription')
                ->addViolation();
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @param string $document
     *
     * @return self
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDocumentCategory()
    {
        return $this->documentCategory;
    }

    /**
     * @param mixed $documentCategory
     *
     * @return self
     */
    public function setDocumentCategory(DocumentCategory $documentCategory = null)
    {
        $this->documentCategory = $documentCategory;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDocumentName()
    {
        return $this->documentName;
    }

    /**
     * @param mixed $documentName
     *
     * @return self
     */
    public function setDocumentName($documentName)
    {
        $this->documentName = $documentName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLinkText()
    {
        return $this->linkText;
    }

    /**
     * @param mixed $linkText
     *
     * @return self
     */
    public function setLinkText($linkText)
    {
        $this->linkText = $linkText;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLinkTitle()
    {
        return $this->linkTitle;
    }

    /**
     * @param mixed $linkTitle
     *
     * @return self
     */
    public function setLinkTitle($linkTitle)
    {
        $this->linkTitle = $linkTitle;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param mixed $published
     *
     * @return self
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDocumentDescription()
    {
        return $this->documentDescription;
    }

    /**
     * @param mixed $documentDescription
     *
     * @return self
     */
    public function setDocumentDescription($documentDescription)
    {
        $this->documentDescription = $documentDescription;

        return $this;
    }
}