<?php 

namespace JLibrary\Traits;

trait ControllerTraits
{
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
        $file_manager = $this->get('JLibrary\Service\SingleFileManager');
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
        $sanitizer = $this->get('JLibrary\Service\Sanitizer');
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
     * utility function for sorting based on entity propertiy names.
     *
     * Requirements:
     *     Controller:
     *         - sort(Request $request, $sort_by, $order)
     *     Twig (index file)
     *         - <div id="js-sorting" class="sorting" data-options="{{ sorting_options|json_encode|e('html_attr') }}"></div>
     *
     * @param $sort_by [entity property name]
     * @param $order [asc|desc]
     */
    protected function sortEntities($request, $sort_by, $order, Array $build_variables){
        // if they want to reset the view
        if ($sort_by === 'reset') return $this->redirectToRoute(self::ROUTE_INDEX);

        // sort by pattern matches entity property names, not ORM column names!
        $pattern_sortby = '/^[a-zA-Z]+$/';
        $pattern_order = '/^(asc|desc)$/';

        if(preg_match($pattern_sortby, $sort_by) && preg_match($pattern_order, $order)){
            $clean_sort_by = 'e.' . $sort_by;
            $clean_order = $order;
            
            $repo = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository(self::ENTITY_NAMESPACE);

            try {
                $query = $repo->createQueryBuilder('e')
                    ->orderBy($clean_sort_by, $clean_order)
                    ->getQuery();

                $build = [
                    'page_title' => $build_variables['page_title'],
                    'page_description' => $build_variables['page_description'],
                    'entities' => $query->getResult(),
                ];
                
                return $this->render(self::TMPL_INDEX, $build);
            }
            catch(\Doctrine\ORM\Query\QueryException $e){
                $request->getSession()->getFlashBag()->add('warning', 'Can\'t find requested page');
                return $this->redirectToRoute(self::ROUTE_INDEX);
            }
        } else {
            $request->getSession()->getFlashBag()->add('warning', 'Can\'t find requested page');
            return $this->redirectToRoute(self::ROUTE_INDEX);
        }
    }
}