<?php

namespace TricksBundle\Controller;

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
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        // Get all tricks groups and associated tricks.
        $tricksGroups = $this->em->getRepository('TricksBundle:TrickGroup')->findAllWithTricks();

        return $this->render('snowtrix/index.html.twig',
            ['tricksGroups' => $tricksGroups]);
    }
}
