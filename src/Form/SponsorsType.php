<?php

namespace App\Form;

use App\Entity\Evenements;
use App\Entity\Sponsors;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class SponsorsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('adresse')
            ->add('mail')
            ->add('numero')
            ->add('imageFile',FileType::class,['required'=>false])
            ->add('evenements',EntityType::class,['class'=>Evenements::class,'choice_label'=>'nom','multiple'=>true]);

        ;    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sponsors::class,
        ]);
    }
}
