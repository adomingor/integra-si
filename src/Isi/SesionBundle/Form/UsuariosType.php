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
use Doctrine\ORM\EntityRepository;

class UsuariosType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // var_dump($options);
        var_dump(array_keys($options));
        var_dump($options["by_reference"]);
        // var_dump($options["persSelecBD"][0]);
        caca;
        // $id = $options["id"];
        // ->add('password', PasswordType::class)
        $builder
            ->add('username')
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repetir Password'),
            ))
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
            ->add('perSelec')
            ->add('persona', EntityType::class, array(
                'class' => 'IsiPersonaBundle:Personas',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                    ->where('p.id in (:ids)')
                    ->setParameter('ids', array(333, 224, 334));
                },
                'placeholder' => 'Persona',
                'choice_label' => 'nombre',
                'multiple' => false,
            ))
        ;
        // ->add('persona', IntegerType::class)
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
