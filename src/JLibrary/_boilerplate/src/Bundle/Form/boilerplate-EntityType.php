<?php 

namespace J29Bundle\Form\**entity_type**;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use J29Bundle\Entity\**entity_type**\**ENTITY_NAME**;

class **ENTITY_NAME**Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('title')
            ->add('published')

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => **ENTITY_NAME**::class,
        ));
    }
}



########      ###      #########
########   Templates   #########
########      ###      #########





/////////////////////////////////////////////
//         Optional File/Image             //
/////////////////////////////////////////////

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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

'disable_file_delete' => null,





/////////////////////////////////
//         Document            //
/////////////////////////////////

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use J29Bundle\Entity\category\DocumentCategory;

->add('documentName')
->add('document', null, [
    'label' => false,
])
->add('documentCategory', null, [
    'class' => DocumentCategory::class,
    'choice_label' => 'title',
    'placeholder' => 'Choose a category',
])
->add('documentDescription')
->add('documentLinkText')

'disable_file_delete' => null,





///////////////////////////////////
//         Ordering              //
///////////////////////////////////

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

->add('levels', ChoiceType::class, [
    'mapped' => false,
    'required' => false,
    'choices' => $options['order_choices'],
    'placeholder' => 'place before...'
])

'order_choices' => null,





///////////////////////////////////
//         MachineName           //
///////////////////////////////////

->add('machineName', null, [
    'required' => false,
    'label' => 'name for database',
    'disabled' => $options['machine_name_disabled'],
])

'machine_name_disabled' => false,

