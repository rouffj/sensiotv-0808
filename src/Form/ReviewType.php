<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rating', Type\NumberType::class)
            ->add('body', Type\TextareaType::class)
            //->add('email', Type\EmailType::class)
            ->add('submit', Type\SubmitType::class, [
                'label' => 'Submit my review'
            ])
        ;

/*
        if (!$this->authorizationChecker->isGranted('ROLE_USER')) {
            $builder->add('email', Type\EmailType::class);
        } else {
            $builder->add('email', Type\EmailType::class, [
                'data' => $tokenStorage->getToken()->getUser()
            ]);
        }
*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //'data_class' => Review::class,
        ]);
    }
}
