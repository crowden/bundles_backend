<?php 

namespace JLibrary\Traits;

use JLibrary\Service\MarkdownParser;

trait DoctrineLifeCycleSanitizer
{
    private $md_parser;

    public function __construct(MarkdownParser $md_parser){
        $this->md_parser = $md_parser;
    }

    protected function sanitize(Array $options){
        foreach ($options as $key => $value){
            $field_value = call_user_func(array($this, 'get' . $key));

            // value is set to value that isn't NULL and not an empty value
            $user_submitted_value = isset($field_value) && !empty($field_value);
            // if the value is optional and empty, simply return
            if($value['optional'] === true && !$user_submitted_value) continue;

            switch($value['type']){
                case 'plain_text':
                    $refactored = $this->returnPlainText($field_value);
                    break;
                case 'url':
                    $refactored = $this->returnUrl($field_value);
                    break;
                case 'markdown_general':
                    $md_filtered = $this->returnPlainText($field_value);
                    $md_parsed = $this->returnMarkdownGeneral($md_filtered);

                    /*Replace h1 with h2*/
                    $md_filtered = preg_replace('/^# *(?=[a-zA-Z0-9].*$)/m', '## ', $md_filtered);
                    $md_parsed = preg_replace('/(?:(?<=<)|(?<=<\/))h1(?=>)/', 'h2', $md_parsed);

                    call_user_func(array($this, 'set' . $value['rawHandler']), $md_filtered);
                    call_user_func(array($this, 'set' . $value['htmlHandler']), $md_parsed);
                    
                    return true;
            }

            call_user_func(array($this, 'set' . $key), $refactored);
        }
    }

    protected function returnPlainText($value){
        if (is_array($value)){
            foreach($value as $key => $single){
                $value[$key] = strip_tags(trim($single));
            }

            return $value;
        }

        return strip_tags(trim($value));
    }

    protected function returnUrl($value){
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

    protected function returnMarkdownGeneral($value){
        // return markdown
        return $this->md_parser->parse_md_general($value);
    }
}