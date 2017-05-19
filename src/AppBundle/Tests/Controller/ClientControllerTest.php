<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ClientControllerTest extends WebTestCase
{
    public function testNewActionError()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/clients/new');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Client Form")')->count());

        $form = $crawler->filter('form')->form();
        // Submit the form with the required input empty [Will trigger the Error]
        $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains("Client name can not be empty", $client->getResponse()->getContent());
    }

    public function testNewAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/clients/new');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Client Form")')->count());

        $form = $crawler->filter('form')->form();
        $form->setValues(["client[name]" => "test name"]);
        $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains("The Client has been created", $client->getResponse()->getContent());
    }
}
