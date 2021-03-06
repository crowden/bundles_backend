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
     * sanitize entity fields based on parameters.
     *
     * @todo  Refactor other entities and controllers to refrain from using this to validate
     * fields such as URL and email. Instead, rely on Symfony constraints to be the first level 
     * of defense. If form is www-facing, for example, and greater validation is needed for say a
     * URL, explicitly use PHP filter_var(value, FILTER_VALIDATE_URL) along with regex to make sure 
     * user is explicitly giving a URL. In CMS, rely on user error being caught by constraints.
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
                    $refactored = $this->returnEmailAddressValidated($field_value);
                    break;
                case 'markdown_extra':
                    $refactored = $this->returnMarkdownExtra($field_value);
                    return true;
                case 'markdown_general':
                    $md_filtered = $this->returnPlainText($field_value);
                    $md_parsed = $this->returnMarkdownGeneral($md_filtered);

                    /*Replace h1 with h2*/
                    $md_filtered = preg_replace('/^# *(?=[a-zA-Z0-9].*$)/m', '## ', $md_filtered);
                    $md_parsed = preg_replace('/(?:(?<=<)|(?<=<\/))h1(?=>)/', 'h2', $md_parsed);

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

    private function returnEmailAddressValidated($value){
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
        // return markdown
        return $this->parser_extra->parse($value);
    }

    private function returnMarkdownGeneral($value){
        // return markdown
        return $this->parser_general->parse($value);
    }

    private function returnMarkdownGithub($value){
        // return markdown
        return $this->parser_github->parse($value);
    }

    private function returnPlainText($value){
        if (is_array($value)){
            foreach($value as $key => $single){
                $value[$key] = strip_tags(trim($single));
            }

            return $value;
        }

        return strip_tags(trim($value));
    }

    private function returnUrl($value){
        $value_no_tags = $this->returnPlainText($value);
        $sanitized = filter_var(trim($value_no_tags), FILTER_SANITIZE_URL);

        try {
            if ($sanitized === false){
                throw new Exception('Problem handling URL');
            } else {
                return $sanitized;
            }
        }
        // catch sanitization error
        catch (Exception $e){
            echo $e->getMessage();        
            return 'ERROR: Link Not Valid';
        }
    }

    private function returnUrlValidated($value){
        $value_no_tags = $this->returnPlainText($value);
        $sanitized = $this->returnUrl($value_no_tags);
        $validated = filter_var($sanitized, FILTER_VALIDATE_URL);
                
        try {
            if ($validated === false){
                throw new \Exception('Problem validating URL');
            } else {
                return $validated;
            }
        }
        catch (\Exception $e){
            echo $e->getMessage();        
            return 'ERROR: Link Not Valid';
        }
    }
}