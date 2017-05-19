<?php

namespace AppBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderRepositoryFunctionalTest extends KernelTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }

    public function testFindAllEnabled()
    {
        $orders = $this->em
            ->getRepository('AppBundle:Order')
            ->findAllEnabled()
        ;

        $this->assertGreaterThan(0, count($orders));
    }
}
