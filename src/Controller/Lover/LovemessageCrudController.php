<?php

namespace App\Controller\Lover;

use App\Entity\Lovemessage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;

class LovemessageCrudController extends AbstractCrudController
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public static function getEntityFqcn(): string
    {
        return Lovemessage::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('message'),
            AssociationField::new('users')->hideWhenUpdating()->hideOnIndex()->setQueryBuilder(function ($queryBuilder) {
                // $queryBuilder est une instance de Doctrine\ORM\QueryBuilder
                // $this->security est accessible via use ($this)
                $admin = $this->security->getUser();
                return $queryBuilder
                    ->andWhere(':admin MEMBER OF entity.receiver')
                    ->setParameter('admin', $admin);
            }),
            ArrayField::new('users')
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getUsers()->map(fn($user) => $user->getEmail())->toArray());
                })
                ->onlyOnIndex(),
        ];
    }

    public function createIndexQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null): QueryBuilder
    {
        $user = $this->security->getUser();
        $qb = parent::createIndexQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        // Affiche seulement les messages associés à l'admin connecté
        $qb->andWhere(':me MEMBER OF entity.users')
            ->setParameter('me', $user);

        return $qb;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Lovemessage) {
            return;
        }

        $admin = $this->security->getUser();
        if ($admin && !$entityInstance->getUsers()->contains($admin)) {
            $entityInstance->addUser($admin);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

}
