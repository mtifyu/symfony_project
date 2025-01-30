<?php

namespace App\Form;

use App\Entity\Application;
use App\Entity\Stock;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // Добавили этот импорт
use Symfony\Component\Form\Extension\Core\Type\NumberType; // Для числовых полей
use Symfony\Component\Form\Extension\Core\Type\TextType;   // Для текстовых полей
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('action', ChoiceType::class, [
                'choices' => [
                    'Buy' => 'buy',
                    'Sell' => 'sell',
                ],
                'label' => 'Action'
            ])
            ->add('quantity')
            ->add('price')
            ->add('status')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('stock', EntityType::class, [
                'class' => Stock::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Application::class,
        ]);
    }
}
