<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content')
            ->add(
                'created_at',
                DateTimeType::class,
                [
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd\'T\'HH:mm:ssZZZZZ',
                    'property_path' => 'createdAt',
                ]
            )
            ->add('completed')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'allow_extra_fields' => false,
            'csrf_protection'    => false,
        ]);
    }
}
