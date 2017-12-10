<?php 

namespace JLibrary\Traits;

trait ControllerTraits
{
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

    /**
     * Simple utitlity to count rows present in database for given entity
     */
    protected function getEntityCount($namespace){
        $query_builder = $this->getDoctrine()->getManager()->createQueryBuilder();
        
        $query_builder->select('COUNT(ns.id)');
        $query_builder->from($namespace, 'ns');

        return (int)$query_builder->getQuery()->getSingleScalarResult();
    }

    protected function entityDeletionAllowed($entity, $repo_namespace, $join_column_property_name){
        $repo = $this->getDoctrine()->getManager()->getRepository($repo_namespace);

        $query = $repo->createQueryBuilder('ns')
            ->select('COUNT(ns.id)')
            ->where('ns.' . $join_column_property_name . ' = ' . $entity->getId());

        return (int)$query->getQuery()->getSingleScalarResult();
    }
}