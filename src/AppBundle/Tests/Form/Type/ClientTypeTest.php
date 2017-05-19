<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\ClientType;
use AppBundle\Entity\Client;
use Symfony\Component\Form\Test\TypeTestCase;

class ClientTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = ["name" => "form name"];

        $form = $this->factory->create(ClientType::class);

        $object = new Client();
        $object->setName($formData["name"]);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
