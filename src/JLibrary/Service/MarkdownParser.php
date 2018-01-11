<?php 

namespace JLibrary\Service;

use cebe\markdown\Markdown;

class MarkdownParser
{
    private $parser_general;
    
    public function __construct(){
        $this->parser_general = new Markdown();
    }

    public function parse_md_general($value){
        return $this->parser_general->parse($value);
    }
}