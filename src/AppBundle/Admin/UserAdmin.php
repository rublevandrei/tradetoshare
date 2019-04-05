<?php
namespace AppBundle\Admin;

use AppBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserAdmin extends AbstractAdmin
{
    public function toString($object)
    {
        return $object instanceof User ? $object->getName() : 'User';
    }

    // These lines configure which fields are displayed on the edit and create actions.
    // The FormMapper behaves similar to the FormBuilder of the Symfony Form component
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('email', EmailType::class)
            ->add('name')
            ->add('password', PasswordType::class)
            ->add('position')
            ->add('lastDiploma')
            ->add('organization')
            ->add('educationYear', ChoiceType::class, ['choices' => $this->getYearsRange()])
            ->add('location')
            ->add('link')
            ->add('avatar', FileType::class, [
                'data_class' => null,
                'required' => false
            ]) ;
    }

    // This method configures the filters, used to filter and sort the list of models;
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('email');
    }

    // Here you specify which fields are shown when all models are listed
    // (the addIdentifier() method means that this field will link to the show/edit page of this particular model)
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('email')
            ->addIdentifier('name');
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