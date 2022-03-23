<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Produits;
use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProduitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prix')
            ->add('description')
            ->add('quantite')
            ->add('imageFile',FileType::class,[
                'required'=>false
            ])
            ->add('categorie',EntityType::class,['class'=>Categories::class,'choice_label'=>'nom','multiple'=>false])
             ->add('utilisateur',EntityType::class,['class'=>Utilisateur::class,'choice_label'=>'nom','multiple'=>false]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
