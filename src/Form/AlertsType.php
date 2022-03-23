<?php

namespace App\Form;

use App\Entity\Alerts;
use App\Entity\Programmes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Captcha\Bundle\CaptchaBundle\Form\Type\CaptchaType;

class AlertsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('programme',EntityType::class,['class'=>Programmes::class,'choice_label'=>'nom','multiple'=>false])
            ->add('localisation')
            ->add('rapport',TextareaType::class)
            ->add('telephone')
            ->add('mail')
            ->add('captchaCode', CaptchaType::class, array('captchaConfig' => 'ExampleCaptcha'))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Alerts::class,
        ]);
    }
}
