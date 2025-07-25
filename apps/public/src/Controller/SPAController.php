<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller;

/**
 * Description of SPAController
 *
 * @author aldo
 */
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SPAController  extends AbstractController 
{
    #[Route('/', name: 'spa_fallback2', requirements: ['any' => '.+'], priority: -1)]
    public function index0(): Response
    {
        $content = file_get_contents(__DIR__.'/../../index.html');
        return new Response($content);
    }
    #[Route('/{any}', name: 'spa_fallback', requirements: ['any' => '.+'], priority: -1)]
    public function index(): Response
    {
        $content = file_get_contents(__DIR__.'/../../index.html');
        return new Response($content);
    }
}