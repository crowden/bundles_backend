<?php 

namespace J29Bundle\Entity\**entity_type**;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * **Entity**
 *
 * @ORM\Entity
 * @ORM\Table(name="**entity_type**_**entities**s")
 */

class **Entity**
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



########      ###      #########
########   Templates   #########
########      ###      #########





//////////////////////////////
//         General          //
//////////////////////////////

/**
 * @ORM\Column(type="json_array")
 * @Assert\All({
 *     @Assert\###()
 * })
 * @Assert\NotNull()
 */
private $array_json_NOT_NULL('collection') = array('add an item');

/**
 * @ORM\Column(type="boolean", nullable=true)
 */
private $checkBox;
    
/**
 * @ORM\Column(type="string", length=255)
 * @Assert\Choice({"c1", "c2", "c3"})
 */
private $choices;

/**
 * @ORM\Column(type="datetime")
 * 
 * @Assert\Date()
 */
private $dateTime;

/**
 * @ORM\Column(type="string", length=255)
 * 
 * @Assert\Length(
 *     max=255, 
 *     maxMessage="This field can only contain 255 characters"
 * )
 * 
 * @Assert\Email(
 *     strict = true, 
 *     message = "The email '{{ value }}' does not appear to be a valid email."
 * )
 */
private $emailAddress;

/**
 * @ORM\Column(type="integer")
 * @Assert\Type(type="integer")
 */
private $integer;

/**
 * @ORM\Column(type="string", length=255)
 * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
 */
private $machineName;

/**
 * @ORM\Column(type="integer")
 */
private $order;

/**
 * @ORM\Column(type="string", length=14)
 * 
 * @Assert\Length(
 *     max=14,
 *     maxMessage="This field can only contain 14 characters"
 * )
 * 
 * @Assert\Regex(
 *     pattern="/^\(?\d{3}\)?[- \.]?\d{3}[- \.]?\d{4}$/",
 *     message="Please format phone/fax numbers as (###) ###-#### with no spaces at the beginning or end."
 * )
 */
private $phoneNumber;

/**
 * @ORM\Column(type="string", length=2)
 * 
 * @Assert\Length(
 *     max=2, 
 *     maxMessage="This field can only contain 2 characters. Please format states as 2 uppercase letters only."
 * )
 */
private $state;

/**
 * @ORM\Column(type="string", length=255)
 * @Assert\NotBlank()
 * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
 */
private $string;

/**
 * @ORM\Column(type="string", length=255, unique=true)
 * 
 * @Assert\Regex(
 *     pattern="/^[a-z0-9][-a-z0-9]*[a-z0-9]{1}$/",
 *     message="URL's can only be letters, numbers, and hyphens. Also, URL's can only start and finish with letters or numbers and must be between 2-255 characters in length."
 * )
 */
private $urlSlug;





///////////////////////////////
//         Blocks            //
///////////////////////////////

/**
 * @ORM\Column(name="id", type="string", length=255)
 * @ORM\Id
 * @ORM\GeneratedValue(strategy="NONE")
 */
private $id;

public function getId(){}
public function setId($id){}





///////////////////////////////////
//         Categories            //
///////////////////////////////////

/**
 * @ORM\Column(type="string", length=255)
 * @Assert\NotBlank()
 * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
 */
private $title;

/**
 * @ORM\Column(type="string", length=255)
 * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
 */
private $machineName;





/////////////////////////////
//         Joins           //
/////////////////////////////

use J29Bundle\Entity\category\EntityNotInCurrentDir;

/**
 * @ORM\ManyToOne(targetEntity="ENTITY_IN_CURRENT_DIR")
 * @ORM\JoinColumn(nullable=false)
 * @Assert\NotNull(message="value required")
 */
private $join_IN_DIR;

/**
 * @ORM\ManyToOne(targetEntity="\ENITY\NOT\IN_CURRENT\DIR")
 * @ORM\JoinColumn(nullable=false)
 * @Assert\NotNull(message="This value cannont be empty.")
 */
private $join_NOT_IN_DIR;

public function notNullEntityJoin(ENTITY $entity = null){}





////////////////////////////////
//         Markdown           //
////////////////////////////////

/**
 * @ORM\Column(type="text")
 */
private $contentHtml;

/**
 * @ORM\Column(type="text")
 * @Assert\NotBlank()
 */
private $contentRaw;





//////////////////////////////////////
//         Slug/Timestamp           //
//////////////////////////////////////

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\Slug(fields={"title"})
 * @ORM\Column(type="string")
 */
private $slug;

/**
 * @Gedmo\Timestampable(on="create")
 * @ORM\Column(type="datetime")
 */
private $createdAt;





/////////////////////////////////////
//         Files/Images            //
/////////////////////////////////////

/**
 * @ORM\Column(type="string", length=255)
 * @Assert\File(
 *     maxSize = "500k",
 *     mimeTypes = {
 *         "image/jpeg", 
 *         "image/png"
 *     },
 *     binaryFormat = false,
 *     mimeTypesMessage = "Please upload only .jpg and .png files"
 * )
 */
private $file;

/**
 * @ORM\Column(type="string", length=255)
 * @Assert\NotBlank()
 * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
 */
private $fileDescription;





/////////////////////////////////
//         Documents           //
/////////////////////////////////

use J29Bundle\Entity\category\DocumentCategory;

/**
 * @ORM\Column(type="string", length=255)
 * @Assert\NotBlank()
 * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
 */
private $documentName;

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
private $documentLinkText;





/////////////////////////////////
//         Callbacks           //
/////////////////////////////////

use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @Assert\Callback
 */
public function CALLBACK(ExecutionContextInterface $context){}