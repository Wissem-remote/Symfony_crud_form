<?php

namespace App\Controller;

use App\Entity\Conducteur;
use App\Form\ConducteurType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $drivers = $doctrine->getRepository(Conducteur::class)->findAll();
        
        return $this->render('home/index.html.twig', [
            'drivers' => $drivers,
        ]);
    }

    #[Route('/add/{nom}/{prenom}/{age}', name: 'add')]

    public function add(ManagerRegistry $doctrine, $nom, $prenom, $age): Response
    {
        $em = $doctrine->getManager();

        $driver = new Conducteur;
        $driver->setNom($nom);
        $driver->setPrenom($prenom);
        $driver->setAge($age);
        $em->persist($driver);
        $em->flush();
        return new Response('<html><body> votre vtc a bien été ajouter </body></html>');
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(ManagerRegistry $doctrine, Conducteur $vtc): Response
    {
        

        $em = $doctrine->getManager();

        $em->remove($vtc);

        $em->flush();

        return $this->redirectToRoute('home', ['delete' => 'success']);
    }

    #[Route('/vtc/{id}', name: 'show')]
    public function show(Conducteur $vtc): Response
    {
        
        return $this->render('home/show.html.twig',[
            'vtc' => $vtc
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create( Request $request, ManagerRegistry $doctrine): Response
    {
        $vtc = new Conducteur;

        // $form = $this->createFormBuilder($vtc)
        //         ->add('nom', TextType::class)
        //         ->add('prenom', TextType::class)
        //         ->add('age', NumberType::class)
        //         ->add('save', SubmitType::class)
        //         ->getForm();

        $form = $this->createForm(ConducteurType::class,$vtc);
        
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            $em = $doctrine->getManager();

            $em->persist($vtc);

            $em->flush();

            $this->addFlash('success_add','Votre conducteur est ajouter');

            return $this->redirectToRoute('home');
        }
        return $this->render('home/form.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/update/{id}', name: 'update')]
    
    public function update(Conducteur $vtc, Request $request, ManagerRegistry $doctrine): Response
    {
        
        $form = $this->createForm(ConducteurType::class, $vtc);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();

            // on fait pas de persist car objet est deja remplie au prêt de doctrine

            $em->flush();

            $this->addFlash('success_update', 'Votre conducteur à été modifier');

            return $this->redirectToRoute('home');
        }
        
        return $this->render('home/update.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
