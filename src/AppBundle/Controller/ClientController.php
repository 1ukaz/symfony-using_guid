<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Form\ClientType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/clients")
 */
class ClientController extends Controller
{

    /**
     * @Route("/new", name="new_client")
     * @Template("AppBundle:CRUD:form.html.twig")
     */
    public function newClientAction(Request $request)
    {
        $form = $this->createForm(ClientType::class, new Client());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $client = $form->getData();

                $em = $this->getDoctrine()->getManager();
                $em->persist($client);
                $em->flush();

                $this->addFlash("success", "The Client has been created");
            } catch (\Exception $e) {
                $this->addFlash("danger", "There has been an Issue at the Client creation! [{$e->getMessage()}]");
            } finally {
                return $this->redirectToRoute('home_orders');
            }
        }

        return ["form" => $form->createView(), "entity" => "Client"];
    }
}
