<?php

declare(strict_types=1);

namespace App\Controller\Backend\Content;

use App\Entity\Blog\Post;
use App\Entity\Content\ContentBlock;
use App\Form\Backend\Content\BlogPostType;
use App\Repository\Blog\PostRepository;
use App\Service\ContentBlockManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/blog/article', name: 'backend_blog_post_')]
final class BlogPostController extends AbstractController
{
    public function __construct(
        private readonly ContentBlockManager $contentBlockManager,
    ) {
    }

    #[Route(name: 'index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('backend/content/blog_post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $form = $this->createForm(BlogPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            // Save content blocks with FK
            /** @var ContentBlock[] $contentBlocks */
            $contentBlocks = $form->get('contentBlocks')->getData();
            $this->contentBlockManager->saveBlocksForPost($post, $contentBlocks);
            $entityManager->flush();

            return $this->redirectToRoute('backend_blog_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend/content/blog_post/new.html.twig', [
            'post' => $post,
            'form' => $form,
            'available_types' => $this->contentBlockManager->getMvpTypes(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        $contentBlocks = $this->contentBlockManager->loadBlocksForPost($post);

        return $this->render('backend/content/blog_post/show.html.twig', [
            'post' => $post,
            'content_blocks' => $contentBlocks,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        // Load existing content blocks with eager loading
        $contentBlocks = $this->contentBlockManager->loadBlocksForPost($post);

        $form = $this->createForm(BlogPostType::class, $post);

        // Set content blocks data on the form
        $form->get('contentBlocks')->setData($contentBlocks);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save content blocks with FK
            /** @var ContentBlock[] $submittedBlocks */
            $submittedBlocks = $form->get('contentBlocks')->getData();
            $this->contentBlockManager->saveBlocksForPost($post, $submittedBlocks);

            $entityManager->flush();

            return $this->redirectToRoute('backend_blog_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend/content/blog_post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
            'available_types' => $this->contentBlockManager->getMvpTypes(),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_blog_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
