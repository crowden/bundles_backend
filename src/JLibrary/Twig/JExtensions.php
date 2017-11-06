<?php 

namespace JLibrary\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class JExtension extends \Twig_Extension
{
    protected $container;
    
    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function getFunctions(){
        return [
            new \Twig_SimpleFunction('jBlock', [$this, 'renderJBlock']),
        ];
    }

    public function renderJBlock($entity_namespace, Array $options = null){
        return $this->container->get('j29.blocker')->getBlockFor($entity_namespace, $options);
    }
}