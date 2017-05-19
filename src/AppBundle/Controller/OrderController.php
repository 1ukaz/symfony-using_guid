<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order;
use AppBundle\Form\OrderType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/orders")
 */
class OrderController extends Controller
{
    /**
     * @Route("/", name="home_orders")
     * @Template("AppBundle:Order:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        try {
            $orders = $this->getDoctrine()->getRepository('AppBundle:Order')->findAllEnabled();

            if (count($orders) == 0) {
                $this->addFlash("info", "There is no data to display.");
            } else {
                $this->addFlash("info", "You can delete Orders, add Clients and add new Orders!");
            }

            return ["orders" => $orders];
        } catch (\Exception $e) {
            $this->addFlash("danger", "There was a problem retrieving the Orders: [{$e->getMessage()}]");
            return ["orders" => []];
        }
    }

    /**
     * @Route("/new", name="new_order")
     * @Template("AppBundle:CRUD:form.html.twig")
     */
    public function newOrderAction(Request $request)
    {
        $form = $this->createForm(OrderType::class, new Order());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $order = $form->getData();

                $em = $this->getDoctrine()->getManager();
                $em->persist($order);
                $em->flush();

                $this->addFlash("success", "The Order has been created");
            } catch (\Exception $e) {
                $this->addFlash("danger", "There has been an Issue at the Order creation! [{$e->getMessage()}]");
            } finally {
                return $this->redirectToRoute('home_orders');
            }
        }

        return ["form" => $form->createView(), "entity" => "Order"];
    }

    /**
     * @Route("/edit/{guid}", requirements={"guid" = "([a-z0-9-]*)"}, defaults={"guid" = null}, name="edit_order")
     * @Template("AppBundle:CRUD:form.html.twig")
     */
    public function editOrderAction($guid, Request $request)
    {
        $order = $this->getDoctrine()->getRepository('AppBundle:Order')->findOneByOid($guid);

        if (is_null($order)) {
            $this->addFlash("danger", "The Order Identifier '{$guid}' is not valid");
            return ["form" => null, "entity" => "Order"];
        }

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $order = $form->getData();

                $em = $this->getDoctrine()->getManager();
                $em->persist($order);
                $em->flush();

                $this->addFlash("success", "The Order has been modified");
            } catch (\Exception $e) {
                $this->addFlash("danger", "There has been an Issue at the Order modification! [{$e->getMessage()}]");
            } finally {
                return $this->redirectToRoute('home_orders');
            }
        }

        return ["form" => $form->createView(), "entity" => "Order"];
    }

    /**
     * @Route("/delete/{guid}", requirements={"guid" = "([a-z0-9-]*)"}, defaults={"guid" = null}, name="delete_order")
     */
    public function deleteOrderAction($guid)
    {
        try {
            $response = new JsonResponse();
            $order = $this->getDoctrine()->getRepository('AppBundle:Order')->findOneByOid($guid);

            if (is_null($order)) {
                throw new \Exception("The Order Identifier '{$guid}' is not valid");
            }

            $order->setEnabled(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();

            $response->setData(["response" => ['status' => 200, 'class' => "success", "msg" => "The Order '{$guid}' has been deleted!"]]);
        } catch (\Exception $e) {
            $response->setData(["response" => ['status' => 500, 'class' => "danger", "msg" => "There was a problem at Order deletion! [{$e->getMessage()}]"]]);
        } finally {
            return $response;
        }
    }
}
