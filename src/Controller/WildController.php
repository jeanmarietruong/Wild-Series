<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wild", name="wild_")
 */
 class WildController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index() :Response
    {
        $programs = $this->getDoctrine()
        ->getRepository(Program::class)
        ->findAll();
        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }
        
        return $this->render('wild/index.html.twig', [
            'programs' => $programs
        ]);
    }
    
    /**
     * @param string $slug The slugger
     * @Route("/show/{slug}",
     * requirements={"slug"="[a-z0-9-]+"},
     * name="show")
     * @return Response
     */
    public function show(?string $slug): Response
    {
        if (!$slug) {
            throw $this
            ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
        ->getRepository(Program::class)
        ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }
        
        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug
        ]);
    }
    
    /**
     * @param string $categoryName
     * @Route("/category/{categoryName}",
     * requirements={"slug"="[a-z0-9-]+"},
     * name="show_category")
     * @return Response
     */
    public function showByCategory(?string $categoryName) :response
    {
        if (!$categoryName) {
            throw $this
            ->createNotFoundException('No category has been sent to find a category in category\'s table.');
        }
        $categoryName = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($categoryName)), "-")
        );
        $category = $this ->getDoctrine()
        ->getRepository(Category::class)
        ->findOneBy(['name' => $categoryName]);
        $program =  $this ->getDoctrine()
        ->getRepository(Program::class)
        ->findBy(['category' => $category->getId()], ['id' => 'DESC'], 3);
        if (!$category) {
            throw $this->createNotFoundException(
                'No program with '.$categoryName.' name, found in category\'s table.'
            );
        }
        return $this->render('wild/category.html.twig', [
            'program' => $program,
            'categoryName'  => $categoryName
        ]);
    }
}
