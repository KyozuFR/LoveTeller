<?php

namespace App\Controller\Lover;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;

class UserCrudController extends AbstractCrudController
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('email'),
            ArrayField::new('roles')->onlyOnIndex(),
            TextField::new('password')->onlyWhenCreating(),
        ];
    }

    public function createIndexQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null): QueryBuilder
    {
        $user = $this->security->getUser();
        $qb = parent::createIndexQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        // Affiche l'utilisateur connecté et ceux qu'il a en receiver
        $qb->andWhere('entity = :me OR :me MEMBER OF entity.receiver')
            ->setParameter('me', $user);

        return $qb;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            return;
        }

        // Ajoute le rôle Loved
        $roles = $entityInstance->getRoles();
        if (!in_array('ROLE_LOVED', $roles, true)) {
            $roles[] = 'ROLE_LOVED';
            $entityInstance->setRoles($roles);
        }

        // Lie l'utilisateur à l'admin connecté
        $admin = $this->security->getUser();
        if ($admin instanceof User && $admin !== $entityInstance) {
            $admin->addUser($entityInstance);
        }

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }
}
