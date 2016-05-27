<?php

namespace Isi\PersonaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Isi\PersonaBundle\Entity\IdentGeneros;
use Isi\PersonaBundle\Form\IdentGenerosType;

class IdentidadGeneroController extends Controller
{
    public function indexAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "Identidad de Género");
        // -> findBy es para obtener todos ordenaos por genero (no es reutilizable auqi, hay que ponerlo en el repositorio, dejo solo de muerstra)
        $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:IdentGeneros")->findBy(array(), array('genero' => 'ASC'));
        return $this->render("IsiPersonaBundle:IdentidadGenero:listado.html.twig", array("listado" => $resu, "totRegi" => count($resu)));
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

    private function grabar($form)
    {
        $band = true;
        try {
            $this->usrCrea($form); // datos del usuario q crea el registro
            $this->usrActu($form); // datos del usuario q actualiza el registro, cuando se crea el registro, es el mismo
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
        }
        catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            $band = false;
            // $this->addFlash('Orange-700', 'Ups! Ésto ocurrió "' . $e->getMessage());
            $this->addFlash("Red-900", "Ya existe el género que intenta agregar");
        }
        catch (\Exception $e) { // excepcion general
            $band = false;
            $this->addFlash("Red-900", "Ups!: " . $e->getMessage());
        }

        return ($band);
    }

    public function nuevoAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "Identidad de Género Nuevo");
        $form = $this->createForm(IdentGenerosType::class, new IdentGeneros());
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($this->grabar($form))
                $this->addFlash("Green-700", "Se agregó '". trim($form->getData()->getGenero()) ."'");
            return $this->redirectToRoute('isi_persona_genero');
        }
        return $this->render("IsiPersonaBundle:IdentidadGenero:formulario.html.twig", array("form"=>$form->createView()));
    }

    public function edicionAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "Edición de Género");
        $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:IdentGeneros")->find($id);
        if (!$resu){
            $this->addFlash("Red-700", "No existe el género que quiere editar");
            return $this->redirectToRoute("isi_persona_genero");
        } else {
            $form = $this->createForm(IdentGenerosType::class, $resu);
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $this->usrActu($form); // datos del usuario q actualiza el registro
                    $this->getDoctrine()->getManager()->flush();
                }
                catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                    $this->addFlash("Red-900", "Ya existe el nombre género por el que intenta cambiar");
                }
                catch (\Exception $e) { // excepcion general
                    $band = false;
                    $this->addFlash("Red-900", "Ups!: " . $e->getMessage());
                }

                return $this->redirectToRoute('isi_persona_genero');
            }
            return $this->render("IsiPersonaBundle:IdentidadGenero:formulario.html.twig", array("form"=>$form->createView()));
        }
    }

    /**
     * @Route("/genero/borrar/{id}")
     */
    public function borrarAction($id)
    {
        return $this->render('IsiPersonaBundle:IdentidadGenero:listado.html.twig', array(
            // ...
        ));
    }

}
