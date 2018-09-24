<?php

namespace ST\TricksBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class TricksGroupsController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Route("/", name="snowtrix_home")
     */
    public function indexAction()
    {
        // Get all tricks groups and associated tricks.
        $tricksGroups = $this->em->getRepository('STTricksBundle:TrickGroup')->findAllWithRelations('tricks');

        dump($tricksGroups);

        return $this->render('snowtrix/index.html.twig');
    }
}
