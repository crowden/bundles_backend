<?php 

namespace J29Bundle\Controller\**ENTITY_TYPE**;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use J29Bundle\Entity\**ENTITY_TYPE**\**Entity**;
use J29Bundle\Form\**ENTITY_TYPE**\**Entity**Type;
use JLibrary\Traits\ControllerTraits;

/**
 * **Entity** **ENTITY_TYPE** controller
 * @Route("/admin/entities")
 */
class **Entity**Controller extends Controller
{
    use ControllerTraits;
    
    const ENTITY_NAMESPACE = 'J29Bundle:**ENTITY_TYPE**\**Entity**';

    const TMPL_INDEX = 'J29Bundle:**ENTITY_TYPE**:**ENTITY_TYPE**-index-**single-entity**.html.twig';
    const TMPL_ACTION = 'J29Bundle:**ENTITY_TYPE**:**ENTITY_TYPE**-action-**single-entity**.html.twig';

    const ROUTE_INDEX = 'j29.**ENTITY_TYPE**.***single_entity***.index';
    const ROUTE_EDIT = 'j29.**ENTITY_TYPE**.***single_entity***.edit';
    const ROUTE_DELETE = 'j29.**ENTITY_TYPE**.***single_entity***.delete';

    private $template_vars = array(
        'form_size' => '###',
        'page_description' => 'Admin Page',
    );

    /**
     * @Route("/", name="j29.**ENTITY_TYPE**.***single_entity***.index")
     * @Method("GET")
     */
    public function indexAction(){
        $entity_manager = $this->getDoctrine()->getManager();

        // template data
        $build = array_merge([
            'page_title' => '**TITLE_ENTITITES_PLURAL**',
            'entities' => $entity_manager->getRepository(self::ENTITY_NAMESPACE)->findAll(),
        ], $this->template_vars);

        return $this->render(self::TMPL_INDEX, $build);
    }

    /**
     * @Route("/new", name="j29.**ENTITY_TYPE**.***single_entity***.new")
     */
    public function newAction(Request $request){
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

    /**
     * @Route("/{id}/edit", name="j29.**ENTITY_TYPE**.***single_entity***.edit", requirements={"id" = "\d+"})
     */
    public function editAction(Request $request, **Entity** $entity){
        $entity_manager = $this->getDoctrine()->getManager();
        $delete_form = $this->renderDeleteForm($entity);
        $form = $this->createForm(**Entity**Type::class, $entity);
        
        $form->handleRequest($request);

        if ($form->isValid()){
            // update entity
            $entity_manager->flush();
            
            return $this->redirectToRoute(self::ROUTE_INDEX);
        } else {
            if ($form->isSubmitted()) $this->addFlash('warning', 'You have errors with your form.');
        }

        // template data
        $build = array_merge([
            'creating_entity' => false,
            'page_title' => 'Edit **TITLE_ENTITY_SINGLE**',
            'form' => $form->createView(),
            'delete_form' => $delete_form->createView(),
        ], $this->template_vars);

        return $this->render(self::TMPL_ACTION, $build);
    }

    /**
     * @Route("/{id}", name="j29.**ENTITY_TYPE**.***single_entity***.delete", requirements={"id" = "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, **Entity** $entity){
        $entity_manager = $this->getDoctrine()->getManager();
        // form creation
        $form = $this->renderDeleteForm($entity);
        $form->handleRequest($request);

        $entity_name = $entity->getName();
    
        // form submission
        if ($form->isValid()) {
            try {
                $entity_manager->remove($entity);
                $entity_manager->flush();
                $this->addFlash('success', 'Item successfully deleted');

                return $this->redirectToRoute(self::ROUTE_INDEX);
            } catch (\Doctrine\DBAL\DBALException $e) {
                $this->addFlash('danger', 'Cannont delete item: <strong><em>' . $entity_name . '</em></strong>');
                $this->addFlash('warning', 'There are items that are using what you\'re trying to delete. If you want to delete this item, first go and disconnect it from the other items it is linked to. <br><br><strong>NOTE:</strong> Deleting this item could cause other areas of this site to break!');

                return $this->redirectToRoute(self::ROUTE_EDIT, ['id' => $entity->getId()]);
            }
        }
    }

    /**
     * @Route("/sort/{sort_by}/{order}", defaults={"order" = "asc"})
     */
    public function sort(Request $request, $sort_by, $order){
        $build_variables = [
            'page_title' => '**TITLE_ENTITITES_PLURAL**',
            'page_description' => 'Admin Page',
        ];

        return $this->sortEntities($request, $sort_by, $order, $build_variables);
    }
}