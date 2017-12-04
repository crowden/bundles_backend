<?php 

namespace J29Bundle\Controller\crud;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use J29Bundle\Entity\crud\Entity;
use J29Bundle\Form\crud\EntityType;
use JLibrary\Traits\ControllerTraits;

/**
 * Entity crud controller
 * @Route("/admin/entities")
 */
class EntityController extends Controller
{
    use ControllerTraits;
    
    const ENTITY_NAMESPACE = 'J29Bundle:crud\Entity';
    
    const UPLOAD_DIR = 'upload_directories';
    const FILE_DIR = 'entity_dir';
    
    const IMAGE_HANDLER = 'Image';

    const TMPL_INDEX = 'J29Bundle:crud:crud-index-entity.html.twig';
    const TMPL_ACTION = 'J29Bundle:crud:crud-action-entity.html.twig';

    const ROUTE_INDEX = 'j29.crud.entity.index';
    const ROUTE_DELETE = 'j29.crud.entity.delete';

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
        'file_required' => ###,
    );

    /**
     * @Route("/", name="j29.crud.entity.index")
     * @Method("GET")
     */
    public function indexAction(){
        $entity_manager = $this->getDoctrine()->getManager();

        $build = [
            'entities' => $entity_manager->getRepository(self::ENTITY_NAMESPACE)->findAll(),
            'page_title' => '#',
            'page_description' => 'Admin Page',
        ];

        return $this->render(self::TMPL_INDEX, $build);
    }

    /**
     * @Route("/new", name="j29.crud.entity.new")
     */
    public function newAction(Request $request){
        $entity = new Entity();

        // form creation
        $form = $this->createForm(EntityType::class, $entity);
        $form->handleRequest($request);

        // form submission
        if ($form->isValid()){
            // sanitize, persist, and redirect
            $this->sanitizeAndPersist($entity, 'create');
            return $this->redirectToRoute(self::ROUTE_INDEX);
            
            /*$image = $this->manageFile($entity, 'file_present', self::IMAGE_HANDLER);
            
            if($image){
                $this->manageFile($entity, 'upload', self::IMAGE_HANDLER, $image);
                
                // sanitize, persist, and redirect
                $this->sanitizeAndPersist($entity, 'create');
                return $this->redirectToRoute(self::ROUTE_INDEX);
            } else {
                $this->addFlash('danger', 'You must upload an image!');
            }*/
        } else {
            if ($form->isSubmitted()) $this->addFlash('warning', 'You have errors with your form.');
        }

        // build array

        return $this->render(self::TMPL_ACTION, $build);
    }

    /**
     * @Route("/{id}/edit", name="j29.crud.entity.edit", requirements={"id" = "\d+"})
     */
    public function editAction(Request $request, Entity $entity){
        $filename = $this->manageFile($entity, 'toggle', self::IMAGE_HANDLER);

        $delete_form = $this->renderDeleteForm($entity);
        $form = $this->createForm(EntityType::class, $entity);
        
        $form->handleRequest($request);

        if ($form->isValid()){
            $this->manageFile($entity, 'new_file', self::IMAGE_HANDLER, null, $filename);
            
            // sanitize, persist, and redirect
            $this->sanitizeAndPersist($entity, 'edit');
            return $this->redirectToRoute(self::ROUTE_INDEX);
        } else {
            if ($form->isSubmitted()) $this->addFlash('warning', 'You have errors with your form.');
        }

        // build array

        return $this->render(self::TMPL_ACTION, $build);
    }

    /**
     * @Route("/{id}", name="j29.crud.entity.delete", requirements={"id" = "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Entity $entity){
        // form creation
        $form = $this->renderDeleteForm($entity);
        $form->handleRequest($request);
    
        // form submission
        if ($form->isValid()) {
            $this->addFlash('success', 'Item successfully deleted');

            $this->manageFile($entity, 'delete', self::IMAGE_HANDLER);
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