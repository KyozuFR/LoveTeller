<?php

namespace App\Command;

use App\Repository\LovemessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'CleanupLovemessages',
    description: 'Add a short description for your command',
)]
class CleanupLovemessagesCommand extends Command
{
    private LovemessageRepository $lovemessageRepository;
    private EntityManagerInterface $em;

    public function __construct(LovemessageRepository $lovemessageRepository, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->lovemessageRepository = $lovemessageRepository;
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->em->getRepository('App\Entity\User')->findAll();

        foreach ($users as $user) {
            $lovemessage = $this->lovemessageRepository
                ->createQueryBuilder('lm')
                ->andWhere(':user MEMBER OF lm.users')
                ->setParameter('user', $user)
                ->orderBy('lm.id', 'ASC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($lovemessage) {
                $lovemessage->removeUser($user);
                $this->em->persist($lovemessage);
            }
        }

        $this->em->flush();

        // Suppression des messages sans user ayant le rôle loved
        $lovemessages = $this->lovemessageRepository->findAll();
        foreach ($lovemessages as $lovemessage) {
            $hasLoved = false;
            foreach ($lovemessage->getUsers() as $user) {
                if (in_array('loved', $user->getRoles(), true) || in_array('ROLE_LOVED', $user->getRoles(), true)) {
                    $hasLoved = true;
                    break;
                }
            }
            if (!$hasLoved) {
                $this->em->remove($lovemessage);
            }
        }

        $this->em->flush();

        $output->writeln('Nettoyage terminé.');
        return Command::SUCCESS;
    }
}
