<?php

namespace Isi\PersonaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DniesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero')
            ->add('pulgarDcho')
            ->add('foto')
            ->add('femision', DateType::class)
            ->add('fvto', DateType::class)
            ->add('nrotramite')
            ->add('ejemplar')
            ->add('codigo')
            ->add('codqr')
            ->add('personas', PersonasType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Isi\PersonaBundle\Entity\Dnies'
        ));
    }
}
