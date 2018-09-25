<?php

namespace ST\TricksBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use ST\TricksBundle\Entity\Trick;
use ST\TricksBundle\Form\TrickCommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class TricksCommentsController extends Controller
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
     * @Route(name="trick_comments_view")
     */
    public function listAction(Trick $trick, $page = 1)
    {
        if ($page < 1) {
            throw $this->createNotFoundException("La page ".$page." n'existe pas.");
        }

        // Paginated comments
        $nbPerPage = 10; // For Comments pagination
        $trickComments = $this->em->getRepository('STTricksBundle:TrickComment')->findAllByTrickPaginated($trick, $page, $nbPerPage);
        $nbPages = ceil(count($trickComments)/$nbPerPage);

        return $this->render('snowtrix/tricks/comments/list.html.twig',
            ['trickComments' => $trickComments,
                'page' => $page,
                'nbPages' => $nbPages]);
    }

    /**
     * @Route("name="trick_comments_add")
     */
    public function addAction(Trick $trick)
    {
        $form = $this->createForm(TrickCommentType::class);

        return $this->render('snowtrix/tricks/view.html.twig',
            ['trick' => $trick]);
    }
}
