<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\Order;

class OrderListener
{
    public function prePersist(Order $order, LifecycleEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $connection = $entityManager->getConnection();

        $order->setCreatedAt(new \DateTime());
        $order->setOid(
            $connection->query(
                "SELECT {$connection->getDatabasePlatform()->getGuidExpression()}"
            )
            ->fetchColumn(0)
        );
    }

    public function preUpdate(Order $order, LifecycleEventArgs $args)
    {
        $order->setEditedAt(new \DateTime());
    }
}
