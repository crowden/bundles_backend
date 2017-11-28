<?php 

namespace J29Bundle\Controller\category;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use J29Bundle\Entity\category\Entity;
use J29Bundle\Form\category\EntityType;
use JLibrary\Traits\ControllerTraits;

/**
 * Entity category controller
 * @Route("/admin/entities")
 */
class EntityController extends Controller
{
    use ControllerTraits;
    
    const ENTITY_NAMESPACE = 'J29Bundle:category\Entity';
    
    const UPLOAD_DIR = 'upload_directories';
    const FILE_DIR = 'entity_dir';
    
    const IMAGE_HANDLER = 'Image';

    const TMPL_INDEX = 'J29Bundle:category:category-index-entity.html.twig';
    const TMPL_ACTION = 'J29Bundle:category:category-action-entity.html.twig';

    const ROUTE_INDEX = 'j29.category.entity.index';
    const ROUTE_DELETE = 'j29.category.entity.delete';

    // type:[plain_text][url][url_validated][email_address][markdown_general]
    private $sanitize_options = array(
        'Title' => [
            'type' => 'plain_text',
            'optional' => false,
        ]
    );

    /**
     * @Route("/", name="j29.category.entity.index")
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
     * @Route("/new", name="j29.category.entity.new")
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

        // template data
        $build = [
            'creating_entity' => #,
            'form' => $form->createView(),
            'form_size' => '#',
            'image_preview' => '#',
            'image_mobile_preview' => '#',
            'page_description' => 'Admin Page',
            'page_title' => '#',
            'show_image_preview' => #,
        ];

        return $this->render(self::TMPL_ACTION, $build);
    }

    /**
     * @Route("/{id}/edit", name="j29.category.entity.edit", requirements={"id" = "\d+"})
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

        // template data
        $build = [
            'creating_entity' => #,
            'delete_form' => $delete_form->createView(),
            'form' => $form->createView(),
            'form_size' => '#',
            'image_preview' => '#',
            'image_mobile_preview' => '#',
            'page_description' => 'Admin Page',
            'page_title' => '#',
            'show_image_preview' => #,
        ];

        return $this->render(self::TMPL_ACTION, $build);
    }

    /**
     * @Route("/{id}", name="j29.category.entity.delete", requirements={"id" = "\d+"})
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