<?php 

namespace Bundle\Twig;

use Doctrine\ORM\EntityManagerInterface;

class ClassName extends \Twig_Extension
{
    protected $entity_manager;
    
    public function __construct(EntityManagerInterface $entity_manager){
        $this->entity_manager = $entity_manager;
    }

    //////////////////////////////////////
    //             Filters              //
    //////////////////////////////////////

    public function getFilters(){
        return [
            new \Twig_SimpleFilter('phoneNumberLink', [$this, 'renderPhoneLink'], ['is_safe' => ['html']]),
        ];
    }

    public function renderPhoneLink($phone_number){
        return preg_replace('/[^\d]*/', '', $phone_number);
    }

    /////////////////////////////////////////
    //             Functions               //
    /////////////////////////////////////////

    public function getFunctions(){
        return [
            new \Twig_SimpleFunction('inTwig', [$this, 'method'], ['is_safe' => ['html'], 'needs_environment' => true,]),
        ];
    }

    public function getCategoryDocuments(\Twig_Environment $environment, Array $specs){
        $repo = $this->_getRepo($specs['entity_namespace']);
        
        $query = $repo->createQueryBuilder('**BASE_TABLE_ABBR**')
            ->where('**BASE_TABLE_ABBR**.prop AND **JOINT_TABLE_ABBR**.prop = :place_holder')
            ->innerJoin('**BASE_TABLE_ABBR**.prop', '**JOINT_TABLE_ABBR**')
            ->orderBy('**BASE_TABLE_ABBR**.prop', 'ASC')
            ->setParameter('place_holder', $specs['place_holder'])
            ->getQuery();

        return $this->_verifyResults($environment, $specs, $query);
    }

    //////////////////////////////////////////////////
    //             Utility Functions                //
    //////////////////////////////////////////////////


    private function _getRepo($namespace){
        return $this->entity_manager->getRepository($namespace);
    }

    private function _verifyResults($environment, $specs, $query){
        $results = $query->getResult();

        if (count($results) > 0){
            $template = $specs['template'];
            $build = [
                'results' => $results,
                'heading' => (null !== $specs['heading']) ? $specs['heading'] : false,
            ];
            
            return $environment->render($template, $build);
        } else {
            return null;
        }
    }
}