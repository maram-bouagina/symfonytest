<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ArticleType; 

class IndexController extends AbstractController
{
    #[Route('/', name: 'article_list')]
    public function home(EntityManagerInterface $em): Response
    {
        $articles = $em->getRepository(Article::class)->findAll();
        return $this->render('articles/index.html.twig', ['articles' => $articles]);
    }

//     #[Route('/article/save', name: 'article_save')]
//     public function save(EntityManagerInterface $em): Response
//     {
//         $article = new Article();
//         $article->setNom('Article 3');
//         $article->setPrix(3000);
        
//         $em->persist($article);
//         $em->flush();

//         return new Response('Article enregistré avec id '.$article->getId());
//     }
//     #[Route('/article/new', name: 'new_article', methods: ['GET', 'POST'])]
// public function new(Request $request, EntityManagerInterface $em): Response
// {
//     $article = new Article();
    
//     $form = $this->createFormBuilder($article)
//         ->add('nom', TextType::class)
//         ->add('prix', NumberType::class)
//         ->add('save', SubmitType::class, ['label' => 'Créer'])
//         ->getForm();

//     $form->handleRequest($request);

//     if($form->isSubmitted() && $form->isValid()) {
//         $em->persist($article);
//         $em->flush();

//         return $this->redirectToRoute('article_list');
//     }

//     return $this->render('articles/new.html.twig', ['form' => $form->createView()]);

//}
#[Route('/article/new', name: 'new_article', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $em): Response
{
    // $article = new Article();
    // $form = $this->createForm(ArticleType::class, $formOptions = [
    //     'action' => $this->generateUrl('new_article'),
    //     'method' => 'POST'
    // ]);

    $article = new Article();
    $form = $this->createForm(ArticleType::class, $article);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($article);
        $em->flush();

        $this->addFlash('success', 'Article créé avec succès!');
        return $this->redirectToRoute('article_list');
    }

    return $this->render('articles/new.html.twig', [
        'form' => $form->createView(),
    ]);
}
    #[Route('/article/{id}', name: 'article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('articles/show.html.twig', [
            'article' => $article
        ]);
    }
    #[Route('/article/edit/{id}', name: 'article_edit', methods: ['GET', 'POST'])]
// public function edit(Request $request, Article $article, EntityManagerInterface $em): Response
// {
//     $form = $this->createFormBuilder($article)
//         ->add('nom', TextType::class, ['attr' => ['class' => 'form-control']])
//         ->add('prix', NumberType::class, ['attr' => ['class' => 'form-control']])
//         ->add('save', SubmitType::class, [
//             'label' => 'Mettre à jour',
//             'attr' => ['class' => 'btn btn-primary mt-3']
//         ])
//         ->getForm();

//     $form->handleRequest($request);

//     if($form->isSubmitted() && $form->isValid()) {
//         $em->flush();
//         return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
//     }

//     return $this->render('articles/edit.html.twig', [
//         'form' => $form->createView(),
//         'article' => $article
//     ]);}
#[Route('/article/edit/{id}', name: 'edit_article', methods: ['GET', 'POST'])]
public function edit(Request $request, Article $article, EntityManagerInterface $em): Response
{
    $form = $this->createForm(ArticleType::class, $article);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();
        $this->addFlash('success', 'Article mis à jour!');
        return $this->redirectToRoute('article_list');
    }

    return $this->render('articles/edit.html.twig', [
        'form' => $form->createView(),
        'article' => $article
    ]);
}

    #[Route('/article/delete/{id}', name: 'article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $em->remove($article);
            $em->flush();
        }
        
        return $this->redirectToRoute('article_list');
    }


}