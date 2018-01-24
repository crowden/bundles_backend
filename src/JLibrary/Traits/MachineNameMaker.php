<?php 

namespace JLibrary\Traits;

/**
 * MachineNameMaker [creates unique machine names]
 */

trait MachineNameMaker {
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max=255, maxMessage="This field can only contain 255 characters")
     */
    private $machineName;

    /**
     * @return mixed
     */
    public function getMachineName()
    {
        return $this->machineName;
    }

    /**
     * @param mixed $machineName
     *
     * @return self
     */
    public function setMachineName($machineName)
    {
        $this->machineName = $machineName;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function generateMachineName(){
        $field_value = (null !== $this->getMachineName()) ? $this->getMachineName() : $this->getTitle();

        $no_tags_trimmed_lowercase = strtolower(strip_tags(trim($field_value)));
        $spaces_made_underscores = preg_replace('/( |-)/', '_', $no_tags_trimmed_lowercase);
        $result = preg_replace('/[^a-z0-9_\-]/', '', $spaces_made_underscores);

        if (null !== self::MACHINE_NAME_PREFIX) $result = self::MACHINE_NAME_PREFIX . '_' . $result;

        $this->setMachineName($result);

        return true;
    }
}