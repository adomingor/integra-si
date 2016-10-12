<?php

namespace Isi\ConfigBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

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
            ->add('creado', DateType::class, array(
                 'widget' => 'single_text',
                 'format' => 'dd/MM/yyyy',
                 'required' => false,
                 'invalid_message' => 'dd/mm/aaaa'
             ))
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
