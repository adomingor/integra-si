<?php

namespace Isi\PersonalBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class LugarTrabajoPersType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $builder->add('usuario_crea')->add('ip_crea')->add('fecha_crea')->add('usuario_actu')->add('ip_actu')->add('fecha_actu')->add('personas')->add('lugarTrabajo')        ;
        $builder->add('usuario_crea')->add('ip_crea')->add('fecha_crea')->add('usuario_actu')->add('ip_actu')->add('fecha_actu');
        $builder->add('personas', CollectionType::class, array(
            'entry_type' => 'Isi\PersonaBundle\Form\PersonasType'
        ));
        $builder->add('lugarTrabajo', EntityType::class, array(
            'class' => 'IsiConfigBundle:LugarTrabajo',
            'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('lt')
                        ->orderBy('lt.descrip', 'ASC');
                },
            'placeholder' => 'Lugar de trabajo',
            'choice_label' => 'descrip'
        ));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Isi\PersonalBundle\Entity\LugarTrabajoPers'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'isi_personalbundle_lugartrabajopers';
    }


}
