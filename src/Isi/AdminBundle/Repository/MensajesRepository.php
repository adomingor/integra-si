<?php

namespace Isi\AdminBundle\Repository;

/**
 * MensajesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MensajesRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllMsjYTipoOrderTipo()
    {
        return $this->getEntityManager() ->createQuery(
            "SELECT m, t FROM IsiAdminBundle:Mensajes m JOIN m.tipoMensaje t ORDER BY t.descrip, m.titulo"
        )->getResult();
    }

    public function findMsById($id)
    {
        $cons = $this->getEntityManager()->createQuery("SELECT m, t FROM IsiAdminBundle:Mensajes m JOIN m.tipoMensaje t where m.id = ?1");
        $cons->setParameter(1, $id);
        return ($cons->getArrayResult());
    }
}