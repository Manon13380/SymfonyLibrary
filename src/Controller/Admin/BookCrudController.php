<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class BookCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Book::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title');
        yield TextField::new('author');
        yield TextField::new('publication')->onlyOnIndex();
        yield TextField::new('ISBN');
        yield ImageField::new('image')
            ->setBasePath('/uploads/photos')
            ->setLabel('Image')->onlyOnIndex();
        yield TextareaField::new('description')->onlyOnIndex();
        yield ArrayField::new('Genre')->onlyOnIndex();
        yield DateTimeField::new('createdAt')->onlyOnIndex();
        yield DateTimeField::new('updatedAt')->onlyWhenUpdating();
        yield TextField::new('slug')->onlyOnIndex();
        yield AssociationField::new('username');
    }
   
}
