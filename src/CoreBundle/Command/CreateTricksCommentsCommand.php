<?php

namespace CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use TricksBundle\Entity\TrickComment;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateTricksCommentsCommand extends Command
{

    private $em;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder; // Doctrine EntityManager

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('snowtrix:createTricksCommand')
        ->setDescription('Add a random number [5 to 15] of comments to existings tricks with random users selected in DB');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->em->getRepository('UserBundle:User')->findAll();

        // Remove demo account, he will not post comments.
        foreach($users as $key => $user) {
            if($user->getUsername() == 'Demo') {
                unset($users[$key]);
            }
        }

        sort($users); // Reorganize array.

        $tricks = $this->em->getRepository('TricksBundle:Trick')->findAll();

        // 'Default' comments that will be added.
        $commentsListTemplate = ['Wow ! Impressionnant, il doit falloir du temps pour arriver à maîtriser cette figure.',
            'Chouette figure !',
            'Je n’ai jamais réussi à faire cette figure pour le moment, je vais continuer à m’entraîner.',
            'Impressionnant ! ',
            'Quelle performance !',
            'Vivement la saison prochaine que je puisse essayer de faire cette figure.',
            'Comment a t’il réussi à faire cette figure ?!',
            'Est ce que quelqu’un pourrait m’aider à faire cette figure ? Envoyez moi un message privé.',
            'Cette figure est assez simple à réaliser, il suffit juste d’être patient et de persévérer, courage à tous !',
            'Je demanderais à mon prof’ de m’aider à réaliser cette figure.',
            'Pour l’instant je m’essaye au Slides, cette figure me plaît bien, je verrais pour l’apprendre dès que j’aurais réussi à faire un superbe slide.',
            'Pour l’instant je m’essaye au Grabs, cette figure me plaît bien, je verrais pour l’apprendre dès que j’aurais réussi à faire un superbe grab.',
            'Qui a déjà réussi à faire un 1080° ?',
            'Superbe ! ',
            'Elle a pas l’air facile à faire ! ',
            'J’aimerais bien arriver à faire cette figure un jour !!',
            'Bravo ! ',
            'La figure est maîtrisée, ça se voit ! ',
            'Vivement que je sache réaliser cette figure.'
            ];

        // Start adding comments.
        foreach($tricks as $key => $trick) {

            $commentList = $commentsListTemplate;
            $nbrOfCommentsToAdd = mt_rand(3,12);
            $nbrOfUsers = sizeof($users) - 1;

            $timeStampStartDateForCommentDate = 1532425168;
            $timeStampEndDateForCommentDate = 1540201168;

            for($i = 0; $i <= $nbrOfCommentsToAdd; $i++) {

                $selectedCommentIndex = mt_rand(0,(sizeof($commentList) -1));
                $userAddingComment = $users[mt_rand(0,$nbrOfUsers)];

                $commentDate = new \DateTime();
                $commentDate->setTimestamp(mt_rand($timeStampStartDateForCommentDate,$timeStampEndDateForCommentDate));

                $trickComment = new TrickComment();
                $trickComment->setTrick($trick);
                $trickComment->setUser($userAddingComment);
                $trickComment->setContent($commentList[$selectedCommentIndex]);
                $trickComment->setCreatedAt($commentDate);

                unset($commentList[$selectedCommentIndex]);
                sort($commentList);

                $this->em->persist($trickComment);
            }

            $this->em->flush();
        }
    }
}