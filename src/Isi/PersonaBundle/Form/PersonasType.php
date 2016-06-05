<?php

namespace Isi\PersonaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PersonasType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('apellido')
            ->add('nombre')
            ->add('sexo', ChoiceType::class, array(
                    'choices'  => array(
                        'Femenino' => 'f',
                        'Masculino' => 'm',
                    ),
                    'expanded' => true,
                ))
            ->add('fnac', DateType::class, array(
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                    'invalid_message' => 'La fecha no es correcta'
                ))
            ->add('ffallec', DateType::class, array(
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                    'invalid_message' => 'La fecha no es correcta'
                ))
            ->add('email')
            ->add('nn')
            ->add('descrip')
            ->add('foto')
            ->add('usuario_crea')
            ->add('ip_crea')
            ->add('fecha_crea')
            ->add('usuario_actu')
            ->add('ip_actu')
            ->add('fecha_actu')
            ->add('estciviles', EntityType::class, array(
                    'class' => 'IsiPersonaBundle:EstCiviles',
                    'choice_label' => 'descrip'
                ))
            ->add('lugarnacim', EntityType::class, array(
                    'class' => 'IsiPersonaBundle:LugarNacim',
                    'choice_label' => 'descrip'
                ))
            ->add('identgeneros', EntityType::class, array(
                    'class' => 'IsiPersonaBundle:IdentGeneros',
                    'choice_label' => 'genero',
                    'multiple' => true,
                    'expanded' => true,
                ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Isi\PersonaBundle\Entity\Personas'
        ));
    }
}
