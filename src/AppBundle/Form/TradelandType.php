<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class TradelandType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
     public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, ['label' => 'Group Name'])
            ->add('summary', TextareaType::class, ['label' => 'Summary', 'attr' => ['rows' => '5']])
            ->add('description', TextareaType::class, ['label' => 'Description', 'attr' => ['rows' => '8']])
//            ->add('image', FileType::class, [
//                'label' => 'Upload image',
//                'data_class' => null,
//                'required' => false
//            ])
//            ->add('type', ChoiceType::class, [
//                    'choices' => [
//                        [
//                            'Public' => 'public',
//                            'Privacy' => 'rivacy',
//                        ],
//                    ], 'placeholder' => false, 'required' => false]
//            )
//            ->add('myNetwork', CheckboxType::class, [
//                'mapped' => false,
//                'label' => 'Add my network',
//                'required' =>false
//            ])
//            ->add('importFriends', CheckboxType::class, [
//                'mapped' => false,
//                'label' => 'Import My Friends',
//                'required' =>false
//            ])
//            ->add('inviteFriend', CheckboxType::class, [
//                'mapped' => false,
//                'label' => 'Invite a friend',
//                'required' =>false
//            ])
//            ->add('termsAccepted', CheckboxType::class, [
//                'mapped' => false,
//                'constraints' => new IsTrue(),
//                'label' => 'Agreement: Check to confirm you have read and accept the',
//                'required' =>true
//            ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Tradeland'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_tradeland';
    }


}
