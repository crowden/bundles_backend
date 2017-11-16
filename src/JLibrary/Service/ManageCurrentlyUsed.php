<?php 

namespace JLibrary\Service;

use Doctrine\ORM\EntityManagerInterface;

class ManageCurrentlyUsed
{
    private $entity_manager;
    
    public function __construct(EntityManagerInterface $entity_manager){
        $this->entity_manager = $entity_manager;
    }

    public function manage($current_entity_id, $repository){
        $repository = $this->entity_manager->getRepository($repository);

        $entities_set_to_current = $repository->findBy(
            array('useAsCurrent' => 1)
        );

        foreach ($entities_set_to_current as $entity) {
            if ($entity->getId() === $current_entity_id) continue;

            $entity->setUseAsCurrent(0);
        }
        
        $this->entity_manager->flush();
    }
}