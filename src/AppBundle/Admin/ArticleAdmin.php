<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Article;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ArticleAdmin extends AbstractAdmin
{
    public function toString($object)
    {
        return $object instanceof Article ? $object->getTitle() : 'Article';
    }

    // These lines configure which fields are displayed on the edit and create actions.
    // The FormMapper behaves similar to the FormBuilder of the Symfony Form component
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('title')
            ->add('description')
            ->add('approved')
            ->add('allow_comments', CheckboxType::class)
            ->add('user', 'entity', [
                'class' => 'AppBundle\Entity\User',
                'choice_label' => 'email',
            ])
//            ->add('created_at', DateTimeType::class)
//            ->add('updated_at')
        ;
    }

    // This method configures the filters, used to filter and sort the list of models;
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title');
    }

    // Here you specify which fields are shown when all models are listed
    // (the addIdentifier() method means that this field will link to the show/edit page of this particular model)
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('title')
            ->addIdentifier('user')
            ->addIdentifier('approved')
            ->addIdentifier('allow_comments')

            //     ->addIdentifier('created_at')
            //     ->addIdentifier('updated_at')
        ;
    }

    // Fields to be shown on show action
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('description')

        ;
    }
}