<?php 

namespace J29Bundle\Controller\category;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use J29Bundle\Entity\category\*Entity*;
use J29Bundle\Form\category\*Entity*Type;
use JLibrary\Traits\ControllerTraits;

/**
 * *Entity* category controller
 * @Route("/admin/entities")
 */
class *Entity*Controller extends Controller
{
    use ControllerTraits;
    
    const ENTITY_NAMESPACE = 'J29Bundle:category\*Entity*';

    const TMPL_INDEX = 'J29Bundle:category:category-index-**entity.html.twig';
    const TMPL_ACTION = 'J29Bundle:category:category-action-**entity.html.twig';

    const ROUTE_INDEX = 'j29.category.entity**.index';
    const ROUTE_DELETE = 'j29.category.entity**.delete';

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
        ]
    );

    private $template_vars = array(
        'form_size' => '###',
        'page_description' => 'Admin Page',
    );

    /**
     * @Route("/", name="j29.category.entity**.index")
     * @Method("GET")
     */
    public function indexAction(){
        $entity_manager = $this->getDoctrine()->getManager();

        // template data
        $build = array_merge([
            'page_title' => '### Categories',
            'entities' => $entity_manager->getRepository(self::ENTITY_NAMESPACE)->findAll(),
        ], $this->template_vars);

        return $this->render(self::TMPL_INDEX, $build);
    }

    /**
     * @Route("/new", name="j29.category.entity**.new")
     */
    public function newAction(Request $request){
        $entity = new __Entity--();

        // form creation
        $form = $this->createForm(__Entity--Type::class, $entity);
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
            'page_title' => 'New ### Category',
            'form' => $form->createView(),
        ], $this->template_vars);

        return $this->render(self::TMPL_ACTION, $build);
    }

    /**
     * @Route("/{id}/edit", name="j29.category.entity**.edit", requirements={"id" = "\d+"})
     */
    public function editAction(Request $request, __Entity-- $entity){
        $delete_form = $this->renderDeleteForm($entity);
        $form = $this->createForm(__Entity--Type::class, $entity);
        
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
            'page_title' => 'Edit ### Category',
            'form' => $form->createView(),
            'delete_form' => $delete_form->createView(),
        ], $this->template_vars);

        return $this->render(self::TMPL_ACTION, $build);
    }

    /**
     * @Route("/{id}", name="j29.category.entity**.delete", requirements={"id" = "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, __Entity-- $entity){
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
            'page_title' => '### Categories',
            'page_description' => 'Admin Page',
        ];

        return $this->sortEntities($request, $sort_by, $order, $build_variables);
    }
}