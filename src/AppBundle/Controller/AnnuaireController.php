<?php
/**
 * Created by PhpStorm.
 * User: Mbape
 * Date: 21/02/2018
 * Time: 14:51
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Plat;

class AnnuaireController extends Controller
{
    /**
     * @Route("/annuaire/fiche", name="plat_fiche")
     * @Method({"GET"})
     */
    public function ficheAction(Request $request)
    {
        // Récupère paramètre de mon url
        $idPlat = $request->query->get('id');

        $em = $this->getDoctrine()->getManager();



        // Cherche plat grâce au nom
        $plat = $em->getRepository('AppBundle:Plat')->findOneBy(array('id' => $idPlat)); // Renvoie un objet

        $user = $plat->getUserPoste();

        $listCom = $em->getRepository('AppBundle:Commentaire')->findByPage(
            $request->query->getInt('page', 1),
            4,
            $user
        );

        if ($plat != null) {
            return $this->render('plat/fiche.html.twig', array(
                'plat' => $plat,
                'listCom' => $listCom
            ));
        } else {
            return $this->render('menu.html.twig');
        }
    }
}