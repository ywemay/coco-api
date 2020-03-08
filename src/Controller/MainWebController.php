<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainWebController extends AbstractController
{
    /**
     * @Route("/", name="home_page")
     */
    public function index()
    {
        $user = $this->getUser();
        return $this->render('main/home.html.twig', []);
    }
}
