<?php

namespace ST\TricksBundle\Controller;

use CoreBundle\Form\Handler\CommentFormHandler;
use CoreBundle\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ST\TricksBundle\Entity\Trick;
use ST\TricksBundle\Entity\TrickComment;
use ST\TricksBundle\Form\Handler\TrickFormHandler;
use ST\TricksBundle\Form\TrickType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
    public function viewAction(Request $request, Trick $trick, $commentsPage = 1, CommentFormHandler $commentFormhandler)
    {
        // commentsPage param could be passed via GET
        if (null !== $request->query->get('commentsPage') && intval($request->query->get('commentsPage')) > 1) {
            $commentsPage = $request->query->get('commentsPage');
        }

        // Comment add form
        $trickComment = new TrickComment();
        $trickComment->setTrick($trick);
        $commentForm = $this->createForm(CommentType::class, $trickComment);

        if ($commentFormhandler->handle($request, $commentForm, $this->getUser())) {
            // Empty form data after submission
            $commentForm = $this->createForm(CommentType::class, new TrickComment());
        }

        // Paginated comments
        $nbCommentsPerPage = 10; // For Comments pagination
        $trickComments = $this->em->getRepository('STTricksBundle:TrickComment')->findAllByTrickPaginated($trick, $commentsPage, $nbCommentsPerPage);
        $nbPagesComments = ceil(count($trickComments)/$nbCommentsPerPage);

        return $this->render('snowtrix/tricks/view.html.twig',
            ['trick' => $trick,
                'commentForm' => $commentForm->createView(),
                'trickComments' => $trickComments,
                'commentsPage' => $commentsPage,
                'nbPagesComments' => $nbPagesComments]);
    }

    /**
     * @Route("/add-trick", name="trick_add")
     * @Security("has_role('ROLE_USER')")
     */
    public function addAction(Request $request, TrickFormHandler $trickFormHandler){

        $trick = new Trick();

        // Handle trickForm submission
        $trickForm = $this->createForm(TrickType::class, $trick);
        $trickFormHandler->handle($request, $trickForm);

        return $this->render('snowtrix/tricks/add.html.twig',
            ['trickForm' => $trickForm->createView()]);
    }
}
