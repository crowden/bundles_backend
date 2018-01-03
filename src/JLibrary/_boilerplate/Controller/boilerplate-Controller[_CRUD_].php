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
    const ROUTE_DELETE = 'j29.**ENTITY_TYPE**.***single_entity***.delete';

    /**
     * types include:
     *     - plain_text
     *     - url
     *     - url_validated
     *     - email_address
     *     - markdown_general
     */
    private $sanitize_options = array(
        'EntityPrivateProperty' => [
            'type' => 'plain_text',
            'optional' => false,
        ],
    );

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
            'page_title' => '**TITLE_ENTITITEIS_PLURAL**',
            'entities' => $entity_manager->getRepository(self::ENTITY_NAMESPACE)->findAll(),
        ], $this->template_vars);

        return $this->render(self::TMPL_INDEX, $build);
    }

    /**
     * @Route("/new", name="j29.**ENTITY_TYPE**.***single_entity***.new")
     */
    public function newAction(Request $request){
        $entity = new **Entity**();

        // form creation
        $form = $this->createForm(**Entity**Type::class, $entity);
        $form->handleRequest($request);

        // form submission
        if ($form->isValid()){
            // sanitize, persist, and redirect
            $this->sanitizeAndPersist($entity, 'create');
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
        $delete_form = $this->renderDeleteForm($entity);
        $form = $this->createForm(**Entity**Type::class, $entity);
        
        $form->handleRequest($request);

        if ($form->isValid()){
            // sanitize, persist, and redirect
            $this->sanitizeAndPersist($entity, 'edit');
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
        // form creation
        $form = $this->renderDeleteForm($entity);
        $form->handleRequest($request);
    
        // form submission
        if ($form->isValid()) {
            /*$allowed_to_delete = $this->entityDeletionAllowed($entity, 'entity_namespace', 'entity_join_property');*/
            
            if (true){
                $this->addFlash('success', 'Item successfully deleted');
                $this->sanitizeAndPersist($entity, 'delete');
            } else {
                $this->addFlash('danger', 'Cannont delete item: <strong><em>' . $entity->getTitle() . '</em></strong>');
                $this->addFlash('warning', 'There are items that are using what you\'re trying to delete. If you want to delete this item, first go and disconnect it from the other items it is linked to. <br><br><strong>NOTE:</strong> Deleting this item could cause other areas of this site to break!');
                return $this->redirectToRoute(self::ROUTE_EDIT, ['id' => $entity->getId()]);
            }

        }
    
        return $this->redirectToRoute(self::ROUTE_INDEX);
    }

    /**
     * @Route("/sort/{sort_by}/{order}", defaults={"order" = "asc"})
     */
    public function sort(Request $request, $sort_by, $order){
        $build_variables = [
            'page_title' => '**TITLE_ENTITITEIS_PLURAL**',
            'page_description' => 'Admin Page',
        ];

        return $this->sortEntities($request, $sort_by, $order, $build_variables);
    }
}