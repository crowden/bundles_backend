<?php 

namespace J29Bundle\Controller\category;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use J29Bundle\Entity\category\**NAMESPACE**;
use J29Bundle\Form\category\**NAMESPACE**Type;
use JLibrary\Traits\ControllerTraits;

use JLibrary\Service\MachineNameGenerator;

/**
 * **NAMESPACE** category controller
 * @Route("/admin/###-PLURAL-###")
 */
class **NAMESPACE**Controller extends Controller
{
    use ControllerTraits;
    
    const ENTITY_NAMESPACE = 'J29Bundle:category\**NAMESPACE**';

    const TMPL_INDEX = 'J29Bundle:category:category-index-**SINGLE_ENTITY**.html.twig';
    const TMPL_ACTION = 'J29Bundle:category:category-action-**SINGLE_ENTITY**.html.twig';

    const ROUTE_INDEX = 'j29.category.**ENTITY_ROUTING**.index';
    const ROUTE_DELETE = 'j29.category.**ENTITY_ROUTING**.delete';

    private $machine_name_maker;

    /**
     * types include:
     *     - plain_text
     *     - url
     *     - url_validated
     *     - email_address
     *     - markdown_general
     */
    private $sanitize_options = array(
        'Title' => [
            'type' => 'plain_text',
            'optional' => false,
        ],
        'MachineName' => [
            'type' => 'plain_text',
            'optional' => true,
        ]
    );

    private $template_vars = array(
        'form_size' => 'large',
        'page_description' => 'Admin Page',
    );

    public function __construct(MachineNameGenerator $machine_name_maker){
        $this->machine_name_maker = $machine_name_maker;
    }

    /**
     * @Route("/", name="j29.category.**ENTITY_ROUTING**.index")
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
     * @Route("/new", name="j29.category.**ENTITY_ROUTING**.new")
     */
    public function newAction(Request $request){
        $entity = new **ENTITY_IN_CONTROLLER**();

        // form creation
        $form = $this->createForm(**ENTITY_IN_CONTROLLER**Type::class, $entity);
        $form->handleRequest($request);

        // form submission
        if ($form->isValid()){
            $this->machine_name_maker->generateName($entity);
            // sanitize, persist, and redirect
            $this->sanitizeAndPersist($entity, 'create');
            return $this->redirectToRoute(self::ROUTE_INDEX);
        } else {
            if ($form->isSubmitted()) $this->addFlash('warning', 'You have errors with your form.');
        }

        // template data
        $build = array_merge([
            'creating_entity' => true,
            'page_title' => 'Create ##-##',
            'form' => $form->createView(),
        ], $this->template_vars);

        return $this->render(self::TMPL_ACTION, $build);
    }

    /**
     * @Route("/{id}/edit", name="j29.category.**ENTITY_ROUTING**.edit", requirements={"id" = "\d+"})
     */
    public function editAction(Request $request, **ENTITY_IN_CONTROLLER** $entity){
        $delete_form = $this->renderDeleteForm($entity);
        $form = $this->createForm(
            **ENTITY_IN_CONTROLLER**Type::class, 
            $entity,
            ['machine_name_disabled' => true]
        );
        
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
            'page_title' => 'Edit ##-##',
            'form' => $form->createView(),
            'delete_form' => $delete_form->createView(),
        ], $this->template_vars);

        return $this->render(self::TMPL_ACTION, $build);
    }

    /**
     * @Route("/{id}", name="j29.category.**ENTITY_ROUTING**.delete", requirements={"id" = "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, **ENTITY_IN_CONTROLLER** $entity){
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