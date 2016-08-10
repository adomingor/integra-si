<?php

namespace Isi\SesionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RolesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        //     ->add('name')
        //     ->add('role', EntityType::class, array(
        //             'class' => 'IsiSesionBundle:Roles',
        //             'query_builder' => function (EntityRepository $er) {
        //                     return $er->createQueryBuilder('r')
        //                         ->orderBy('r.name', 'ASC');
        //                 },
        //             'placeholder' => 'Roles',
        //             'choice_label' => 'name',
        //             'multiple' => true,
        //         ))
        //     ->add('usuarios')
        // ;
        $builder
            ->add('name')
            ->add('role')
            ->add('usuarios')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Isi\SesionBundle\Entity\Roles'
        ));
    }
}
