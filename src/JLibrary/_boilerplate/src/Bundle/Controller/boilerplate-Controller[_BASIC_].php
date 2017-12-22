<?php 

namespace J29Bundle\Controller\**ENTITY_TYPE**;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class _***_Controller extends Controller
{
    /**
     * @Route("/###", name="j29.**ENTITY_TYPE**.###")
     */
    public function ###Action(Request $request){
        $build = array(
            'page_title' => '###',
            'page_description' => '###',
        );

        return $this->render('J29Bundle:**ENTITY_TYPE**:###.html.twig', $build);
    }
}