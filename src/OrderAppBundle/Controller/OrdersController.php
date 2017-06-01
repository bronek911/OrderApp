<?php

namespace OrderAppBundle\Controller;

use OrderAppBundle\Entity\Orders;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\True;

/**
 * Order controller.
 *
 * @Route("orders")
 */
class OrdersController extends Controller
{
    /**
     * Lists all order entities.
     *
     * @Route("/", name="orders_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $orders = $em->getRepository('OrderAppBundle:Orders')->findBy(['status' => true], ['id' => 'DESC']);
        $ordersDone = $em->getRepository('OrderAppBundle:Orders')->findBy(['status' => false], ['id' => 'DESC']);

        return $this->render('orders/index.html.twig', array(
            'orders' => $orders,
            'ordersDone' => $ordersDone,
        ));
    }

    /**
     * Creates a new order entity.
     *
     * @Route("/new", name="orders_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $order = new Orders();
        $form = $this->createForm('OrderAppBundle\Form\OrdersType', $order);
        $form->handleRequest($request);



        if ($request->getMethod() === "POST") {


            $clientId = $request->get('client');
            $productsArray = $request->get('product');
            $quantityArray = $request->get('quantity');
            $priceArray = $request->get('price');
            $sumArray = $request->get('sum');
            $orderArray = [];


            for ($i = 0; $i < count($productsArray); $i++) {
                $orderArray[$i] = [];
                $orderArray[$i]['name'] = $productsArray[$i];
                $orderArray[$i]['quantity'] = $quantityArray[$i];
                $orderArray[$i]['price'] = $priceArray[$i];
                $orderArray[$i]['sum'] = $sumArray[$i];
            }

            $sum = 0;
            $productsPrices = [];

            for ($i = 0; $i < count($orderArray); $i++) {

                $productsPrices[$i] = [];

                //Sprawdzić która cena ma bnyć przesłana
                $productsPrices[$i] = $orderArray[$i]['sum'];

                $sum += $productsPrices[$i];
            }


            $clientId = $request->get('client');
            $client = $this->getDoctrine()->getRepository('OrderAppBundle:Clients')->find($clientId);
            $clientCompany = $client->getCompany();


            $order->setFullOrder(json_encode($orderArray));
            $order->setClient($client);
            $order->setTotalPrice($sum);

            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();

            return $this->redirectToRoute('orders_show', array(
                'id' => $order->getId(),
                'company' => $clientCompany,
                'products' => $orderArray,
                'prices' => $productsPrices,
            ));
        }

        $productsList = $this->getDoctrine()->getRepository('OrderAppBundle:Products')->findAll();
        $clients = $this->getDoctrine()->getRepository('OrderAppBundle:Clients')->findAll();

        return $this->render('orders/new.html.twig', array(
            'order' => $order,
            'form' => $form->createView(),
            'products' => $productsList,
            'clients' => $clients,
        ));
    }

    /**
     * Finds and displays a order entity.
     *
     * @Route("/{id}", name="orders_show")
     * @Method("GET")
     */
    public function showAction(Orders $order)
    {

        $deleteForm = $this->createDeleteForm($order);
        $array = json_decode($order->getFullOrder(), true);
        $productsPrices = [];

        for ($i = 0; $i < count($array); $i++) {
            foreach ($array[$i] as $key => $val) {
                $product = $this->getDoctrine()->getRepository('OrderAppBundle:Products')->findOneBy(['name' => $key]);
                $productsPrices[$i] = $array[$i]['sum'];
            }
        }

        return $this->render('orders/show.html.twig', array(
            'order' => $order,
            'delete_form' => $deleteForm->createView(),
            'products' => $array,
            'prices' => $productsPrices,
            'sum' => $order->getTotalPrice(),
        ));
    }

    /**
     * Displays a form to edit an existing order entity.
     *
     * @Route("/{id}/edit", name="orders_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Orders $order)
    {
        $deleteForm = $this->createDeleteForm($order);
        $editForm = $this->createForm('OrderAppBundle\Form\OrdersType', $order);
        $editForm->handleRequest($request);

        $orderArray = json_decode($order->getFullOrder(), true);
        $productsList = $this->getDoctrine()->getRepository('OrderAppBundle:Products')->findAll();

        if ($request->getMethod() === "POST") {

            $productsArray = $request->get('product');
            $quantityArray = $request->get('quantity');

            $orderArray = [];

            for ($i = 0; $i < count($productsArray) - 1; $i++) {
                $orderArray[$i] = [];
                $orderArray[$i][$productsArray[$i]] = $quantityArray[$i];
            }

            $sum = 0;
            $productsPrices = [];

            for ($i = 0; $i < count($orderArray); $i++) {
                foreach ($orderArray[$i] as $key => $val) {
                    $product = $this->getDoctrine()->getRepository('OrderAppBundle:Products')->findOneBy(['name' => $key]);
                    $productsPrices[$i] = $val * $product->getPrice();
                    $sum += $productsPrices[$i];
                }
            }

            $order->setFullOrder(json_encode($orderArray));
            $order->setTotalPrice($sum);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('orders_edit', array('id' => $order->getId()));
        }

        return $this->render('orders/edit.html.twig', array(
            'order' => $order,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'products' => $orderArray,
            'productsList' => $productsList,
        ));
    }

    /**
     * Deletes a order entity.
     *
     * @Route("/{id}", name="orders_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Orders $order)
    {
        $form = $this->createDeleteForm($order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($order);
            $em->flush();
        }

        return $this->redirectToRoute('orders_index');
    }

    /**
     * Deletes a order entity.
     *
     * @Route("/move/{id}", name="order_move")
     * @Method("POST")
     */
    public function moveAction(Request $request, Orders $order)
    {

        if ($request->getMethod() === "POST") {

            if ($order->getStatus() == 1) {
                $order->setStatus(0);
            } elseif ($order->getStatus() == 0) {
                $order->setStatus(1);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }

        return $this->redirectToRoute('orders_index');
    }


    public function lastTenOrdersAction()
    {
        $lastOrders = $this->getDoctrine()->getManager()->getRepository('OrderAppBundle:Orders')->findBy([], ['id' => 'DESC'], 10);
        $noOfUndoneOrders = count($this->getDoctrine()->getManager()->getRepository('OrderAppBundle:Orders')->findBy(['status' => 1]));

        return $this->render('notifications.html.twig', array(
            'lastOrders' => $lastOrders,
            'noOfUndoneOrders' => $noOfUndoneOrders,
        ));
    }


    /**
     * Creates a form to delete a order entity.
     *
     * @param Orders $order The order entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Orders $order)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('orders_delete', array('id' => $order->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
