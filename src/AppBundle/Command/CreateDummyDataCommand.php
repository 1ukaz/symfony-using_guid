<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\Order;
use AppBundle\Entity\Client;

class CreateDummyDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:database:populate')
            ->setDescription('Populates DDBB with Dummy Data to play around. [By Lucas Guegnolle]')
            ->setHelp('This command allows you to populate DDBB with Dummy Data to play around.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $buffer = new SymfonyStyle($input, $output);
        $response = $buffer->confirm('Would you like me to insert a Dummy data set for a quick start?', true);

        if (true === $response) {
            $buffer->title("Let's insert some Dummy Data to Play around!");

            $buffer->section("First insert some Clients (5)");
            $clientsCount = $this->insertClients($buffer, $em, $output);

            sleep(3);

            $buffer->section("Let's assign them some Orders to display (20)");
            $this->insertOrders($buffer, $em, $output, $clientsCount);

            sleep(2);

            $buffer->note("Finished inserting Dummy Data!");
            $buffer->note("Go around and play a little bit with the application!");
            $buffer->note("Have fun!");
        }
    }

    protected function insertOrders($bfr, $em, $out, $max = 5)
    {
        $ordersProgress = new ProgressBar($out, 20);
        $ordersProgress->start();
        $ordersProgress->setFormat('debug');

        for ($i = 1; $i < 21; $i++) {
            try {
                $client = $em->getRepository("AppBundle:Client")->find(rand(1, $max));

                $order = new Order();
                $order->setData("Some dummy data to persist a test element #{$i}");
                $order->setClient($client);

                $em->persist($order);
                $ordersProgress->advance(1);
                $bfr->text("=> Order #{$i} for Client '{$client->getName()}' has been inserted!");
            } catch (\Exception $e) {
                $bfr->error("There was a problem inserting the Order #{$i}: {$e->getMessage()}");
            } finally {
                sleep(2);
            }
        }
        $em->flush();
        $em->clear();
        $bfr->newLine(3);
        $bfr->success("Finished inserting the Orders!");

        return;
    }

    protected function insertClients($bfr, $em, $out)
    {
        $clientsProgress = new ProgressBar($out, 5);
        $clientsProgress->start();
        $clientsProgress->setFormat('debug');

        $clients = ["Tony Stark", "Bruce Banner", "Steve Rogers", "Matt Murdock", "Peter Parker"];
        foreach ($clients as $cl) {
            try {
                $client = new Client();
                $client->setName($cl);

                $em->persist($client);
                $clientsProgress->advance(1);
                $bfr->text("=> Client '{$cl}' inserted!");
            } catch (\Exception $e) {
                $bfr->error("There was a problem inserting the Client '{$cl}': {$e->getMessage()}");
            } finally {
                sleep(2);
            }
        }
        $em->flush();
        $em->clear();
        $bfr->newLine(3);
        $bfr->success("Finished inserting the Clients");

        $bfr->newLine(1);

        return count($clients);
    }
}
