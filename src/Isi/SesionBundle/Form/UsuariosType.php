<?php

namespace Isi\SesionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class UsuariosType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password')
            ->add('salt')
            ->add('email')
            ->add('isActive')
            ->add('imagen')
            ->add('roles', EntityType::class, array(
                    'class' => 'IsiSesionBundle:Roles',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('r')
                                ->orderBy('r.name', 'ASC');
                        },
                    'placeholder' => 'Roles',
                    'choice_label' => 'name',
                    'multiple' => true,
                ))
                ->add('persona', IntegerType::class)
        ;
        // ->add('persona', HiddenType::class)
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Isi\SesionBundle\Entity\Usuarios'
        ));
    }
}
