<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DemoController extends Controller
{
    /**
     * @Route("/demo_mode", name="demo_mode")
     */
    public function showDemoPage(){
        return $this->render('demo_mode.html.twig');
    }
}
