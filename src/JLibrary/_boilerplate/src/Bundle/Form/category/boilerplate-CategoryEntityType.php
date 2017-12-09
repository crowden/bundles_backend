<?php 

namespace J29Bundle\Form\category;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use J29Bundle\Entity\category\**NAME**;

class **NAME**Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('title')
            ->add('machineName', null, [
                'required' => false,
                'label' => 'name for database',
                'disabled' => $options['machine_name_disabled'],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => **NAME**::class,
            'machine_name_disabled' => false
        ));
    }
}