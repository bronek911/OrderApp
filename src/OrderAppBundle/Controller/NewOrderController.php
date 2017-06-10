<?php

namespace OrderAppBundle\Controller;

use OrderAppBundle\Entity\Orders;
use OrderAppBundle\Entity\Clients;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class NewOrderController extends Controller
{

    /**
     * @Route("/search", name="orders_search")
     *
     */
    public function searchClientAction(Request $request)
    {

        $term = $request->get('term', null);
        $repository = $this->getDoctrine()->getManager()->getRepository('OrderAppBundle:Clients');
        if ($term) {
            $companies = $repository->createQueryBuilder('c')
                ->where("c.company LIKE :term")
                ->setParameter('term', '%'.$term.'%')
                ->getQuery()
                ->getResult();

        } else {
            $companies = $repository->findAll();
        }
        $list = array();
        foreach ($companies as $company) {
            array_push($list, array('label' => $company->getCompany(), 'value' => $company->getCompany()));
        }

        return new JsonResponse($list, 200);
    }

}
