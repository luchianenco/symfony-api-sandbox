<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 19/02/2017
 * Time: 17:50
 */

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('topic', EntityType::class, [
                'class' => 'AppBundle\Entity\Topic',
                'choice_label' => 'title',
                'multiple' => false,
                'required' => true
            ])
            ->add('title', TextType::class)
            ->add('author', TextType::class)
            ->add('text', TextType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Article',
            'csrf_protection' => false
        ]);
    }
}
