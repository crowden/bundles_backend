<?php 

namespace JLibrary\Service;

class MachineNameGenerator
{
    public function generateName($entity, $prefix = null){
        $field_value = (null !== $entity->getMachineName()) ? $entity->getMachineName() : $entity->getTitle();

        $no_tags_trimmed_lowercase = strtolower(strip_tags(trim($field_value)));
        $spaces_made_underscores = preg_replace('/ /', '_', $no_tags_trimmed_lowercase);
        $result = preg_replace('/[^-a-z]/', '', $spaces_made_underscores);

        if (null !== $prefix) $result = $prefix . '_' . $result;

        $entity->setMachineName($result);

        return true;
    }
}