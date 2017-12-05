<?php 

namespace J29Bundle\Controller\crud;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use J29Bundle\Entity\crud\Entity;
use J29Bundle\Form\crud\EntityType;
use JLibrary\Traits\ControllerTraits;

use JLibrary\Service\SingleFileManager;

/**
 * Entity crud controller
 * @Route("/admin/entities")
 */
class EntityController extends Controller
{
    use ControllerTraits;
    
    const ENTITY_NAMESPACE = 'J29Bundle:crud\Entity';

    const TMPL_INDEX = 'J29Bundle:crud:crud-index-entity.html.twig';
    const TMPL_ACTION = 'J29Bundle:crud:crud-action-entity.html.twig';

    const ROUTE_INDEX = 'j29.crud.entity.index';
    const ROUTE_DELETE = 'j29.crud.entity.delete';

    private $file_manager;

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
        'FileDescription' => [
            'type' => 'plain_text',
            'optional' => true,
        ],
    );

    private $template_vars = array(
        'form_size' => '###',
        'page_description' => 'Admin Page',
        'file_handler' => '###',
        'file_required' => true | false,
        'file_directory' => '###',
    );

    public function __construct(SingleFileManager $file_manager){
        $this->file_manager = $file_manager;
    }

    /**
     * @Route("/", name="j29.crud.entity.index")
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
     * @Route("/new", name="j29.crud.entity.new")
     */
    public function newAction(Request $request){
        $entity = new Entity();

        // form creation
        $form = $this->createForm(
            EntityType::class, 
            $entity, 
            ['disable_file_delete' => $this->template_vars['file_required']]
        );
        
        $form->handleRequest($request);

        // form submission
        if ($form->isValid()){
            $file = $this->file_manager->manage([
                'mode' => 'upload',
                'entity' => $entity,
                'directory' => $this->getParameter('upload_directories')[$this->template_vars['file_directory']],
                'handler' => $this->template_vars['file_handler'],
                'required' => $this->template_vars['file_required'],
            ]);

            if ($file === false){
                $this->addFlash('danger', 'You must upload a file!');
            } else {
                // sanitize, persist, and redirect
                $this->sanitizeAndPersist($entity, 'create');
                return $this->redirectToRoute(self::ROUTE_INDEX);
            }
        } else {
            if ($form->isSubmitted()) $this->addFlash('warning', 'You have errors with your form.');
        }

        // template data
        $build = array_merge([
            'creating_entity' => true,
            'form' => $form->createView(),
            'page_title' => 'New ###',
            'image_preview' => isset($file) ? $file : false,
        ], $this->template_vars);

        return $this->render(self::TMPL_ACTION, $build);
    }

    /**
     * @Route("/{id}/edit", name="j29.crud.entity.edit", requirements={"id" = "\d+"})
     */
    public function editAction(Request $request, Entity $entity){
        $file = $this->file_manager->manage([
            'mode' => 'toggle',
            'entity' => $entity,
            'directory' => $this->getParameter('upload_directories')[$this->template_vars['file_directory']],
            'handler' => $this->template_vars['file_handler'],
            'required' => $this->template_vars['file_required'],
        ]);

        $delete_form = $this->renderDeleteForm($entity);
        $form = $this->createForm(
            EntityType::class, 
            $entity, 
            ['disable_file_delete' => $this->template_vars['file_required']]
        );
        
        $form->handleRequest($request);

        if ($form->isValid()){
            $submitted_file = $this->file_manager->manage([
                'mode' => 'update',
                'entity' => $entity,
                'previous_file' => $file,
                'directory' => $this->getParameter('upload_directories')[$this->template_vars['file_directory']],
                'handler' => $this->template_vars['file_handler'],
                'required' => $this->template_vars['file_required'],
                'delete_file' => $form['delete_file']->getData(),
            ]);

            if ($submitted_file === false){
                throw new \Exception('Something went wrong!');
            } else {
                // sanitize, persist, and redirect
                $this->sanitizeAndPersist($entity, 'edit');
                return $this->redirectToRoute(self::ROUTE_INDEX);
            }
            
        } else {
            if ($form->isSubmitted()) $this->addFlash('warning', 'You have errors with your form.');
        }

        // template data
        $build = array_merge([
            'creating_entity' => false,
            'form' => $form->createView(),
            'image_preview' => isset($file) ? $file : false,
            'page_title' => 'Edit ###',
            'delete_form' => $delete_form->createView(),
        ], $this->template_vars);

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

            $file = $this->file_manager->manage([
                'mode' => 'delete',
                'entity' => $entity,
                'directory' => $this->getParameter('upload_directories')[$this->template_vars['file_directory']],
                'handler' => $this->template_vars['file_handler'],
                'required' => $this->template_vars['file_required'],
            ]);
            
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