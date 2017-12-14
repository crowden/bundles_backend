<?php 

namespace J29Bundle\Controller\category;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use J29Bundle\Entity\category\**ENTITY**;
use J29Bundle\Form\category\**ENTITY**Type;
use JLibrary\Traits\ControllerTraits;

use JLibrary\Service\MachineNameGenerator;
use JLibrary\Service\SingleFileManager;

/**
 * **ENTITY** category controller
 * @Route("/admin/*_entity_*-categories")
 */
class **ENTITY**Controller extends Controller
{
    use ControllerTraits;
    
    const ENTITY_NAMESPACE = 'J29Bundle:category\**ENTITY**';

    const TMPL_INDEX = 'J29Bundle:category:category-index-*_entity_*-category.html.twig';
    const TMPL_ACTION = 'J29Bundle:category:category-action-*_entity_*-category.html.twig';

    const ROUTE_INDEX = 'j29.category.**ENTITY_ROUTING**.index';
    const ROUTE_DELETE = 'j29.category.**ENTITY_ROUTING**.delete';

    private $machine_name_maker;
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
        'MachineName' => [
            'type' => 'plain_text',
            'optional' => true,
        ],
        'CategoryDescription' => [
            'type' => 'plain_text',
            'optional' => false,
        ],
        'FileDescription' => [
            'type' => 'plain_text',
            'optional' => true,
        ],
    );

    private $template_vars = array(
        'form_size' => 'large',
        'page_description' => 'Admin Page',
        'file_handler' => 'file',
        'file_required' => true|false,
        'file_directory' => '##FILE_DIR##',
    );

    public function __construct(MachineNameGenerator $machine_name_maker, SingleFileManager $file_manager){
        $this->machine_name_maker = $machine_name_maker;
        $this->file_manager = $file_manager;
    }

    /**
     * @Route("/", name="j29.category.**ENTITY_ROUTING**.index")
     * @Method("GET")
     */
    public function indexAction(){
        $entity_manager = $this->getDoctrine()->getManager();

        // template data
        $build = array_merge([
            'page_title' => '##PAGE_TITLE## Categories',
            'entities' => $entity_manager->getRepository(self::ENTITY_NAMESPACE)->findAll(),
        ], $this->template_vars);

        return $this->render(self::TMPL_INDEX, $build);
    }

    /**
     * @Route("/new", name="j29.category.**ENTITY_ROUTING**.new")
     */
    public function newAction(Request $request){
        $entity = new **IN_CONTROLLER_ENTITY**();

        // form creation
        $form = $this->createForm(
            **IN_CONTROLLER_ENTITY**Type::class, 
            $entity, 
            [
                'disable_file_delete' => $this->template_vars['file_required']
            ]
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
                $this->machine_name_maker->generateName($entity, 'category');
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
            'page_title' => 'Create ##PAGE_TITLE## Category',
            'form' => $form->createView(),
            'image_preview' => isset($file) ? $file : false,
        ], $this->template_vars);

        return $this->render(self::TMPL_ACTION, $build);
    }

    /**
     * @Route("/{id}/edit", name="j29.category.**ENTITY_ROUTING**.edit", requirements={"id" = "\d+"})
     */
    public function editAction(Request $request, **IN_CONTROLLER_ENTITY** $entity){
        $file = $this->file_manager->manage([
            'mode' => 'toggle',
            'entity' => $entity,
            'directory' => $this->getParameter('upload_directories')[$this->template_vars['file_directory']],
            'handler' => $this->template_vars['file_handler'],
            'required' => $this->template_vars['file_required'],
        ]);

        $delete_form = $this->renderDeleteForm($entity);
        
        $form = $this->createForm(
            **IN_CONTROLLER_ENTITY**Type::class, 
            $entity,
            [
                'machine_name_disabled' => true, 
                'disable_file_delete' => $this->template_vars['file_required'],
            ]
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
            'page_title' => 'Edit ##PAGE_TITLE## Category',
            'form' => $form->createView(),
            'image_preview' => isset($file) ? $file : false,
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
            $allowed_to_delete = $this->entityDeletionAllowed($entity, 'entity_namespace', 'entity_join_property');
            
            if ($allowed_to_delete){
                $this->addFlash('success', 'Item successfully deleted');

                $file = $this->file_manager->manage([
                    'mode' => 'delete',
                    'entity' => $entity,
                    'directory' => $this->getParameter('upload_directories')[$this->template_vars['file_directory']],
                    'handler' => $this->template_vars['file_handler'],
                    'required' => $this->template_vars['file_required'],
                ]);

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
            'page_title' => '##PAGE_TITLE## Categories',
            'page_description' => 'Admin Page',
        ];

        return $this->sortEntities($request, $sort_by, $order, $build_variables);
    }
}