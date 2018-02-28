<?php 

namespace J29Bundle\Entity\**entity_type**;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use JLibrary\Traits\DoctrineLifeCycleSanitizer;


/**
 * **Entity**
 *
 * @ORM\Entity
 * @ORM\Table(name="**entity_type**_**entities**s")
 * @ORM\HasLifecycleCallbacks()
 */
class **Entity**
{
    use DoctrineLifeCycleSanitizer;
    
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

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preSanitize(){
        // types include: plain_text, url, markdown_general
        $this->sanitize([
            'property' => [
                'type' => 'plain_text',
                'optional' => false,
            ],
            'contentRaw' => [
                'type' => 'markdown_general',
                'optional' => false,
                'rawHandler' => 'contentRaw',
                'htmlHandler' => 'contentHtml',
            ],
        ]);
    }
}



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
     * @ORM\Column(type="decimal", nullable=true, precision=20, scale=6)
     * @Assert\Type("numeric")
     */
    private $decimal;

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
    private $machineName(USE_TRAIT and SET_PREFIX_CONST);

    /**
     * @ORM\Column(type="decimal")
     * @Assert\Type("number")
     */
    private $number;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Choice(choices={"1","2","3","4","5","6","7","8","9","10",})
     */
    private $order;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
     */
    private $string_NOT_BLANK;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
     */
    private $string_NULLABLE;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
     * @Assert\Url()
     */
    private $urlAbsolute;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * 
     * @Assert\Regex(
     *     pattern="/^[a-z0-9][-a-z0-9]*[a-z0-9]{1}$/",
     *     message="URL's can only be letters, numbers, and hyphens. Also, URL's can only start and finish with letters or numbers and must be between 2-255 characters in length."
     * )
     */
    private $urlSlug;

    /**
     * @ORM\Column(type="smallint")
     */
    private $weight;

///////////////////////////////
//         Address           //
///////////////////////////////

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
     */
    private $address1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
     */
    private $address2;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=2)
     *
     * @Assert\NotBlank()
     * 
     * @Assert\Length(
     *     max=2, 
     *     maxMessage="This field can only contain 2 characters. Please format states as 2 uppercase letters only."
     * )
     */
    private $state;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     */
    private $zip;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(type="integer")
     */
    private $zipRegion;

//////////////////////////////////
//         Phone/Fax            //
//////////////////////////////////

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
    private $faxNumber;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     */
    private $phoneExtension;

////////////////////////////////////////
//         GeoCoordinates             //
////////////////////////////////////////

    /**
     * @ORM\Column(type="decimal", nullable=true, precision=20, scale=6)
     * @Assert\Type("numeric")
     */
    private $geoLat;

    /**
     * @ORM\Column(type="decimal", nullable=true, precision=20, scale=6)
     * @Assert\Type("numeric")
     */
    private $geoLong;


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
    
    /**
     * @ORM\ManyToOne(targetEntity="\")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="This value cannont be empty.")
     */
    private $joinManyToOne;

    /**
     * @ORM\OneToMany(targetEntity="\", mappedBy="prop", cascade={"persist"})
     * @Assert\Valid()
     */
    private $joinOneToMany;

    /**
     * @ORM\OneToMany(targetEntity="\", mappedBy="prop", cascade={"remove"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    private $joinOneToManyOrphanRemoval;

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
     * @Gedmo\Slug(fields={"slug"})
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
    public function validateTicketLinkText(ExecutionContextInterface $context){
        if(condition){
            $context->buildViolation('You must provide a value for this field.')
                ->atPath('property')
                ->addViolation();
        }
    }

/////////////////////////////////////
//         UniqueEntity            //
/////////////////////////////////////
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
// on class
/**
 * @UniqueEntity("service")
 */


/////////////////////////////////////
//         Collections             //
/////////////////////////////////////
    public function add***SINGLE***(***TypeHint*** $collection_single_element)
    {
        $this->**property**->add($collection_single_element);
        $collection_single_element->**inversedSetMethod**($this);

        return $this;
    }

    public function remove***SINGLE***(***TypeHint*** $collection_single_element)
    {
        if ($this->**property**->contains($collection_single_element)) {
            $this->**property**->removeElement($collection_single_element);
        }

        return $this;
    }