<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Reminder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReminderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                "label" => "Nom du rappel"
            ])
            ->add('description', null, [
                "label" => "Description"
            ])
            ->add('dueDate', null, [
                "label" => "Date limite",
                'widget' => 'single_text'
            ])
            ->add('category', EntityType::class, [
                "label" => "Catégorie",
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Aucune catégorie',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reminder::class,
        ]);
    }
}
