<?php 

namespace J29Bundle\Form\crud;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use J29Bundle\Entity\crud\***ENTITY***;

class ***ENTITY***Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('fileDescription')
            ->add('file', null, [
                'label' => false,
            ])
            ->add('delete_file', CheckboxType::class, [
                'mapped' => false,
                'label' => false,
                'disabled' => $options['disable_file_delete'],
                'required' => false,
            ])
            ->add('published')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => ***ENTITY***::class,
            'disable_file_delete' => null
        ));
    }
}