<?php
namespace Isi\PersonaBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Isi\PersonaBundle\Entity\EstCiviles;
use Isi\PersonaBundle\Form\EstCivilesType;
//use Symfony\Component\HttpFoundation\Response;


class EstadoCivilController extends Controller
{
    private function sugerencia(){
        $sugerencia = "<br><br><p class='text-muted'><small><i class='fa fa-lightbulb-o fa-lg text-warning' aria-hidden='true'></i> Utiliza el filtro de búsqueda para verificar si un dato existe.</small></p>";
        return($sugerencia);
    }

    public function indexAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-opera fa-2x'< aria-hidden='true'></i>&nbsp;<i class='fa fa-list-alt fa-lg'< aria-hidden='true'></i>");
        $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->findAllOrdByDescrip();
        return $this->render("IsiPersonaBundle:EstadoCivil:listado.html.twig", array("listado" => $resu, "totRegi" => count($resu)));
    }

    private function usrCrea($form)
    {
        $request = Request::createFromGlobals();
        $form->getData()->SetUsuarioCrea($this->getUser()->getUsername()); // usuario q crea el registro
        $form->getData()->SetIpCrea($request->getClientIp()); // ip del usaurio q crea el registro
        $form->getData()->SetFechaCrea(new \DateTime()); // fecha y hora en que crea el registro
        return ($form);
    }

    private function usrActu($form)
    {
        $form->getData()->SetUsuarioActu($this->getUser()->getUsername()); // usuario q actualiza el registro
        $form->getData()->SetIpActu(Request::createFromGlobals()->getClientIp()); // ip del usaurio q actualiza el registro
        $form->getData()->SetFechaActu(new \DateTime()); // fecha y hora en que actualiza el registro
        return($form);
    }

    private function grabar($form)
    {
        $band = true;
        if ($form->getData()->getCodindec() > 0) {
            $cons = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->findByCodindec($form->getData()->getCodindec());
            if ($cons) {
                $band = false;
                // nomenclatura addFlash("TipoMensaje (ver integra.js)", "Titulo ¬ Mensaje (acepta etiquetas html)"
                $this->addFlash("warning", "duplicado ¬ ya existe el código del indec: '" . $cons[0]->getCodindec() . "' en: '".$cons[0]->getDescrip() . "' !'" . $this->sugerencia());
            }
        }
        else {
            if ($form->getData()->getCodindec() < 0) {
                $band = false;
                $this->addFlash("error", "dato incorrecto ¬ El código del Indec no es válido!");
            }
        }
        if ($band) {
            try {
                $this->usrCrea($form); // datos del usuario q crea el registro
                $this->usrActu($form); // datos del usuario q actualiza el registro, cuando se crea el registro, es el mismo
                $em = $this->getDoctrine()->getManager();
                $em->persist($form->getData());
                $em->flush();
            }
            catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                $band = false;
                // $this->addFlash('success', 'Ups! Ésto ocurrió "' . $e->getMessage());

                // nomenclatura addFlash("TipoMensaje (ver integra.js)", "Titulo ¬ Mensaje (acepta etiquetas html)"
                $this->addFlash("warning", "duplicado ¬ ya existe el estado civil <u class='text-warning'><b>" . $form->getData()->getDescrip() . "</b></u>" . $this->sugerencia());
            }
            catch (\Exception $e) { // excepcion general
                $band = false;
                $this->addFlash("error", "Ups!¬" . $e->getMessage());
            }
        }

        return ($band);
    }

    public function nuevoAction(Request $request)
    {
        // var_dump($request->get('_route'));
        // var_dump($request->getUri());
        $request->getSession()->set("icoNombre", "<i class='fa fa-opera fa-2x'< aria-hidden='true'></i>&nbsp;<i class='fa fa-plus fa-lg'< aria-hidden='true'></i>");
        $estCivil = new EstCiviles();
        $form = $this->createForm(EstCivilesType::class, $estCivil);
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($this->grabar($form))
                $this->addFlash("success", "buen trabajo! ¬ Se agregó '".trim($form->getData()->getDescrip()) . "' (" . $form->getData()->getCodindec() . ") . '");
            return $this->redirectToRoute('isi_persona_estadoCivil');
        }
        return $this->render("IsiPersonaBundle:EstadoCivil:formularioVC.html.twig", array("form"=>$form->createView(), "idForm"=>"fEstCivNuevo", "urlAction"=>$request->getUri()));
    }

    public function edicionAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-opera fa-2x'< aria-hidden='true'></i>&nbsp;<i class='fa fa-pencil fa-lg'< aria-hidden='true'></i>");
        $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->find($id);
        if (!$resu){
            $this->addFlash("error", "mmmm <i class='fa fa-thumbs-o-down' aria-hidden='true'></i> ¬ <i class='text-danger'>No existe</i> el estado civil que quieres modificar");
            return $this->redirectToRoute("isi_persona_estadoCivil");
        } else {
            $desc = $resu->getDescrip(); // guardo solo para mostrar lo que se modifico
            $codi = $resu->getCodindec(); // guardo solo para mostrar lo que se modifico
            $usrCrea = $resu->getUsuarioCrea(); // usuario q crea el registro
            $ipCrea = $resu->getIpCrea(); // ip del usaurio q crea el registro
            $fechaCrea = $resu->getFechaCrea(); // fecha y hora en que crea el registro
            $form = $this->createForm(EstCivilesType::class, $resu);
            $form->handleRequest($request);
            if ($form->isValid()) {
                // controlo q no exista el código del Indec si es mayor que 0
                $band = false;
                if ($form->getData()->getCodindec() > 0) {
                    $cons = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->findByCodindec($form->getData()->getCodindec());
                    if (($cons)&&($resu->getId() != $cons[0]->getId()) ) {
                        $band = true;
                        $this->addFlash("warning", "duplicado ¬ ya existe el código del indec: '" . $cons[0]->getCodindec() . "' en: '".$cons[0]->getDescrip() . "' !'" . $this->sugerencia());

                    }
                }
                // Fin controlo q no exista el código del Indec si es mayor que 0
                if (!$band) {
                    try {
                        // el usuario creador no se modifica
                        $form->getData()->SetUsuarioCrea($usrCrea);
                        $form->getData()->SetIpCrea($ipCrea);
                        $form->getData()->SetFechaCrea($fechaCrea);
                        $this->usrActu($form); // datos del usuario q actualiza el registro
                        $this->getDoctrine()->getManager()->flush();
                        $this->addFlash("success", "buen trabajo! ¬ Se modificó '" . $desc . "(" . $codi . ")'" . " por '" . trim($form->getData()->getDescrip()) . " (" . $form->getData()->getCodindec() . ")' . ");
                    }
                    catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                        $band = false;
                        $this->addFlash("warning", "duplicarás ¬ Ya existe el estado civil por el que intenta cambiar" . $this->sugerencia());
                    }
                    catch (\Exception $e) { // excepcion general
                        $band = false;
                        $this->addFlash("error", "Ups! ¬" . $e->getMessage());
                    }
                }
                return $this->redirectToRoute('isi_persona_estadoCivil');
            }
            return $this->render("IsiPersonaBundle:EstadoCivil:formularioVC.html.twig", array("form"=>$form->createView(), "idForm"=>"fEstCivActu", "urlAction"=>$request->getUri()));
        }
    }

    public function borrarAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-opera fa-2x'< aria-hidden='true'></i>&nbsp;<i class='fa fa-trash fa-lg'< aria-hidden='true'></i>");
        $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->find($id);
        if (!$resu)
            $this->addFlash("danger", "No existe el estado civil que quiere eliminar");
        else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($resu);
            $em->flush();
            $this->addFlash("success", "Se eliminó '" . $resu->getDescrip() . " (Indec: " . $resu->getCodindec() . ")' ");
        }
        return $this->redirectToRoute('isi_persona_estadoCivil');
    }

    public function formularioAction(Request $request)
    {
        // var_dump($request->get('_route'));
        // var_dump($request->getUri());
        $request->getSession()->set("icoNombre", "<i class='fa fa-opera fa-2x'< aria-hidden='true'></i>&nbsp;<i class='fa fa-plus fa-lg'< aria-hidden='true'></i>");
        $estCivil = new EstCiviles();
        $form = $this->createForm(EstCivilesType::class, $estCivil);
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($form->getData()->getCodindec() > 0) {
                $cons = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->findByCodindec($form->getData()->getCodindec());
                if ($cons) { // controlo no este duplicado el codigo del indec
                    throw new \Exception('Ya existe el código del Indec!');
                }
                else {
                    // de esta forma ajax detecta el error y se muestra un mensaje generico con sweetAlert2
                    $this->usrCrea($form); // datos del usuario q crea el registro
                    $this->usrActu($form); // datos del usuario q actualiza el registro, cuando se crea el registro, es el mismo
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($form->getData());
                    $em->flush();
                }
            }

            // de esta forma valida el controlador y muestra los mensajes flash
            // $this->grabar($form);
            // if ($this->grabar($form))
            //     $this->addFlash("success", "Se agregó '".trim($form->getData()->getDescrip())." (".$form->getData()->getCodindec().")'.");
        }
        return $this->render("IsiPersonaBundle:EstadoCivil:formulario.html.twig", array("form"=>$form->createView(),"idForm"=>"", "urlAction"=>""));
    }
}
