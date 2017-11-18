<?php 

namespace J29Bundle\Controller\admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{
    /**
     * @Route("/", name="j29.admin.index")
     */
    public function indexAction(Request $request){
        $build = array(
            'page_title' => '###',
            'page_description' => '###',
        );

        return $this->render('J29Bundle:admin:index.html.twig', $build);
    }
}