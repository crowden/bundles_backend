<?php 

namespace J29Bundle\Controller\crud;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use J29Bundle\Entity\crud\**Entity**;
use J29Bundle\Form\crud\**Entity**Type;
use JLibrary\Traits\ControllerTraits;

use JLibrary\Service\Orderer;

/**
 * **Entity** crud controller
 * @Route("/admin/entities")
 */
class **Entity**Controller extends Controller
{
    use ControllerTraits;
    
    const ENTITY_NAMESPACE = 'J29Bundle:crud\**Entity**';

    const TMPL_INDEX = 'J29Bundle:crud:crud-index-entity**.html.twig';
    const TMPL_ACTION = 'J29Bundle:crud:crud-action-entity**.html.twig';

    const ROUTE_INDEX = 'j29.crud.entity__&&.index';
    const ROUTE_DELETE = 'j29.crud.entity__&&.delete';

    private $jOrderer;

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

    public function __construct(Orderer $jOrderer){
        $this->jOrderer = $jOrderer;
    }

    /**
     * @Route("/", name="j29.crud.entity__&&.index")
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
     * @Route("/new", name="j29.crud.entity__&&.new")
     */
    public function newAction(Request $request){
        $entity = new Entity---*();
        
        $form_options['order_choices'] = $this->jOrderer->getOrderChoices(array(
            'current_entity' => $entity,
            'namespace' => self::ENTITY_NAMESPACE,
            'create_mode' => true,
        ));

        // form creation
        $form = $this->createForm(Entity---*Type::class, $entity, $form_options);
        $form->handleRequest($request);


        if ($form->isValid()){
            // manage any ordering
            $this->jOrderer->manageOrders(array(
                'option_chosen' => $form['levels']->getData(),
                'options_available' => $form_options['order_choices'],
                'current_entity' => $entity,
                'namespace' => self::ENTITY_NAMESPACE,
                'create_mode' => true,
            ));

            // sanitize, persist, and redirect
            $this->sanitizeAndPersist($entity, 'create');
            return $this->redirectToRoute(self::ROUTE_INDEX);
        } else {
            if ($form->isSubmitted()) $this->addFlash('warning', 'You have errors with your form.');
        }

        // template data
        $build = array_merge([
            'creating_entity' => true,
            'page_title' => 'New ###',
            'form' => $form->createView(),
        ], $this->template_vars);

        return $this->render(self::TMPL_ACTION, $build);
    }

    /**
     * @Route("/{id}/edit", name="j29.crud.entity__&&.edit", requirements={"id" = "\d+"})
     */
    public function editAction(Request $request, Entity---* $entity){
        $form_options['order_choices'] = $this->jOrderer->getOrderChoices(array(
            'current_entity' => $entity,
            'namespace' => self::ENTITY_NAMESPACE,
            'create_mode' => false,
        ));
        
        $delete_form = $this->renderDeleteForm($entity);
        $form = $this->createForm(Entity---*Type::class, $entity, $form_options);
        
        $form->handleRequest($request);

        if ($form->isValid()){
            // manage any ordering
            $this->jOrderer->manageOrders(array(
                'option_chosen' => $form['levels']->getData(),
                'options_available' => $form_options['order_choices'],
                'current_entity' => $entity,
                'namespace' => self::ENTITY_NAMESPACE,
                'create_mode' => false,
            ));
            
            // sanitize, persist, and redirect
            $this->sanitizeAndPersist($entity, 'edit');
            return $this->redirectToRoute(self::ROUTE_INDEX);
        } else {
            if ($form->isSubmitted()) $this->addFlash('warning', 'You have errors with your form.');
        }

        // template data
        $build = array_merge([
            'creating_entity' => false,
            'page_title' => 'Edit ###',
            'form' => $form->createView(),
            'delete_form' => $delete_form->createView(),
        ], $this->template_vars);

        return $this->render(self::TMPL_ACTION, $build);
    }

    /**
     * @Route("/{id}", name="j29.crud.entity__&&.delete", requirements={"id" = "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Entity---* $entity){
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
            'page_title' => '###',
            'page_description' => 'Admin Page',
        ];

        return $this->sortEntities($request, $sort_by, $order, $build_variables);
    }
}