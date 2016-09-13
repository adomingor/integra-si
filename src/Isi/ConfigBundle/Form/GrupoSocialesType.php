<?php

namespace Isi\ConfigBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GrupoSocialesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('grupo')
            ->add('descrip')
            ->add('usuario_crea')
            ->add('ip_crea')
            ->add('fecha_crea')
            ->add('usuario_actu')
            ->add('ip_actu')
            ->add('fecha_actu')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Isi\ConfigBundle\Entity\GrupoSociales'
        ));
    }
}
