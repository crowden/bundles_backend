<?php 

namespace JLibrary\Traits;

trait ControllerTraits
{
    /**
     * utility function for sanitizing and persisting entities to db.
     * Method requires parent class to have an array of sanitize options for
     * entity members.
     *
     * Allowed values for persisting must be one of the following:
     *     - create
     *     - edit
     *     - delete
     */
    protected function sanitizeAndPersist($entity, $action){
        $sanitizer = $this->get('j29.sanitizer');
        $sanitizer->sanitize($entity, $this->sanitize_options);
        
        $entity_manager = $this->getDoctrine()->getManager();

        switch($action){
            case 'create':
                $entity_manager->persist($entity);
                break;
            case 'edit':
                break;
            case 'delete':
                $entity_manager->remove($entity);
        } 

        // shared by all entity manager functions
        $entity_manager->flush();
    }

    /**
     * utility function for interacting with the single_file_manager service;
     *
     * Allowed values for @var $case include:
     *     - delete
     *     - file_present
     *     - new_file
     *     - toggle
     *     - upload
     */
    protected function manageFile($entity, $case, $handler, $file = NULL, $current_file_name = NULL){
        $file_manager = $this->get('j29.single_file_manager');
        $file_directory = $this->getParameter(self::UPLOAD_DIR)[self::FILE_DIR];

        switch($case){
            case 'file_present':
                return $file_manager->fileIsNotNull($entity, $handler);
            case 'upload':
                $file_manager->upload($entity, $file, $file_directory, $handler);
                break;
            case 'toggle':
                return $file_manager->toggleFileAndFilename($entity, $file_directory, $handler);
            case 'new_file':
                $file_manager->handleNewFileUpload($entity, $current_file_name, $file_directory, $handler);
                break;
            case 'delete':
                $file_manager->deleteFile($entity, $file_directory, $handler);
                break;
        }
    }

    /**
     * dynamically creates delete form shared by all controllers
     */
    protected function renderDeleteForm($entity){
        return $this->createFormBuilder()
            ->setAction($this->generateUrl(self::ROUTE_DELETE, array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * utility function to sort index page entries
     * @Route("/sort/{sorted_by}", name="j29.admin.sort")
     */
    protected function sort(Request $request, $sort_by){
        $entity_manager = $this->getDoctrine()->getManager();
        $repository = $entity_manager->getRepository(self::ENTITY_NAMESPACE);

        highlight_string("<?php\n\$request =\n" . var_export($request, true) . ";\n?>");
    }
}