<?php 

namespace J29Bundle\Form\crud;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use J29Bundle\Entity\crud\Entity;

class EntityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('levels', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'choices' => $options['order_choices'],
                'placeholder' => 'place before...'
            ])
            ->add('published')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Entity::class,
            'order_choices' => null,
        ));
    }
}