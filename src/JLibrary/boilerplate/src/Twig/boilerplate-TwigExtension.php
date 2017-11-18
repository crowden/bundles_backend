<?php 

namespace Bundle\Twig;

use Service\That\You\Need;

class ClassName extends \Twig_Extension
{
    protected $service;
    
    public function __construct(Need $service){
        $this->service = $service;
    }

    public function getFunctions(){
        return [
            new \Twig_SimpleFunction('inTwig', [$this, 'method']),
        ];
    }

    public function method($entity_namespace, Array $options = null){
        return $this->service->getBlockFor($entity_namespace, $options);
    }
}