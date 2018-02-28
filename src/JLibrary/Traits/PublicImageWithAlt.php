<?php 

namespace JLibrary\Traits;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * PublicImageWithAlt [handles file uploading and setting image alt value]
 *     - accepts jpg, png, and svg images
 *     - assumes images are public and live in public_html
 *     - required properties are:
 *         - imageTemp
 *         - imageAlt
 *         - pathSet
 *         - pathTemp
 */

trait PublicImageWithAlt {

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
     */
    private $imageAlt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pathSet;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $modificationDate;

    /**
     * @Assert\File(
     *     maxSize = "500k",
     *     mimeTypes = {
     *         "image/jpeg", 
     *         "image/png",
     *         "image/svg+xml"
     *     },
     *     binaryFormat = false,
     *     mimeTypesMessage = "Please upload only .svg, .jpg and .png files"
     * )
     */
    private $imageTemp;
    
    /**
     * hold temporary reference for unlinking purposes
     */
    private $pathTemp;

    /**
     * @Assert\Type(type="boolean")
     */
    private $markedForDeletion = false;
    
    public function getImageAlt()
    {
        // return imageAlt
        return $this->imageAlt;
    }

    public function setImageAlt($imageAlt)
    {
        $this->imageAlt = $imageAlt;
        return $this;
    }

     public function getImageTemp()
    {
        // return imageTemp
        return $this->imageTemp;
    }

    /**
     * Set temporary image and handle path
     */
    public function setImageTemp(UploadedFile $image_temporary = null)
    {
        $this->imageTemp = $image_temporary;

        // check if there's an old image path
        if (isset($this->pathSet)) {
            // store the old name to delete after the update
            $this->pathTemp = $this->pathSet;
            $this->pathSet = null;
        } else {
            $this->pathSet = 'initial';
        }
    }

    public function getPathSet()
    {
        return null === $this->pathSet
            ? null
            : self::UPLOAD_DIR . '/' . $this->pathSet;
    }

    public function setPathSet($path_set)
    {
        $this->pathSet = $path_set;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    /**
     * @param mixed $modificationDate
     *
     * @return self
     */
    public function setModificationDate($modificationDate)
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }

    ////////////////////////////////////
    //            UTILITY             //
    ////////////////////////////////////


    /*
     * @ORM\PostLoad()
     */
    public function postEntityLoad(){
        $this->modificationDate = date_format(new \DateTime(), 'Y-m-d H:i:s');
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     *
     * If imageTemp is set, create filename for it and
     * set it on $pathSet
     */
    public function preUpload()
    {
        $this->modificationDate = date_format(new \DateTime(), 'Y-m-d H:i:s');
        // sanitize here so that this property is not forgotten 
        // when trait is included
        $this->sanitize([
            'imageAlt' => [
                'type' => 'plain_text',
                'optional' => true,
            ],
        ]);
        
        $uploaded_file = $this->getImageTemp();

        // user chose a file to upload
        if (null !== $uploaded_file) {
            $sha = md5(uniqid());
            $extension = $uploaded_file->guessExtension();

            if (isset($extension)){
                $this->pathSet = $sha . '.' . $extension; // unique filename
            } else {
                die($extension->getErrorMessage());
            }
        }

        // there is NOT a file chosen for upload and delete file == true
        if (null === $uploaded_file && $this->markedForDeletion){
            // will be null or absolute path to file
            $current_file = $this->getAbsolutePath();
            
            // if there is a current file, delete it
            if (null !== $current_file) unlink($current_file);
            
            // completely reset all other properties
            $this->pathTemp = null;
            $this->pathSet = null;
            $this->imageAlt = null;
            $this->markedForDeletion = false;
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        $uploaded_file = $this->getImageTemp();

        if (null !== $uploaded_file){
            // if there is an error in moving the file, this should prevent
            // persistance to the database
            $uploaded_file->move($this->getUploadRootDir(), $this->pathSet);

            // is there an old image to delete?
            if (isset($this->pathTemp)) {
                // delete old file
                unlink($this->getUploadRootDir() . '/' . $this->pathTemp);
                // clear the temp image path
                $this->pathTemp = null;
            }

            $this->imageTemp = null;
        }
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        // delete old file upon entity deletion
        if ($file) unlink($file);
    }
    
    protected function getAbsolutePath()
    {
        return null === $this->pathSet
            ? null
            : $this->getUploadRootDir() . '/' . $this->pathSet;
    }

    protected function getUploadRootDir()
    {
        $absolute_path = __DIR__ . '/../../../../../../public_html/' . self::UPLOAD_DIR;

        if(!is_dir($absolute_path)){
            mkdir($absolute_path, 0755, true);
        }
        
        return $absolute_path;
    }

    /**
     * @return mixed
     */
    public function getMarkedForDeletion()
    {
        return $this->markedForDeletion;
    }

    /**
     * @param mixed $markedForDeletion
     *
     * @return self
     */
    public function setMarkedForDeletion($markedForDeletion)
    {
        $this->markedForDeletion = $markedForDeletion;

        return $this;
    }
}