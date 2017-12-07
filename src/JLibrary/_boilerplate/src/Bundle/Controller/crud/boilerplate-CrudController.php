<?php 

namespace J29Bundle\Controller\crud;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use J29Bundle\Entity\crud\**Entity**;
use J29Bundle\Form\crud\**Entity**Type;
use JLibrary\Traits\ControllerTraits;

/**
 * **Entity** crud controller
 * @Route("/admin/entities")
 */
class **Entity**Controller extends Controller
{
    use ControllerTraits;
    
    const ENTITY_NAMESPACE = 'J29Bundle:crud\**Entity**';

    const TMPL_INDEX = 'J29Bundle:crud:crud-index-**entity.html.twig';
    const TMPL_ACTION = 'J29Bundle:crud:crud-action-**entity.html.twig';

    const ROUTE_INDEX = 'j29.crud.entity_*.index';
    const ROUTE_DELETE = 'j29.crud.entity_*.delete';

    /**
     * types include:
     *     - plain_text
     *     - url
     *     - url_validated
     *     - email_address
     *     - markdown_general
     */
    private $sanitize_options = array(
        'PrivateProperty' => [
            'type' => 'plain_text',
            'optional' => false,
        ]
    );

    private $template_vars = array(
        'form_size' => '###',
        'page_description' => 'Admin Page',
    );

    /**
     * @Route("/", name="j29.crud.entity_*.index")
     * @Method("GET")
     */
    public function indexAction(){
        $entity_manager = $this->getDoctrine()->getManager();

        // template data
        $build = array_merge([
            'page_title' => '###',
            'entities' => $entity_manager->getRepository(self::ENTITY_NAMESPACE)->findAll(),
        ], $this->template_vars);

        return $this->render(self::TMPL_INDEX, $build);
    }

    /**
     * @Route("/new", name="j29.crud.entity_*.new")
     */
    public function newAction(Request $request){
        $entity = new **Entity--();

        // form creation
        $form = $this->createForm(**Entity--Type::class, $entity);
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
            'page_title' => '###',
            'form' => $form->createView(),
        ], $this->template_vars);

        return $this->render(self::TMPL_ACTION, $build);
    }

    /**
     * @Route("/{id}/edit", name="j29.crud.entity_*.edit", requirements={"id" = "\d+"})
     */
    public function editAction(Request $request, **Entity-- $entity){
        $delete_form = $this->renderDeleteForm($entity);
        $form = $this->createForm(**Entity--Type::class, $entity);
        
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
            'page_title' => '###',
            'form' => $form->createView(),
            'delete_form' => $delete_form->createView(),
        ], $this->template_vars);

        return $this->render(self::TMPL_ACTION, $build);
    }

    /**
     * @Route("/{id}", name="j29.crud.entity_*.delete", requirements={"id" = "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, **Entity-- $entity){
        // form creation
        $form = $this->renderDeleteForm($entity);
        $form->handleRequest($request);
    
        // form submission
        if ($form->isValid()) {
            $this->addFlash('success', 'Item successfully deleted');
            $this->sanitizeAndPersist($entity, 'delete');
        }
    
        return $this->redirectToRoute(self::ROUTE_INDEX);
    }

    /**
     * @Route("/sort/{sort_by}/{order}", defaults={"order" = "asc"})
     */
    public function sort(Request $request, $sort_by, $order){
        $build_variables = [
            'page_title' => '#',
            'page_description' => 'Admin Page',
        ];

        return $this->sortEntities($request, $sort_by, $order, $build_variables);
    }
}