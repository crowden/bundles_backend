<?php 

namespace JLibrary\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

class SingleFileManager
{
    /**
     * If upload directories don't exist, create them
     * @param Array $upload_directories [from config.yml]
     */
    public function __construct(Array $upload_directories){
        foreach ($upload_directories as $dir){
            if(!is_dir($dir)){
                mkdir($dir, 0755, true);
            }
        }
    }

    /**
     * Main entry point for file manager.
     *
     * @param $specs - array of options to direct program flow
     */
    public function manage(Array $specs){
        switch($specs['mode']){
            case 'upload':
                return $this->manageUpload($specs);
            case 'delete':
                return $this->manageDelete($specs);
            case 'toggle':
                return $this->manageToggle($specs);
            case 'update':
                return $this->manageUpdate($specs);
        }
    }

    /**
     * Checks value of input field. Does upload and returns filename if value is 
     * provided, or returns null|false based on 'required' value.
     * 
     * @return [string|null|false (error)]
     */
    private function manageUpload(Array $specs){
        $get_handler = 'get' . $specs['handler'];
        $set_handler = 'set' . $specs['handler'];
        
        $uploaded_file = call_user_func(array($specs['entity'], $get_handler));
        
        // Not NULL, return filename
        if (isset($uploaded_file)){
            $sha = md5(uniqid());
            $extension = $uploaded_file->guessExtension();

            // could extension be guessed?
            if (isset($extension)){
                // unique filename
                $filename = $sha . '.' . $extension; 

                // move file with new name
                $uploaded_file->move($specs['directory'], $filename); 

                // set filename on entity
                call_user_func(array($specs['entity'], $set_handler), $filename); 
                
                return $filename;
            } else {
                die($extension->getErrorMessage());
            }
        // field is NULL
        } else {
            if ($specs['required']) {
                return false;
            } else {
                return null;
            }
        }
    }

    /**
     * If entity has property value for file, then deletion is attempted and
     * any errors in deleting are caught.
     *
     * If no value is detected, function returns null.
     * 
     * @return [true|null]
     */
    private function manageDelete(Array $specs){
        $property_value = $this->getHandler($specs);

        if (isset($property_value)){
            try {
                // able to delete successfully
                unlink($specs['directory'] . '/' . $property_value);
                return true;
            // error in trying to delelte
            } catch (\Exception $e) {
                echo 'File deletion error: ' . $e->getMessage();die;
            }
        // there is no file to delete
        } else {
            return null;
        }
    }

    /**
     * Create file from given filename and directory. If property value exists, function 
     * returns filename prior to toggling.
     * 
     * @return [string|null]
     */
    private function manageToggle(Array $specs){
        $filename = $this->getHandler($specs);

        if (isset($filename)){
            $this->setHandler($specs, [new File($specs['directory'] . '/' . $filename)]);

            return $filename;
        } else {
            return null;
        }
    }

    /**
     * First checks if field has an initial value and if there was a previous file upload.
     * If there is a value and for the form field along with a previous file,
     * the previous file is unlinked and the new one is uploaded, otherwise the new file is 
     * simply uploaded. If the form field does NOT have a value and there was not a 
     * previous file, the the method simply returns null, otherwise a 'delete previous' 
     * check is ran to determine if old files should be deleted (if not required [error])
     * or if the previous file should simply be set again on the object's property
     * 
     * @return [true|null|string|false (error)]
     */
    private function manageUpdate(Array $specs){
        $field_has_value = $this->getHandler($specs);
        $previous_file = $specs['previous_file']; // [string|null]

        // New File Provided - TRUE
        if (isset($field_has_value)){
            // delete old file if present
            if (isset($previous_file)){
                unlink($specs['directory'] . '/' . $previous_file);
            }

            // do upload of new file
            return $this->manageUpload($specs);
        } 
        // New File Provided - FALSE
        else 
        {
            if (isset($previous_file)){
                // ERROR: this should never be possible
                $error = $specs['delete_file'] && $specs['required'];
                if ($error) return false;

                // Delete previous file
                if ($specs['delete_file'] && !$specs['required']){
                    unlink($specs['directory'] . '/' . $previous_file);
                    return true;
                }

                // has previous file and does NOT want to delete
                return $this->setHandler($specs, [$previous_file]);
            } else {
                // cannot reach if 'required'
                return null;
            }
        }
    }

    private function getHandler(Array $specs, $use_get = true){
        $get = $use_get ? 'get' : $use_get;
        $get_handler = $get . $specs['handler'];

        $result = call_user_func(array($specs['entity'], $get_handler));

        return $result;
    }

    private function setHandler(Array $specs, Array $value, $use_set = true){
        $set = $use_set ? 'set' : $use_set;
        $set_handler = $set . $specs['handler'];

        call_user_func_array(array($specs['entity'], $set_handler), $value);

        return true;
    }
}
