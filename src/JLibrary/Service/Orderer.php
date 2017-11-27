<?php 

namespace JLibrary\Service;

use Doctrine\ORM\EntityManager;

class Orderer
{
    private $em;

    public function __construct(EntityManager $em){
        $this->em = $em;
    }

    public function getOrderChoices(Array $specs){
        $entity_count = $this->getEntityCount($specs['namespace']);
        $count_test = $specs['create_mode'] ? 0 : 1;

        // this is the 1st entity
        if ($entity_count === $count_test && $specs['create_mode']){
            $specs['current_entity']->setOrder(1);
            return null;
        }

        // 1st entity is being edited
        if ($entity_count === $count_test && !$specs['create_mode']){
            return null;
        }

        return $this->getSortedEntityArray($specs['namespace'], $specs['current_entity']);
    }

    private function getEntityCount($namespace){
        $query_builder = $this->em->createQueryBuilder();
        
        $query_builder->select('COUNT(ns.id)');
        $query_builder->from($namespace, 'ns');

        return (int)$query_builder->getQuery()->getSingleScalarResult();
    }

    private function getSortedEntityArray($namespace, $current_entity = null){
        $repo = $this->em->getRepository($namespace);
        
        $query = $repo->createQueryBuilder('ns')
            ->select('ns.id, ns.title, ns.order')
            ->orderBy('ns.order', 'ASC')
            ->getQuery();

        $result = $query->getResult();
        $result_single_dimension = [];

        for ($i = 0; $i < count($result); $i++){
            if (($current_entity !== null) && ($result[$i]['id'] === $current_entity->getId())){
                $result_single_dimension['--- current position---']  = $result[$i]['order'];
                continue;
            }
            
            $result_single_dimension[$result[$i]['title']] = $result[$i]['order'];
        }

        return $result_single_dimension;
    }

    private function getMaxOrderValue($namespace){
        $repo = $this->em->getRepository($namespace);
        
        $query = $repo->createQueryBuilder('ns')
            ->select('MAX(ns.order)')
            ->getQuery();

        return (int)$query->getSingleScalarResult();
    }

    private function reorder($level, $entity, $namespace){
        $all_entities = $this->em->getRepository($namespace)->findAll();

        foreach($all_entities as $single_entity){
            $current_order = $single_entity->getOrder();
            
            if ($current_order >= $level){
                $single_entity->setOrder($current_order + 1);
            }
        }

        // for the for the entity being created/edited
        $entity->setOrder($level);

        return true;
    }

    public function manageOrders(Array $specs){
        $entity_count = $this->getEntityCount($specs['namespace']);
        $count_test = $specs['create_mode'] ? 0 : 1;
        
        // POSSIBILITY #1: The 1st entity is being created|edited
        if($entity_count === $count_test) return null;

        // POSSIBILITY #2: Created new entity without order
        if (
            $entity_count !== $count_test && 
            $specs['option_chosen'] === null && 
            $specs['create_mode']){
                $highest = $this->getMaxOrderValue($specs['namespace']);
                $specs['current_entity']->setOrder(++$highest);

                return true;
        }

        // POSSIBILITY #3: Not 1st entry and level was chosen
        if ($entity_count !== $count_test && $specs['option_chosen'] !== null){
            if (!in_array($specs['option_chosen'], $specs['options_available'], true)){
                 throw new \Exception('Something went wrong!');
            } else {
                $this->reorder($specs['option_chosen'],$specs['current_entity'], $specs['namespace']);

                return true;
            }
        }
    }
}