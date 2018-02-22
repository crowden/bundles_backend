<?php 

namespace J29Bundle\Controller\**ENTITY_TYPE**;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use J29Bundle\Entity\**ENTITY_TYPE**\**ENTITY**;
use J29Bundle\Form\**ENTITY_TYPE**\**ENTITY**Type;

use JLibrary\Traits\ControllerTraits;

class **ENTITY**Controller extends Controller
{
    use ControllerTraits;
    
    const ENTITY_NAMESPACE = 'J29Bundle:**ENTITY_TYPE**\**ENTITY**';

    private $template_vars = array(
        'form_size' => '###',
        'page_description' => 'Admin Page',
    );

    /**
     * @Route("/###", name="j29.**ENTITY_TYPE**.**entity_lowercase**.**action_type**")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction(Request $request){
        $entity = new **Entity**();
        $entity_manager = $this->getDoctrine()->getManager();

        // form creation
        $form = $this->createForm(**Entity**Type::class, $entity);
        $form->handleRequest($request);

        // form submission
        if ($form->isValid()){
            // save entity
            $entity_manager->persist($entity);
            $entity_manager->flush();

            return $this->redirectToRoute(self::ROUTE_INDEX);
        } else {
            if ($form->isSubmitted()) $this->addFlash('warning', 'You have errors with your form.');
        }

        // template data
        $build = array_merge([
            'creating_entity' => true,
            'page_title' => 'New **TITLE_ENTITY_SINGLE**',
            'form' => $form->createView(),
        ], $this->template_vars);

        return $this->render(self::TMPL_ACTION, $build);
    }
}