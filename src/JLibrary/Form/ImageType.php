<?php 

namespace JLibrary\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use JLibrary\Entity\Image;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('imageTemp')
            ->add('imageAlt')
            ->add('pathSet', HiddenType::class)
            ->add('modificationDate', null, [
                'label' => false,
                'attr' => [
                    'disabled' => true,
                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Image::class,
        ));
    }
}