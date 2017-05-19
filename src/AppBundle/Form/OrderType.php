<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Order;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("data", TextType::class, ["attr" => ["class" => "form-control"]])
            ->add(
                "client",
                EntityType::class, [
                  "class" => "AppBundle:Client",
                  "choice_label" => "name",
                  "choice_value" => "id",
                  "placeholder" => "Choose a Client",
                  "attr" => ["class" => "form-control"],
                ]
            )
            ->add('save', SubmitType::class, ['label' => 'Save', 'attr' => ['class' => 'btn btn-primary']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Order::class]);
    }
}
