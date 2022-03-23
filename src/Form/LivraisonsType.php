<?php

namespace App\Form;

use App\Entity\Livraisons;
use App\Entity\Livreurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class LivraisonsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('datelivraison')
            ->add('adresse')
            ->add('livreur',EntityType::class,['class'=>Livreurs::class,'choice_label'=>'nom','multiple'=>false])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Livraisons::class,
        ]);
    }
}
