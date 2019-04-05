<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Company;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class CompanyAdmin extends AbstractAdmin
{
    public function toString($object)
    {
        return $object instanceof Company ? $object->getName() : 'Company';
    }

    // These lines configure which fields are displayed on the edit and create actions.
    // The FormMapper behaves similar to the FormBuilder of the Symfony Form component
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name')
            ->add('link', UrlType::class)
            ->add('location')
            ->add('location2')
            ->add('location3')
            ->add('location4')
            ->add('location5')
            ->add('yearFounded', ChoiceType::class, ['choices' => $this->getYearsRange()])
            ->add('size', ChoiceType::class, ['choices' => [
                'Self-employee' => '1',
                '11-50 employees' => '10',
                '51-200 employees' => '50',
                '201-500 employees' => '200',
                '501-1000 employees' => '500',
                '1001-5000 employees' => '1000',
                '5001-10,000 employees' => '5000',
                '10,001+ employees' => '10000',
            ]])
            ->add('email', EmailType::class)
            ->add('user', 'entity', [
                'class' => 'AppBundle\Entity\User',
                'choice_label' => 'email',
            ])
            ->add('industry')
            ->add('description', TextareaType::class)
            ->add('blocked')
            ->add('logo', FileType::class, [
                'data_class' => null,
                'required' => false
            ]);
    }

    // This method configures the filters, used to filter and sort the list of models;
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
    }

    // Here you specify which fields are shown when all models are listed
    // (the addIdentifier() method means that this field will link to the show/edit page of this particular model)
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name')
            ->addIdentifier('user')
            ->addIdentifier('link')
            ->addIdentifier('blocked')
            ->addIdentifier('industry');

    }

    protected $years = [];

    public function getYearsRange()
    {
        for ($i = date('Y'); $i >= 1800; $i--) {
            $this->years[$i] = $i;
        }

        return $this->years;
    }
}
