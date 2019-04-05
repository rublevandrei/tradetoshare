<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Tradeland;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TradelandAdmin extends AbstractAdmin
{
    public function toString($object)
    {
        return $object instanceof Tradeland ? $object->getName() : 'Tradeland';
    }

    // These lines configure which fields are displayed on the edit and create actions.
    // The FormMapper behaves similar to the FormBuilder of the Symfony Form component
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name')
            ->add('summary', TextareaType::class)
            ->add('description', TextareaType::class)
            ->add('blocked')
            ->add('owner', 'entity', [
                'class' => 'AppBundle\Entity\User',
                'choice_label' => 'email',
            ]);
//        ->add('owner', 'sonata_type_model', [
//        'class' => 'AppBundle\Entity\User',
//        'property' => 'email',
//    ]);
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
            ->addIdentifier('blocked')
            ->addIdentifier('summary');
    }
}