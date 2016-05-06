<?php

namespace Isi\PersonaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Isi\PersonaBundle\Entity\EstCiviles;
use Isi\PersonaBundle\Form\EstCivilesType;

class EstadoCivilController extends Controller
{
    public function indexAction()
    {
        $resu = $this->getDoctrine()->getRepository('IsiPersonaBundle:EstCiviles')->findAll(array('descrip' => 'ASC'));
        if (!$resu)
            $this->addFlash('Red-700', 'No se encontraron Estados Civiles');
        return $this->render('IsiPersonaBundle:EstadoCivil:listado.html.twig', array('listado' => $resu));
    }

    public function nuevoAction(Request $request)
    {
        $request->getSession()->set('icoNombre', 'Nuevo Estado Civil');
        $estCivil = new EstCiviles();
        $form = $this->createForm(EstCivilesType::class, $estCivil);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $form->getData()->SetUsuariocrea($this->getUser()->getUsername()); // usuario q crea el registro
            $form->getData()->SetIpcrea($request->getClientIp()); // ip del usaurio q crea el registro
            $form->getData()->SetFechacrea( new \DateTime() ); // fecha y hora en que crea el registro
            $form->getData()->SetUsuarioactu($this->getUser()->getUsername()); // usuario q actualiza el registro
            $form->getData()->SetIpactu($request->getClientIp()); // ip del usaurio q actualiza el registro
            $form->getData()->SetFechaactu( new \DateTime() ); // fecha y hora en que actualiza el registro

            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            //
            $this->addFlash('Green-700', 'El estado civil'. trim($form->getData()->getDescrip()) .' fue grabado');
            // $estCivil = new EstCiviles();
            // $form = $this->createForm(new EstCivilesType(), $estCivil);
            return $this->redirectToRoute('isi_config_estadoCivil');
        }
        return $this->render('IsiPersonaBundle:EstadoCivil:estadoCivil.html.twig', array('form'=>$form->createView()));
    }
}
