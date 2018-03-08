<?php 

namespace JLibrary\Traits;

trait HoneyPot
{
    protected function checkHoneyPot($honeypot_field){
        // user should not be able to set, so possibly malicious
        if (strlen($honeypot_field->getData()) > 0) return true;
        // honeypot not set
        return false;
    }
}