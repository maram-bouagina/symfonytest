<?php
namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\PropertySearch;
use App\Entity\CategorySearch;
use App\Entity\PriceSearch; // Ajouté
use App\Form\PropertySearchType;
use App\Form\CategorySearchType;
use App\Form\PriceSearchType; // Ajouté
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ArticleType;
use App\Form\CategoryType;

class IndexController extends AbstractController
{
    #[Route('/', name: 'article_list')]
    public function home(Request $request, EntityManagerInterface $em): Response
    {
        $propertySearch = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $propertySearch);
        $form->handleRequest($request);
        $articles = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $nom = $propertySearch->getNom();
            if ($nom) {
                $articles = $em->getRepository(Article::class)->findBy(['nom' => $nom]);
            } else {
                $articles = $em->getRepository(Article::class)->findAll();
            }
        } else {
            $articles = $em->getRepository(Article::class)->findAll();
        }

        return $this->render('articles/index.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles
        ]);
    }

    #[Route('/art_cat', name: 'article_par_cat')]
    public function articlesParCategorie(Request $request, EntityManagerInterface $em): Response
    {
        $categorySearch = new CategorySearch();
        $form = $this->createForm(CategorySearchType::class, $categorySearch);
        $form->handleRequest($request);
        $articles = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $categorySearch->getCategory();
            $articles = $category ? $em->getRepository(Article::class)->findBy(['category' => $category]) : $em->getRepository(Article::class)->findAll();
        } else {
            $articles = $em->getRepository(Article::class)->findAll();
        }

        return $this->render('articles/articlesParCategorie.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles
        ]);
    }

    #[Route('/art_prix', name: 'article_par_prix', methods: ['GET', 'POST'])]
    public function articlesParPrix(Request $request, EntityManagerInterface $em): Response
    {
        $priceSearch = new PriceSearch();
        $form = $this->createForm(PriceSearchType::class, $priceSearch);
        $form->handleRequest($request);
        $articles = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $minPrice = $priceSearch->getMinPrice();
            $maxPrice = $priceSearch->getMaxPrice();
            $articles = $em->getRepository(Article::class)->findByPriceRange($minPrice, $maxPrice);
        } else {
            $articles = $em->getRepository(Article::class)->findAll();
        }

        return $this->render('articles/articlesParPrix.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles
        ]);
    }

    #[Route('/article/new', name: 'new_article', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
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

    #[Route('/category/new', name: 'new_category', methods: ['GET', 'POST'])]
    public function newCategory(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('article_list');
        }
        dump($form->createView());
        return $this->render('new_category.html.twig', [
            'form' => $form->createView()
        ]);
    }
}