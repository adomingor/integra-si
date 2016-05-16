<?php
namespace Isi\PersonaBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Isi\PersonaBundle\Entity\EstCiviles;
use Isi\PersonaBundle\Form\EstCivilesType;
use Symfony\Component\HttpFoundation\Response;

class EstadoCivilController extends Controller
{
    public function indexAction()
    {
        $resu = $this->getDoctrine()->getRepository('IsiPersonaBundle:EstCiviles')->findAllOrdByDescrip();
        return $this->render('IsiPersonaBundle:EstadoCivil:listado.html.twig', array('listado' => $resu, 'totRegi' => count($resu)));
    }

    private function usrCrea($form)
    {
        // $form->getData()->SetUsuariocrea($this->getUser()->getUsername()); // usuario q crea el registro
        // $form->getData()->SetIpcrea($request->getClientIp()); // ip del usaurio q crea el registro
        // $form->getData()->SetFechacrea( new \DateTime() ); // fecha y hora en que crea el registro
        return ($form);
    }

    private function usrActu($form)
    {
        // $form->getData()->SetUsuarioactu($this->getUser()->getUsername()); // usuario q actualiza el registro
        // $form->getData()->SetIpactu($request->getClientIp()); // ip del usaurio q actualiza el registro
        // $form->getData()->SetFechaactu( new \DateTime() ); // fecha y hora en que actualiza el registro
        return($form);
    }

    // private function grabar($form)
    // {
    //     // ver esta parte, no entra al catch, lo detecta symfony >:(
    //     $band = true;
    //     try {
    //         echo "intenta";
    //         $this->usrCrea($form); // datos del usuario q crea el registro
    //         $this->usrActu($form); // datos del usuario q actualiza el registro, cuando se crea el registro, es el mismo
    //         $em = $this->getDoctrine()->getManager();
    //         $em->persist($form->getData());
    //         $em->flush();
    //     } catch (Exception $e) {
    //         echo 'Excepción capturada: ',  $e->getMessage(), "\n";
    //         $band = false;
    //     }
    //     return ($band);
    // }

    public function nuevoAction(Request $request)
    {
        $request->getSession()->set('icoNombre', 'Nuevo Estado Civil');
        $estCivil = new EstCiviles();
        $form = $this->createForm(EstCivilesType::class, $estCivil);
        $form->handleRequest($request);
        if ($form->isValid()) {
            // // controlo q no exista el código del Indec o la descripción
            // $idRegi = null;
            // $band = false;
            // if ($form->getData()->getCodindec() != 0) { // si ingresan algún código (pueden no saberlo o no tener código del indec)
            //     $cons = $this->getDoctrine()->getRepository('IsiPersonaBundle:EstCiviles')->findByCodindec($form->getData()->getCodindec());
            //     if ($cons) {
            //         $band = true;
            //         $idRegi = $cons[0]->getId();
            //         $this->addFlash('Orange-700', 'Ya existe el código del indec: "' . $cons[0]->getCodindec() . '" en: "' . $cons[0]->getDescrip() . '"!' );
            //     }
            // }
            // $cons = $this->getDoctrine()->getRepository('IsiPersonaBundle:EstCiviles')->findByDescrip($form->getData()->getDescrip());
            // if (($cons)&&($idRegi != $cons[0]->getId())) {
            //     $band = true;
            //     $this->addFlash('Orange-700', 'Ya existe el Estado Civil: "' . $cons[0]->getDescrip() . '" código Indec: "' . $cons[0]->getCodindec() . '"!' );
            // }
            // if ($band)
            //     return $this->redirectToRoute('isi_persona_estadoCivil');
            // // Fin controlo q no exista el código del Indec o la descripción

            // $this->grabar($form);

            var_dump($form);

            $this->usrCrea($form); // datos del usuario q crea el registro
            $this->usrActu($form); // datos del usuario q actualiza el registro, cuando se crea el registro, es el mismo
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            $this->addFlash('Green-700', 'Se agregó "'. trim($form->getData()->getDescrip()) .'".');
            // return $this->redirectToRoute('isi_persona_estadoCivil');
        }
        return $this->render('IsiPersonaBundle:EstadoCivil:formularioVC.html.twig', array('form'=>$form->createView()));
    }

    public function edicionAction(Request $request, $id)
    {
        $request->getSession()->set('icoNombre', 'Edición de Estado Civil');
        $resu = $this->getDoctrine()->getRepository('IsiPersonaBundle:EstCiviles')->find($id);
        if (!$resu){
            $this->addFlash('Red-700', 'No existe el estado civil que quiere editar');
            return $this->redirectToRoute('isi_persona_estadoCivil');
        } else {
            $desc = $resu->getDescrip(); // guardo solo para mostrar lo que se modifico
            $codi = $resu->getCodindec(); // guardo solo para mostrar lo que se modifico
            $form = $this->createForm(EstCivilesType::class, $resu);
            $form->handleRequest($request);
            if ($form->isValid()) {
                // controlo q no exista el código del Indec o la descripción por el que se modifica
                // $idRegi = null;
                // $band = false;
                // $cons = $this->getDoctrine()->getRepository('IsiPersonaBundle:EstCiviles')->findByCodindec($form->getData()->getCodindec());
                // if (($cons)&&($resu->getId() != $cons[0]->getId()) ) {
                //     $band = true;
                //     $idRegi = $cons[0]->getId();
                //     $this->addFlash('Orange-700', 'Ya existe el código del indec: "' . $cons[0]->getCodindec() . '" en: "' . $cons[0]->getDescrip() . '" !' );
                // }
                // $cons = $this->getDoctrine()->getRepository('IsiPersonaBundle:EstCiviles')->findByDescrip($form->getData()->getDescrip());
                // if (($cons)&&($resu->getId() != $cons[0]->getId())&&($idRegi != $cons[0]->getId())) {
                //     $band = true;
                //     $this->addFlash('Orange-700', 'Ya existe el Estado Civil: "' . $cons[0]->getDescrip() . '" código Indec: "' . $cons[0]->getCodindec() . '" !' );
                // }
                // if ($band)
                //     return $this->redirectToRoute('isi_persona_estadoCivil');
                // Fin controlo q no exista el código del Indec o la descripción
                $this->usrActu($form); // datos del usuario q actualiza el registro
                $this->getDoctrine()->getManager()->flush();
                // return $this->redirectToRoute('isi_persona_estadoCivil');
            }
            return $this->render('IsiPersonaBundle:EstadoCivil:formularioVC.html.twig', array('form'=>$form->createView()));
        }
    }

    public function borrarAction(Request $request, $id)
    {
        $request->getSession()->set('icoNombre', 'Borrado de Estado Civil');
        $resu = $this->getDoctrine()->getRepository('IsiPersonaBundle:EstCiviles')->find($id);
        if (!$resu)//{}
            $this->addFlash('Red-700', 'No existe el estado civil que quiere eliminar');
        else {
            $desc = $resu->getDescrip();
            $codi = $resu->getCodindec();

            $em = $this->getDoctrine()->getManager();
            $em->remove($resu);
            $em->flush();
            $this->addFlash('Green-700', 'Se eliminó "' . $desc . ' (Indec: ' . $codi . ')" ');
        }
        return $this->redirectToRoute('isi_persona_estadoCivil');
    }

    public function formularioAction(Request $request)
    {
        $request->getSession()->set('icoNombre', 'Nuevo Estado Civil');
        $estCivil = new EstCiviles();
        $form = $this->createForm(EstCivilesType::class, $estCivil);
        $form->handleRequest($request);
        if ($form->isValid()) {
            // se captura el error en ajax
            $this->usrCrea($form); // datos del usuario q crea el registro
            $this->usrActu($form); // datos del usuario q actualiza el registro, cuando se crea el registro, es el mismo
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            $this->addFlash('Green-700', 'Se agregó "'. trim($form->getData()->getDescrip()) .'".');
        }
        return $this->render('IsiPersonaBundle:EstadoCivil:formulario.html.twig', array('form'=>$form->createView()));
    }
}
