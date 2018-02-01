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
            /*->add('j29FormCode', TextType::class, [
                'mapped' => false,
                'label' => 'J29 Form Code',
                'required' => false,
                'trim' => false,
            ])*/
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


//////////////////////////////////////
//         Collection               //
//////////////////////////////////////

->add('socialIcons', CollectionType::class, [
    'entry_type' => SingleType::class,
    'allow_add' => true,
    'allow_delete' => true,
    'by_reference' => false,
    'delete_empty' => true,
    'entry_options' => [
        'required' => false,
        'error_bubbling' => false,
    ],
])

->add('manualCollection', CollectionType::class, [
    'entry_type' => SingleType::class,
    'by_reference' => false,
    'error_bubbling' => false,
    'entry_options' => [
        'required' => false,
        'error_bubbling' => false,
    ],
])

///////////////////////////////////
//         Honeypot              //
///////////////////////////////////

->add('j29FormCode', TextType::class, [
    'mapped' => false,
    'label' => 'J29 Form Code',
    'required' => false,
    'trim' => false,
])

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

/////////////////////////////////
//         DateTime            //
/////////////////////////////////

use Symfony\Component\Form\Extension\Core\Type\DateType;

->add('dateEnd', DateType::class, [
    'widget' => 'choice',
    'placeholder' => [
        'year' => 'Year',
        'month' => 'Month',
        'day' => 'Day',
    ]
])

//////////////////////////////
//         Time             //
//////////////////////////////

use Symfony\Component\Form\Extension\Core\Type\TimeType;
->add('timeEnd', TimeType::class, [
    'placeholder' => [
        'hour' => 'hour',
        'minute' => 'minute',
    ]
])



///////////////////////////////////////
//         Image(trait)              //
///////////////////////////////////////
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
->add('imageTemp')
->add('imageAlt')
->add('pathSet', HiddenType::class)
->add('modificationDate', null, [
    'label' => false,
    'attr' => [
        'disabled' => true,
    ]
])