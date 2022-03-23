<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Programmes;
use App\Entity\Locaux;
use App\Entity\Transporteur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgrammesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('date')
            ->add('duree')
            ->add('details' )
            ->add('transporteur',EntityType::class,['class'=>Transporteur::class,'choice_label'=>'nom','multiple'=>false])
            ->add('locale',EntityType::class,['class'=>Locaux::class,'choice_label'=>'nom','multiple'=>false]);;


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Programmes::class,
        ]);
    }
}
