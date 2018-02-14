<?php 

namespace JLibrary\Traits;

use cebe\markdown\Markdown;

trait DoctrineLifeCycleSanitizer
{
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
        // whether value is array or not, everthing is checked for tags
        $value_no_tags = $this->returnPlainText($value);
        
        // value is array
        if (is_array($value_no_tags)){
            try {
                foreach($value_no_tags as $key => $single){
                    $value_no_tags[$key] = filter_var($single, FILTER_SANITIZE_URL);
                    
                    // throw exception if can sanitized URL
                    if ($value_no_tags[$key] === false) throw new \Exception('Problem handling URL');
                }
                // return sanitized array
                return $value_no_tags;
            } catch (\Exception $e){
                /*echo $e->getMessage();*/
                return 'ERROR: Link Not Valid';
            }
        // value is scalar
        } else {
            $sanitized = filter_var($value_no_tags, FILTER_SANITIZE_URL);

            try {
                if ($sanitized === false){
                    throw new \Exception('Problem handling URL');
                } else {
                    return $sanitized;
                }
            }
            // catch sanitization error
            catch (\Exception $e){
                /*echo $e->getMessage();*/
                return 'ERROR: Link Not Valid';
            }
        }
    }

    protected function returnMarkdownGeneral($value){
        /**
         * @todo Use dependency injection with Doctrine Event Subcriber
         * on PreUpdate/Prepersist
         */
        $parser = new Markdown();
        $parsed_data = $parser->parse($value);
        $parser = null;

        return $parsed_data;
    }
}