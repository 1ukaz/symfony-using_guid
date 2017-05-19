<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\OrderType;
use AppBundle\Entity\Order;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\PreloadedExtension;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\Platforms\MySqlPlatform;

class OrderTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = ["data" => "form testing dummy data"];

        $form = $this->factory->create(OrderType::class);

        $object = new Order();
        $object->setData($formData["data"]);

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

    protected function getExtensions()
    {
        // mock entity manager
        $entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['getClassMetadata', 'getRepository'])
            ->getMock()
        ;

        // this method will be mocked specific to the class name when provided
        // by the mocked repository below - this can be generic here
        $entityManager->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue(new ClassMetadata('entity')))
        ;

        $parent = $this;

        $entityManager->expects($this->any())
            ->method('getRepository')
            ->will($this->returnCallback(function ($entityName) use ($parent) {
                // This is a hack beacuse the shortcut name "AppBundle:Client" throws:
                // Doctrine\ORM\ORMException: Unknown Entity namespace alias 'AppBundle'. Exception
                $entityName = str_replace(":", "\\Entity\\", $entityName);
                // if ever the Doctrine\ORM\Query\Parser is engaged, it will check for
                // the existence of the fields used in the DQL using the class metadata
                $classMetadata = new ClassMetadata($entityName);

                if (preg_match('/[^a-z]Client$/i', $entityName)) {
                    $classMetadata->addInheritedFieldMapping(['fieldName' => 'name', 'columnName' => 'name']);
                }

                // mock statement
                $statement = $parent->getMockBuilder('\Doctrine\DBAL\Statement')
                    ->disableOriginalConstructor()
                    ->getMock()
                ;

                // mock connection
                $connection = $parent->getMockBuilder('\Doctrine\DBAL\Connection')
                    ->disableOriginalConstructor()
                    ->setMethods(['connect', 'executeQuery', 'getDatabasePlatform', 'getSQLLogger', 'quote'])
                    ->getMock()
                ;

                $connection->expects($parent->any())
                    ->method('connect')
                    ->will($parent->returnValue(true))
                ;

                $connection->expects($parent->any())
                    ->method('executeQuery')
                    ->will($parent->returnValue($statement))
                ;

                $connection->expects($parent->any())
                    ->method('getDatabasePlatform')
                    ->will($parent->returnValue(new MySqlPlatform()))
                ;

                $connection->expects($parent->any())
                    ->method('quote')
                    ->will($parent->returnValue(''))
                ;

                // mock unit of work
                $unitOfWork = $parent->getMockBuilder('\Doctrine\ORM\UnitOfWork')
                    ->disableOriginalConstructor()
                    ->getMock()
                ;

                // mock entity manager
                $entityManager = $parent->getMockBuilder('\Doctrine\ORM\EntityManager')
                    ->disableOriginalConstructor()
                    ->setMethods(['getClassMetadata', 'getConfiguration', 'getConnection', 'getUnitOfWork'])
                    ->getMock()
                ;

                $entityManager->expects($parent->any())
                    ->method('getClassMetadata')
                    ->will($parent->returnValue($classMetadata))
                ;

                $entityManager->expects($parent->any())
                    ->method('getConfiguration')
                    ->will($parent->returnValue(new Configuration()))
                ;

                $entityManager->expects($parent->any())
                    ->method('getConnection')
                    ->will($parent->returnValue($connection))
                ;

                $entityManager->expects($parent->any())
                    ->method('getUnitOfWork')
                    ->will($parent->returnValue($unitOfWork))
                ;

                // mock repository
                $repo = $parent->getMockBuilder('\Doctrine\ORM\EntityRepository')
                    ->setConstructorArgs([$entityManager, $classMetadata])
                    ->setMethods(['createQueryBuilder'])
                    ->getMock()
                ;

                $repo->expects($parent->any())
                    ->method('createQueryBuilder')
                    ->will($parent->returnCallback(function ($alias) use ($entityManager, $entityName) {
                        $queryBuilder = new QueryBuilder($entityManager);
                        $queryBuilder->select($alias)
                            ->from($entityName, $alias)
                        ;
                        return $queryBuilder;
                    }))
                ;

                return $repo;
            }))
        ;

        // mock registry
        $mockRegistry = $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->setMethods(['getManagerForClass'])
            ->getMock()
        ;

        $mockRegistry->expects($this->any())
            ->method('getManagerForClass')
            ->will($this->returnValue($entityManager))
        ;

        return [new PreloadedExtension(['client' => new EntityType($mockRegistry)], [])];
    }
}
