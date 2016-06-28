<?php

namespace Isi\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Isi\AdminBundle\Form\MensajesType;
use Isi\AdminBundle\Entity\Mensajes;
use Symfony\Component\HttpFoundation\Session\Session;
// use Doctrine\ORM\EntityManager;

class MensajesSistemaController extends Controller
{
    // protected $em;
    // protected $flash;
    //
    // public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    // {
    //     $this->em = $entityManager;
    //     // $this->flash = $flash;
    // }

    public function indexAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-comments-o fa-2x isi_icono-mensaje' aria-hidden='true'></i>");
        // -> findBy es para obtener todos ordenaos por genero (no es reutilizable auqi, hay que ponerlo en el repositorio, dejo solo de muerstra)
        $resu = $this->getDoctrine()->getRepository("IsiAdminBundle:Mensajes")->findAllMsjYTipoOrderTipo();
        // $resu = $this->getDoctrine()->getRepository("IsiAdminBundle:Mensajes")->findAll();
        return $this->render("IsiAdminBundle:Mensajes:listado.html.twig", array("listado" => $resu, "totRegi" => count($resu)));
    }

    private function grabar($form)
    {
        $band = true;
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
        }
        catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            $band = false;
            $this->addFlash("warning", "duplicado ¬ psst... <br> negr@... <br> <strong>ya existe</strong> el mensaje!");
        }
        catch (\Exception $e) { // excepcion general
            $band = false;
            $this->addFlash("error", "Ups! ¬" . $e->getMessage());
        }
        return ($band);
    }

    public function nuevoAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-comments-o fa-2x isi_icono-mensaje' aria-hidden='true'></i>&nbsp;<i class='fa fa-plus fa-lg isi_icono-mensaje' aria-hidden='true'></i>");
        $form = $this->createForm(MensajesType::class, new Mensajes());
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($this->grabar($form))
                $this->addFlash("success", "bien aheee! ¬ mensaje grabado!");
                // $this->addFlash("success", "bien aeee! ¬ mensaje grabado'" . trim($form->getData()->getGenero()) . "'");
            return $this->redirectToRoute('isi_admin_mensajeSistema');
        }
        return $this->render("IsiAdminBundle:Mensajes:formulario.html.twig", array("form"=>$form->createView()));
    }

    public function edicionAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-comments-o fa-2x isi_icono-mensaje' aria-hidden='true'></i>&nbsp;<i class='fa fa-pencil fa-lg isi_icono-mensaje' aria-hidden='true'></i>");
        $resu = $this->getDoctrine()->getRepository("IsiAdminBundle:Mensajes")->find($id);
        if (!$resu){
            $this->addFlash("error", "¬No existe el mensaje que quiere editar");
            return $this->redirectToRoute("isi_admin_mensajeSistema");
        } else {
            $form = $this->createForm(MensajesType::class, $resu);
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash("success", "arreeeeeeba ¬ Se modificó el mensaje!");
                }
                catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                    $this->addFlash("warning", "Nuuuuu ¬ Ya existe el mensaje por el que intentas cambiar");
                }
                catch (\Exception $e) { // excepcion general
                    $band = false;
                    $this->addFlash("error", "Ups! ¬".$e->getMessage());
                }

                return $this->redirectToRoute('isi_admin_mensajeSistema');
            }
            return $this->render("IsiAdminBundle:Mensajes:formulario.html.twig", array("form"=>$form->createView()));
        }
    }

    public function borrarAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-comments-o fa-2x isi_icono-mensaje' aria-hidden='true'></i>&nbsp;<i class='fa fa-trash fa-lg isi_icono-mensaje' aria-hidden='true'></i>");
        $resu = $this->getDoctrine()->getRepository("IsiAdminBundle:Mensajes")->find($id);
        if (!$resu)
            $this->addFlash("error", "No existe el género que quiere eliminar");
        else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($resu);
            $em->flush();
            $this->addFlash("success", "Se eliminó '" . $resu->getGenero() . "'");
        }
        return $this->redirectToRoute("isi_admin_mensajeSistema");
    }

    // public function mensajeAction(Request $request, $id) {
    //     var_dump("entra");
    //     $tipo = "warning";
    //     $titulo = "no se obtuvo " . "<i class='fa fa-commenting-o' aria-hidden='true' style='font-size:2em;'></i>";
    //     $descrip = "";
    //     try {
    //         var_dump("<br> intenta");
    //         // $resu = $this->getDoctrine()->getRepository("IsiAdminBundle:Mensajes")->findMsById($id);
    //         $resu = $this->em->getRepository("IsiAdminBundle:Mensajes")->findMsById($id);
    //         // esto si el repositorio devuelve array
    //         $tipo = $resu[0]["tipoMensaje"]["descrip"];
    //         $titulo = $resu[0]["titulo"];
    //         $descrip = $resu[0]["descrip"];
    //         // esto si el repositorio devuelve objeto
    //         // $tipo = $resu[0]->getTipoMensaje()->getDescrip();
    //         // $titulo = $resu[0]->getTitulo();
    //         // $descrip = $resu[0]->getDescrip();
    //
    //     } catch (\Exception $e) {
    //         $resu = null;
    //     }
    //     // var_dump("<BR>");
    //     // var_dump($resu);
    //     // var_dump($tipo);
    //     // var_dump("<BR>");
    //     // var_dump($titulo);
    //     // var_dump("<BR>");
    //     // var_dump($descrip);
    //     // var_dump("<BR>");
    //     return ($this->addFlash($tipo, $titulo . "¬ " . $descrip));
    // }
}
