<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
  public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, ['label' => 'Full Name'])
            ->add('position', null, ['label' => 'Position'])
            ->add('lastDiploma', null, ['label' => 'Last Diploma'])
            ->add('organization', null, ['label' => 'Organization'])
            ->add('educationYear', ChoiceType::class, ['choices' => $this->getYearsRange(),
                'label' => 'Last year of education'
            ])
            ->add('location', null, ['label' => 'Location', 'attr' => ['class'=>'location']])
            ->add('email', EmailType::class, ['label' => 'Your email address at company'])
            ->add('link', null, ['label' => 'Link'])
            ->add('avatar', FileType::class, [
                'label' => 'Upload/Change Your Avatar',
                'data_class' => null,
                'required' => false
            ])
            ->add('about', TextareaType::class, ['label' => 'About me',   'required' =>false])
            ->add('summary', TextareaType::class, ['label' => 'Summary',   'required' =>false])
        ;

    }


    public function getYearsRange()
    {
        for ($i = date('Y'); $i >= 1800; $i--) {
            $this->years[$i] = $i;
        }

        return $this->years;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }


}
