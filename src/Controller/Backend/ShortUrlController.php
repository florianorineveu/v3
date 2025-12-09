<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Entity\Tool\ShortUrl;
use App\Form\Backend\ShortUrlType;
use App\Repository\Tool\ShortUrlRepository;
use App\Service\ShortenUrl;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/url-raccourcie', name: 'backend_short_url_')]
final class ShortUrlController extends AbstractController
{
    #[Route(name: 'index', methods: ['GET'])]
    public function index(ShortUrlRepository $shortUrlRepository): Response
    {
        return $this->render('backend/short_url/index.html.twig', [
            'short_urls' => $shortUrlRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ShortenUrl $shortenUrl): Response
    {
        $shortUrl = new ShortUrl();
        $form = $this->createForm(ShortUrlType::class, $shortUrl);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shortUrl->setCreatedBy($this->getUser());

            if (!$shortUrl->getSlug()) {
                $shortUrl->setSlug($shortenUrl->generateSlug());
            }

            $entityManager->persist($shortUrl);
            $entityManager->flush();

            return $this->redirectToRoute('backend_short_url_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend/short_url/new.html.twig', [
            'short_url' => $shortUrl,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(ShortUrl $shortUrl): Response
    {
        return $this->render('backend/short_url/show.html.twig', [
            'short_url' => $shortUrl,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ShortUrl $shortUrl, EntityManagerInterface $entityManager, ShortenUrl $shortenUrl): Response
    {
        $form = $this->createForm(ShortUrlType::class, $shortUrl);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$shortUrl->getSlug()) {
                $shortUrl->setSlug($shortenUrl->generateSlug());
            }

            $entityManager->flush();

            return $this->redirectToRoute('backend_short_url_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend/short_url/edit.html.twig', [
            'short_url' => $shortUrl,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, ShortUrl $shortUrl, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $shortUrl->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($shortUrl);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_short_url_index', [], Response::HTTP_SEE_OTHER);
    }
}
