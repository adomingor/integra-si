<?php

namespace Isi\ConfigBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LugarTrabajoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descrip')
            ->add('creado', 'date')
            ->add('objetivo')
            ->add('usuario_crea')
            ->add('ip_crea')
            ->add('fecha_crea')
            ->add('usuario_actu')
            ->add('ip_actu')
            ->add('fecha_actu')
            ->add('tiposLugarTrabajo', EntityType::class, array(
                'class' => 'IsiConfigBundle:TiposLugarTrabajo',
                'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('tlt')
                            ->orderBy('tlt.descrip', 'ASC');
                    },
                'placeholder' => 'tipo de lugar',
                'choice_label' => 'descrip'
            ))
        ;
        // ->add('tiposLugarTrabajo')
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Isi\ConfigBundle\Entity\LugarTrabajo'
        ));
    }
}
