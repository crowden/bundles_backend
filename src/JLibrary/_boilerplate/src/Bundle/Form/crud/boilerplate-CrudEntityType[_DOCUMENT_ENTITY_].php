<?php 

namespace J29Bundle\Form\crud;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use J29Bundle\Entity\crud\Document;
use J29Bundle\Entity\category\DocumentCategory;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('documentName')
            ->add('documentDescription')
            ->add('document', null, [
                'label' => false,
            ])
            ->add('delete_file', CheckboxType::class, [
                'mapped' => false,
                'label' => false,
                'disabled' => $options['disable_file_delete'],
                'required' => false,
            ])
            ->add('linkText')
            ->add('documentCategory', null, [
                'class' => DocumentCategory::class,
                'choice_label' => 'title',
                'placeholder' => 'Choose a category',
            ])
            ->add('published')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Document::class,
            'disable_file_delete' => null
        ));
    }
}
