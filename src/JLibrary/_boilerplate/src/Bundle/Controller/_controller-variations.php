<?php 

use JLibrary\Service\SingleFileManager; // Files
use JLibrary\Service\Orderer; // Ordering
use JLibrary\Service\MachineNameGenerator; // Categories

class _Variations extends Controller
{
    private $file_manager; // Files
    private $orderer; // Ordering
    private $machine_name_maker; // Categories

    private $sanitize_options = array(
        ////////////////
        // Categories //
        ////////////////
        'Title' => [
            'type' => 'plain_text',
            'optional' => false,
        ],
        'MachineName' => [
            'type' => 'plain_text',
            'optional' => true,
        ]
        ///////////
        // Files //
        ///////////
        'FileDescription' => [
            'type' => 'plain_text',
            'optional' => true,
        ],
        ///////////////
        // Documents //
        ///////////////
        'DocumentDescription' => [
            'type' => 'plain_text',
            'optional' => false,
        ],
        'DocumentName' => [
            'type' => 'plain_text',
            'optional' => false,
        ],
        'DocumentLinkText' => [
            'type' => 'plain_text',
            'optional' => false,
        ],
    );

    private $template_vars = array(
        ///////////
        // Files //
        ///////////
        'file_handler' => '###',
        'file_required' => true|false,
        'file_directory' => '###',
    );

    public function __construct(SingleFileManager $file_manager, Orderer $orderer, MachineNameGenerator $machine_name_maker){
        $this->file_manager = $file_manager; // Files
        $this->orderer = $orderer; // Ordering
        $this->machine_name_maker = $machine_name_maker; // Categories
    }

    public function indexAction(){}

    public function newAction(Request $request){
        ///////////
        // Files //
        ///////////
        $form = $this->createForm(###Type::class, $entity, ['disable_file_delete' => $this->template_vars['file_required']]);
        $form->handleRequest($request);

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
                $this->sanitizeAndPersist($entity, 'create');
                return $this->redirectToRoute(self::ROUTE_INDEX);
            }
        } '...'

        //////////////
        // Ordering //
        //////////////
        $form_options['order_choices'] = $this->orderer->getOrderChoices(array(
            'current_entity' => $entity,
            'namespace' => self::ENTITY_NAMESPACE,
            'create_mode' => true,
        ));

        $form = $this->createForm(###Type::class, $entity, $form_options);
        $form->handleRequest($request);

        if ($form->isValid()){
            // manage any ordering
            $this->orderer->manageOrders(array(
                'option_chosen' => $form['levels']->getData(),
                'options_available' => $form_options['order_choices'],
                'current_entity' => $entity,
                'namespace' => self::ENTITY_NAMESPACE,
                'create_mode' => true,
            ));

            $this->sanitizeAndPersist($entity, 'create');
            return $this->redirectToRoute(self::ROUTE_INDEX);
        } '...'

        ////////////////
        // Categories //
        ////////////////
        if ($form->isValid()){
            $this->machine_name_maker->generateName($entity, 'category');
            
            $this->sanitizeAndPersist($entity, 'create');
            return $this->redirectToRoute(self::ROUTE_INDEX);
        } '...'

        ...........................................

        ///////////
        // Files //
        ///////////
        $build[] = 'image_preview' => isset($file) ? $file : false,
    }

    public function editAction(Request $request, ### $entity){
        ///////////
        // Files //
        ///////////
        $file = $this->file_manager->manage([
            'mode' => 'toggle',
            'entity' => $entity,
            'directory' => $this->getParameter('upload_directories')[$this->template_vars['file_directory']],
            'handler' => $this->template_vars['file_handler'],
            'required' => $this->template_vars['file_required'],
        ]);

        $delete_form = $this->renderDeleteForm($entity);
        $form = $this->createForm(###Type::class, $entity, ['disable_file_delete' => $this->template_vars['file_required']]);

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
                $this->sanitizeAndPersist($entity, 'edit');
                return $this->redirectToRoute(self::ROUTE_INDEX);
            }
            
        } '...'

        //////////////
        // Ordering //
        //////////////
        $form_options['order_choices'] = $this->orderer->getOrderChoices(array(
            'current_entity' => $entity,
            'namespace' => self::ENTITY_NAMESPACE,
            'create_mode' => false,
        ));

        $delete_form = $this->renderDeleteForm($entity);
        $form = $this->createForm(###Type::class, $entity, $form_options);
        
        $form->handleRequest($request);

        if ($form->isValid()){
            // manage any ordering
            $this->orderer->manageOrders(array(
                'option_chosen' => $form['levels']->getData(),
                'options_available' => $form_options['order_choices'],
                'current_entity' => $entity,
                'namespace' => self::ENTITY_NAMESPACE,
                'create_mode' => false,
            ));
            
            $this->sanitizeAndPersist($entity, 'edit');
            return $this->redirectToRoute(self::ROUTE_INDEX);
        } '...'

        ////////////////
        // Categories //
        ////////////////

        $delete_form = $this->renderDeleteForm($entity);
        $form = $this->createForm(###Type::class, $entity, ['machine_name_disabled' => true]);
        
        $form->handleRequest($request);

        ...........................................

        ///////////
        // Files //
        ///////////
        $build[] = 'image_preview' => isset($file) ? $file : false,
    }

    public function deleteAction(Request $request, ### $entity){
        ///////////
        // Files //
        ///////////
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
    }
}
