<?php

namespace App\Form;

use App\Entity\Evenements;
use App\Entity\Sponsors;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EvenementsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('date')
            ->add('imageFile',FileType::class,['required'=>false])
            ->add('nbrplace')
            ->add('datef')
            ->add('Lieu')
            ->add('sponsors',EntityType::class,['class'=>Sponsors::class,'choice_label'=>'nom','multiple'=>true]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Evenements::class,
        ]);
    }
}
