<?php

namespace Isi\SialeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-folder-open fa-2x isi_iconoLegajos' aria-hidden='true'></i>");
        return $this->render('IsiSialeBundle:Default:index.html.twig');
    }

    public function legMotOrigPersAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-search fa-2x isi_iconoLegajos' aria-hidden='true'></i><i class='fa fa-folder-open fa-2x isi_iconoLegajos' aria-hidden='true'></i>");
        $verLinks = false;

        $form = $this->createFormBuilder()
            ->setMethod("GET")
            ->add("fDde", TextType::class)
            ->add("fHta", TextType::class)
            ->add("chkConfirma", CheckboxType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            if (($form->get("fDde")->getdata() == null) || ($form->get("fHta")->getdata() == null)) {
                $this->forward("isi_mensaje:msjFlash", array("id" => 34));
                return $this->redirectToRoute("isi_consulta_legMotOrigPers", array("verLinks" => $verLinks));
            }
            try {
                $resu = $this->cantLegajos($form->get("fDde")->getdata(), $form->get("fHta")->getdata());
            } catch (\Exception $e) {
                $text = $e->getMessage();
                switch (true) {
                    case stristr($text, "22007"): # error en el formato de la fecha
                        $this->forward("isi_mensaje:msjFlash", array("id" => 35));
                        break;
                    default:
                        $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>consultando S. I. A. Le.</u><br> "));
                        // $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>consultando S. I. A. Le.</u><br> " . $e->getMessage()));
                        break;
                }
                $resu = null;
                return $this->redirectToRoute("isi_consulta_legMotOrigPers", array("verLinks" => $verLinks));
            }
            $cantRegi = $resu[0]["cant"];
            if ($cantRegi == 0)
                $this->forward("isi_mensaje:msjFlash", array("id" => 6));
            else {
                $verLinks = true;
                $colorMsj = "wwarning";
                $msg2 = "Puedes iniciar el proceso de descarga";
                $limiteReg = 20000;
                switch (true) {
                    case ($cantRegi <= 1000):
                        $colorMsj = "ssuccess";
                        break;
                    case ($cantRegi <= 4000):
                        $colorMsj = "iinfo";
                        break;
                    case ($cantRegi <= 10000):
                        break;
                    case ($cantRegi > $limiteReg):
                        $verLinks = false;
                        $msg2 = "Por el momento, un informe con más de $limiteReg legajos deben ser solicitados al Dpto. Informática de la S. E. N. A. y F.";
                        break;
                }
                $this->addFlash($colorMsj, "Se encontraron $cantRegi legajo(s). ". $msg2);
            }
        }
        return $this->render("IsiSialeBundle:Default:legajoMotivoDatosPersExporta.html.twig", array("form"=>$form->createView(), "verLinks" => $verLinks));
    }

    private function cantLegajos ($fDde, $fHta)
    {
        $sql = "select count (distinct numero) as cant from familia.t_legajosc a, familia.t_documentos b, familia.t_legajosd c where a.idLegajoC = c.idLegajoC and b.iddocumento = c.iddocumento and a.codPers = b.codPers and b.fechaDoc between '".$fDde."' and '".$fHta."';";
        $em = $this->getDoctrine()->getManager("infseptimo")->getConnection();
        $stmt = $em->prepare($sql);
        $stmt->execute();
        return ($stmt->fetchAll());
    }

    public function legMotOrigPersCSVAction ($fDde, $fHta)
    {
        // Obtener toda la información posible del SIALe
        // echo ("inicio <br>");
        // echo (date("d/m/Y H:i:s"));
        // echo("<br>");

        $em = $this->getDoctrine()->getManager("infseptimo")->getConnection();

        // eliminamos si existen las tablas temporales
        $sql = "DROP TABLE cons1;";
        $stmt = $em->prepare($sql);
        try {
            $stmt->execute();
        } catch(\Exception $e) {
        }
        $sql = "DROP TABLE cons2;";
        $stmt = $em->prepare($sql);
        try {
            $stmt->execute();
        } catch(\Exception $e) {
        }
        $sql = "DROP TABLE cons3;";
        $stmt = $em->prepare($sql);
        try {
            $stmt->execute();
        } catch(\Exception $e) {
        }
        $sql = "DROP TABLE cons44;";
        $stmt = $em->prepare($sql);
        try {
            $stmt->execute();
        } catch(\Exception $e) {
        }
        $sql = "DROP TABLE cons4;";
        $stmt = $em->prepare($sql);
        try {
            $stmt->execute();
        } catch(\Exception $e) {
        }
        $sql = "DROP TABLE informe;";
        $stmt = $em->prepare($sql);
        try {
            $stmt->execute();
        } catch(\Exception $e) {
        }
        // fin eliminamos si existen las tablas temporales

        $sql = "select distinct a.idlegajoc, a.numero as legajo, a.nrofolios, a.fechalegc as fechaCreaLeg, a.activo as LegajoActivo, a.codpers, g.idlegmotivo, g.legmotivodesc as motivolegajo, h.apellido, h.nombre, h.fnac, h.sexo into temp cons1 from familia.t_legajosc a, familia.t_documentos b, familia.t_legajosd c, familia.t_legmotivos g, persona h where a.idLegajoC = c.idLegajoC and b.iddocumento = c.iddocumento and a.codPers = b.codPers and b.fechaDoc between '".$fDde."' and '".$fHta."' and a.idlegmotivo = g.idlegmotivo and a.codPers = h.codPers and b.codPers = h.codPers;";
        $stmt = $em->prepare($sql);
        try {
            $stmt->execute();
        } catch(\Exception $e) {
            $resu = ['ups_Error' => $e];
            return $resu;
        }

        $sql = "select distinct f.idLegajoC, f.idCaso, f.casonro, f.activo as casoActivo, f.fechaegreso as egresoCaso, g.idmotivointervencion, g.motivodescs, h.idprocfuente, h.procfuentedesc into temp cons2 from familia.t_legajosc a, familia.t_documentos b, familia.t_legajosd c, familia.t_usuariosfam d, familia.t_entidades e, familia.t_casos f, familia.t_motivosintervencion g, familia.t_procfuentes h where a.idLegajoC = c.idLegajoC and b.iddocumento = c.iddocumento and a.activo = true and a.codPers = b.codPers and b.fechaDoc between '".$fDde."' and '".$fHta."' and e.identidad = d.identidad and d.idUsuarioFam = b.idUsuarioFam and a.idLegajoC = f.idLegajoC and c.idLegajoC = f.idLegajoC and f.idmotivointervencion = g.idmotivointervencion and f.idprocfuente = h.idprocfuente;";
        $stmt = $em->prepare($sql);
        try {
            $stmt->execute();
        } catch(\Exception $e) {
            $resu = ['ups_Error' => $e];
            return $resu;
        }

        $sql = "SELECT a.*, b.idCaso, b.casonro, b.casoActivo, b.egresoCaso, b.idmotivointervencion, b.motivodescs, b.idprocfuente, b.procfuentedesc into temp cons3 FROM cons1 a LEFT JOIN cons2 b ON a.idlegajoc = b.idlegajoc;";
        $stmt = $em->prepare($sql);
        try {
            $stmt->execute();
        } catch(\Exception $e) {
            $resu = ["ups_Error" => $e];
            return $resu;
        }

        $sql = "SELECT distinct * into temp cons44 FROM cons3;";
        $stmt = $em->prepare($sql);
        try {
            $stmt->execute();
        } catch(\Exception $e) {
            $resu = ["ups_Error" => $e];
            return $resu;
        }

        $sql = "create index tempcons44 on cons44 (codpers);";
        $stmt = $em->prepare($sql);
        try {
            $stmt->execute();
        } catch(\Exception $e) {
            $resu = ["ups_Error" => $e];
            return $resu;
        }

        $sql = "SELECT a.*, b.identidad_origen, b.entidadOrigen, b.fechamov, b.movimtipo, b.identidad_destino, b.entidadDestino into temp cons4 from cons44 a left join familia.vw_legmovimientos b on a.idLegajoC = b.idLegajoC and b.idLegajoC in (select distinct idLegajoC from cons44) and idLegMovim in (select max(idLegMovim) from familia.t_legmovims where idLegajoC in (select distinct idLegajoC from cons44) group by idLegajoC);";
        $stmt = $em->prepare($sql);
        try {
            $stmt->execute();
        } catch(\Exception $e) {
            $resu = ["ups_Error" => $e];
            return $resu;
        }

        $sql = "select distinct a.*, b.nrodoc, c.coddomic, c.ccodpais, desctg (c.ncodpais, c.ccodpais, 2) as pais, c.ccodprov, desctg (c.ncodprov, c.ccodprov, 2) as
        provincia, c.ccodmunic , desctg (c.ncodmunic, c.ccodmunic, 2) as municipio, c.ccoddpto, desctg (c.ncoddpto, c.ccoddpto, 2) as departamento, c.ccodloca, desctg
        (c.ncodloca, c.ccodloca, 2) as localidad , c.cbepo, desctg (c.nbepo, c.cbepo, 2) as barrio, c.ccodcalle, desctg (c.ncodcalle, c.ccodcalle, 2) as calle, puerta,
         piso, dpto, manz, block, lote, observacion into temp informe from cons4 a, tipdocxpers b, domicilio c, persxdomicilio d where a.codPers = b.codpers and a.codpers = d.codpers and c.coddomic = d.coddomic order by apellido, nombre;";

        $stmt = $em->prepare($sql);
        try {
            $stmt->execute();
        } catch(\Exception $e) {
            $resu = ["ups_Error" => $e];
            var_dump($resu);
            return $resu;
        }

        $sql = "select * from informe;";
        $stmt = $em->prepare($sql);
        try {
            $stmt->execute();
        } catch(\Exception $e) {
            $resu = ["ups_Error" => $e];
            return $resu;
        }

        $datos = $stmt->fetchAll();
        $response = new Response();
        $response = $this->render("::bdACSV.html.twig", array("listado" => $datos));
        $response->headers->set("Content-Type", "text/csv", "charset=UTF-8");
        $response->headers->set("Content-Disposition", "attachment; filename=SialeController.csv");
        $response->headers->set("Content-Description", "Exportación de datos");
        // Disable caching
        $response->headers->set("Cache-Control", "no-cache, no-store, must-revalidate"); // HTTP 1.1
        $response->headers->set("Pragma", "no-cache"); // HTTP 1.0
        $response->headers->set("Expires", "0"); // Proxies
        //
        // // $response = new Response(json_encode($datos));
        // // $response->headers->set("Content-Type", "application/json", "charset=UTF-8");
        // // $response->headers->set("Content-Description", "Exportación de datos");
        // // $response->headers->set("Content-Disposition", "attachment; filename="siale.json"");
        //
        $datos = null;
        return $response;
    }
}
