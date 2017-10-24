<?php 

namespace JLibrary\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

class SingleFileManager
{
    /**
     * Main job of the constructor is to make sure that all the upload 
     * directories exist. If they don't, they are created at runtime.
     */
    public function __construct(Array $upload_directories){
        foreach ($upload_directories as $dir){
            if(!is_dir($dir)){
                mkdir($dir, 0755, true);
            }
        }
    }

    /**
     * unlink(delete) file from given directory
     */
    public function deleteFile($entity, $directory, $handler){
        unlink($directory . call_user_func(array($entity, 'get' . $handler)));
    }

    /**
     * Used to determine if a file was provided by the user.
     */
    public function fileIsNotNull($entity, $handler){
        $input_value = call_user_func(array($entity, 'get' . $handler));
        return isset($input_value) ? $input_value : false;
    }

    public function handleNewFileUpload($entity, $current_filename, $directory, $handler){
        $new_field_value = call_user_func(array($entity, 'get' . $handler));

        if ($new_field_value !== NULL){
            unlink($directory . $current_filename);
            $this->upload($entity, $new_field_value, $directory, $handler);
        } else {
            call_user_func(array($entity, 'set' . $handler), $current_filename);
        }
    }

    /**
     * create new file from filename
     *
     * @return [string] [current filename]
     */
    public function toggleFileAndFilename($entity, $directory, $handler){
        $filename = call_user_func(array($entity, 'get' . $handler));
        
        call_user_func_array(
            array($entity, 'set' . $handler),
            array(new File($directory . $filename))
        );

        return $filename;
    }

    /**
     * move file to upload directory and set filename to sha value plus extension.
     * 
     * @return filename
     */
    public function upload($entity, $uploaded_file, $directory, $handler){
        $sha = md5(uniqid());
        $extension = $uploaded_file->guessExtension();

        if (isset($extension)){
            $filename = $sha . '.' . $extension;
            $uploaded_file->move($directory, $filename);
            call_user_func(array($entity, 'set' . $handler), $filename);
        } else {
            die($extension->getErrorMessage());
        }
    }
}