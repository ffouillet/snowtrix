<?php

namespace ST\TricksBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use ST\TricksBundle\Entity\Trick;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class TricksController extends Controller
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
     * @Route("/trick/{slug}", name="trick_view")
     */
    public function viewAction(Trick $trick)
    {
        return $this->render('snowtrix/tricks/view.html.twig',
            ['trick' => $trick]);
    }
}
