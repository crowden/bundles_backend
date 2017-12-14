<?php 

namespace J29Bundle\Controller\crud;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use J29Bundle\Entity\crud\Document;
use J29Bundle\Form\crud\DocumentType;
use JLibrary\Traits\ControllerTraits;

use JLibrary\Service\SingleFileManager;

/**
 * Document crud controller
 * @Route("/admin/documents")
 */
class DocumentController extends Controller
{
    use ControllerTraits;
    
    const ENTITY_NAMESPACE = 'J29Bundle:crud\Document';

    const TMPL_INDEX = 'J29Bundle:crud:crud-index-document.html.twig';
    const TMPL_ACTION = 'J29Bundle:crud:crud-action-document.html.twig';

    const ROUTE_INDEX = 'j29.crud.document.index';
    const ROUTE_DELETE = 'j29.crud.document.delete';

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
        'DocumentDescription' => [
            'type' => 'plain_text',
            'optional' => false,
        ],
        'DocumentName' => [
            'type' => 'plain_text',
            'optional' => false,
        ],
        'LinkText' => [
            'type' => 'plain_text',
            'optional' => false,
        ],
    );

    private $template_vars = array(
        'form_size' => 'small',
        'page_description' => 'Admin Page',
        'file_handler' => 'document',
        'file_required' => true,
        'file_directory' => 'documents',
    );

    public function __construct(SingleFileManager $file_manager){
        $this->file_manager = $file_manager;
    }

    /**
     * @Route("/", name="j29.crud.document.index")
     * @Method("GET")
     */
    public function indexAction(){
        $entity_manager = $this->getDoctrine()->getManager();

        // template data
        $build = array_merge([
            'page_title' => 'Documents',
            'entities' => $entity_manager->getRepository(self::ENTITY_NAMESPACE)->findAll(),
        ], $this->template_vars);

        return $this->render(self::TMPL_INDEX, $build);
    }

    /**
     * @Route("/new", name="j29.crud.document.new")
     */
    public function newAction(Request $request){
        $entity = new Document();

        // form creation
        $form = $this->createForm(
            DocumentType::class, 
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
            'page_title' => 'New Document',
            'image_preview' => isset($file) ? $file : false,
        ], $this->template_vars);

        return $this->render(self::TMPL_ACTION, $build);
    }

    /**
     * @Route("/{id}/edit", name="j29.crud.document.edit", requirements={"id" = "\d+"})
     */
    public function editAction(Request $request, Document $entity){
        $file = $this->file_manager->manage([
            'mode' => 'toggle',
            'entity' => $entity,
            'directory' => $this->getParameter('upload_directories')[$this->template_vars['file_directory']],
            'handler' => $this->template_vars['file_handler'],
            'required' => $this->template_vars['file_required'],
        ]);

        $delete_form = $this->renderDeleteForm($entity);
        $form = $this->createForm(
            DocumentType::class, 
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
            'page_title' => 'Edit Document',
            'delete_form' => $delete_form->createView(),
        ], $this->template_vars);

        return $this->render(self::TMPL_ACTION, $build);
    }

    /**
     * @Route("/{id}", name="j29.crud.document.delete", requirements={"id" = "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Document $entity){
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
            'page_title' => 'Documents',
            'page_description' => 'Admin Page',
        ];

        return $this->sortEntities($request, $sort_by, $order, $build_variables);
    }
}