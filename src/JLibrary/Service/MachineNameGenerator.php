<?php 

namespace JLibrary\Service;

class MachineNameGenerator
{
    public function generateName($entity){
        $field_value = (null !== $entity->getMachineName()) ? $entity->getMachineName() : $entity->getTitle();

        $no_tags_trimmed_lowercase = strtolower(strip_tags(trim($field_value)));
        $spaces_made_hyphens = preg_replace('/ /', '-', $no_tags_trimmed_lowercase);
        $result = preg_replace('/[^-a-z]/', '', $spaces_made_hyphens);

        $entity->setMachineName($result);

        return true;
    }
}