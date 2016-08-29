<?php

namespace Isi\SesionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Doctrine\ORM\EntityRepository;

class UsuariosType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $idP = $builder->getData()->getPersona()->getId();
        $builder
            ->add('username')
            ->add('password', PasswordType::class)
            ->add('salt')
            ->add('email', EmailType::class)
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
            ->add('persona', EntityType::class, array(
                'class' => 'IsiPersonaBundle:Personas',
                'query_builder' => function (EntityRepository $er) use ($idP){
                    return $er->createQueryBuilder('p')
                    ->where('p.id = (:idP)')
                    ->setParameter('idP', $idP);
                },
                'choice_label' => function ($er) {return ("ID: " . $er->getId() . ' - ' . $er->getApellido() . ', ' . $er->getNombre());},
                'multiple' => false,
            ))
            ->add('perSelec')
        ;
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
