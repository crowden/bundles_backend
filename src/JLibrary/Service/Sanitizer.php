<?php 

namespace JLibrary\Service;

use cebe\markdown\Markdown;
use cebe\markdown\GithubMarkdown;
use cebe\markdown\MarkdownExtra;

class Sanitizer
{
    private $parser_general;
    private $parser_github;
    private $parser_extra;

    public function __construct(){
        $this->parser_general = new Markdown();
        $this->parser_github = new GithubMarkdown();
        $this->parser_extra = new MarkdownExtra();
    }

    /**
     * sanitize entity fields based on parameters
     * 
     * @param $options
     *        - type [string]
     *        - optional [boolean]
     *        - callback [string]
     */
    public function sanitize($entity, Array $options){
        foreach ($options as $key => $value){
            $field_value = call_user_func(array($entity, 'get' . $key));
            
            // value is set to value that isn't NULL and not an empty value
            $user_submitted_value = isset($field_value) && !empty($field_value);

            if($value['optional'] === true && !$user_submitted_value) continue;

            switch($value['type']){
                case 'plain_text':
                    $refactored = $this->returnPlainText($field_value);
                    break;
                case 'url':
                    $refactored = $this->returnUrl($field_value);
                    break;
                case 'url_validated':
                    $refactored = $this->returnUrlValidated($field_value);
                    break;
                case 'email_address':
                    $refactored = $this->returnEmailAddress($field_value);
                    break;
                case 'markdown_extra':
                    $refactored = $this->returnMarkdownExtra($field_value);
                    return true;
                case 'markdown_general':
                    $md_filtered = $this->returnPlainText($field_value);
                    $md_parsed = $this->returnMarkdownGeneral($md_filtered);

                    call_user_func(array($entity, 'set' . $value['rawHandler']), $md_filtered);
                    call_user_func(array($entity, 'set' . $value['htmlHandler']), $md_parsed);
                    
                    return true;
                case 'markdown_github':
                    $refactored = $this->returnMarkdownGithub($field_value);
                    return true;
            }

            call_user_func(array($entity, 'set' . $key), $refactored);
        }
    }

    private function returnEmailAddress($value){
        $validated = filter_var(trim($value), FILTER_VALIDATE_EMAIL);
        
        try {
            if ($validated === false) throw new Exception('Could not validate email address');

            return $validated;
        }
        // catch validation error
        catch (Exception $e){
            echo $e->getMessage();
        }
    }

    private function returnMarkdownExtra($value){
        return $this->parser_extra->parse($value);
    }

    private function returnMarkdownGeneral($value){
        return $this->parser_general->parse($value);
    }

    private function returnMarkdownGithub($value){
        return $this->parser_github->parse($value);
    }

    private function returnPlainText($value){
        return strip_tags(trim($value));
    }

    private function returnUrl($value){
        $sanitized = filter_var(trim($value), FILTER_SANITIZE_URL);

        try {
            if ($sanitized === false) throw new Exception('Problem handling URL');
            return $sanitized;
        }
        // catch sanitization error
        catch (Exception $e){
            echo $e->getMessage();
        }
    }

    private function returnUrlValidated($value){
        $sanitized = $this->returnUrl($value);
        $validated = filter_var($sanitized, FILTER_VALIDATE_URL);
                
        try {
            if ($validated === false) throw new Exception('Problem validating URL');
            return $validated;
        }
        catch (Exception $e){
            echo $e->getMessage();        
        }
    }
}