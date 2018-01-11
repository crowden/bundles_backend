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
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     *
     * If imageTemp is set, create filename for it and
     * set it on $pathSet
     */
    public function preUpload()
    {
        $uploaded_file = $this->getImageTemp();

        if (null !== $uploaded_file) {
            $sha = md5(uniqid());
            $extension = $uploaded_file->guessExtension();

            if (isset($extension)){
                $this->pathSet = $sha . '.' . $extension; // unique filename
            } else {
                die($extension->getErrorMessage());
            }
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        $uploaded_file = $this->getImageTemp();

        if (null === $uploaded_file) return;

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
        $absolute_path = __DIR__ . '/../../../../../public_html/' . self::UPLOAD_DIR;

        if(!is_dir($absolute_path)){
            mkdir($absolute_path, 0755, true);
        }
        
        return $absolute_path;
    }
}