<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\IsTrue;

class CompanyType extends AbstractType
{
    protected $years = [];

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Company'
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, ['label' => 'Company Name'])
            ->add('link', UrlType::class, ['label' => 'Company Website URL'])
            ->add('size', ChoiceType::class, ['choices' => [
                'Self-employee' => '1',
                '11-50 employees' => '10',
                '51-200 employees' => '50',
                '201-500 employees' => '200',
                '501-1000 employees' => '500',
                '1001-5000 employees' => '1000',
                '5001-10,000 employees' => '5000',
                '10,001+ employees' => '10000',
            ], 'placeholder' => false,
                'label' => 'Company Size', 'required' => false
            ])
            ->add('yearFounded', ChoiceType::class, ['choices' => $this->getYearsRange()
                , 'label' => 'Year Founded'])
            ->add('location', null, ['label' => 'Company Locations', 'attr' => ['class'=>'active location']])
            ->add('location2', null, ['attr' => ['class'=>'none_show location']])
            ->add('location3', null, ['attr' => ['class'=>'none_show location']])
            ->add('location4', null, ['attr' => ['class'=>'none_show location']])
            ->add('location5', null, ['attr' => ['class'=>'none_show location']])
            ->add('email', EmailType::class, ['label' => 'Company email address', 'required' => true])
            ->add('industry', null, ['label' => 'Main Company Industry', 'required' => true])
            ->add('description', TextareaType::class, ['label' => 'Description', 'attr' => ['maxlength' => "2000"]])
            ->add('logo', HiddenType::class, ['label' => null]);
        if (is_null($builder->getData()->getId())) {
            $builder->add('termsAccepted', CheckboxType::class, [
                'mapped' => false,
                'constraints' => new IsTrue()
            ]);
        }
    }

    public function getYearsRange()
    {
        for ($i = date('Y'); $i >= 1800; $i--) {
            $this->years[$i] = $i;
        }

        return $this->years;
    }
}

