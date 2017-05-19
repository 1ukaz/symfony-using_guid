<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrderControllerTest extends WebTestCase
{
    public function testIndexAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Orders List")')->count());
    }

    public function testNewAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $em = $client->getContainer()->get("doctrine.orm.entity_manager");
        $clients = $em->getRepository("AppBundle:Client")->findAll();
        $crawler = $client->request('GET', '/orders/new');

        $form = $crawler->filter('form')->form();
        if (count($clients) > 0) {
            // If there are Clients in DDBB, we assign the first one to Order
            $form['order[client]']->select($clients[0]->getId());
        } else {
            $form['order[client]']->select(0);
        }
        $form->setValues(["order[data]" => "unit test text"]);
        $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        if (count($clients) > 0) {
            $this->assertContains("The Order has been created", $client->getResponse()->getContent());
        } else {
            // Since NO Client was selected in Dropdown, the Form is invalid
            $this->assertContains("This value is not valid.", $client->getResponse()->getContent());
        }
    }

    public function testEditAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $em = $client->getContainer()->get("doctrine.orm.entity_manager");
        $orders = $em->getRepository("AppBundle:Order")->findAll();
        $clients = $em->getRepository("AppBundle:Client")->findAll();
        $identifier = (count($orders) > 0) ? $orders[0]->getOid() : "123456";

        $crawler = $client->request("GET", "/orders/edit/{$identifier}");

        if (count($orders) > 0) {
            $form = $crawler->filter('form')->form();
            $form['order[client]']->select($clients[0]->getId());
            $form->setValues(["order[data]" => "unit test edited text"]);
            $client->submit($form);
        }

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        if (count($orders) == 0) {
            $this->assertContains("The Order Identifier &#039;123456&#039; is not valid", $client->getResponse()->getContent());
        } else {
            $this->assertContains("The Order has been modified", $client->getResponse()->getContent());
        }
    }

    public function testDeleteAction()
    {
        $client = static::createClient();

        $em = $client->getContainer()->get("doctrine.orm.entity_manager");
        $orders = $em->getRepository("AppBundle:Order")->findAll();
        $identifier = (count($orders) > 0) ? $orders[0]->getOid() : "123456";

        $crawler = $client->request("GET", "/orders/delete/{$identifier}");

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertArrayHasKey("status", json_decode($client->getResponse()->getContent(), true)["response"]);
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        if (count($orders) == 0) {
            $this->assertContains(
                "There was a problem at Order deletion! [The Order Identifier '123456' is not valid]",
                json_decode($client->getResponse()->getContent(), true)["response"]["msg"]
            );
        } else {
            $this->assertContains(
                "The Order '{$identifier}' has been deleted!",
                json_decode($client->getResponse()->getContent(), true)["response"]["msg"]
            );
        }
    }
}
