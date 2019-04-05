<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Tradeland;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;


class NotificationAdmin extends AbstractAdmin
{
    public function toString($object)
    {
        return $object instanceof Tradeland ? $object->getUser()->getFullName() : 'Notification';
    }

    // These lines configure which fields are displayed on the edit and create actions.
    // The FormMapper behaves similar to the FormBuilder of the Symfony Form component
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            //->add('data', 'array')
            ->add('type')
            ->add('user', 'entity', [
                'class' => 'AppBundle\Entity\User',
                'choice_label' => 'email',
            ]);
    }

    // This method configures the filters, used to filter and sort the list of models;
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
   //     $datagridMapper->add('data');
    }

    // Here you specify which fields are shown when all models are listed
    // (the addIdentifier() method means that this field will link to the show/edit page of this particular model)
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('user')
            ->addIdentifier('type')
        //    ->addIdentifier('data', 'array')
            ->addIdentifier('created_at', 'datetime')
            ;
    }
}