<?php

declare(strict_types=1);

namespace App\Controller;

use HackerNewsApi\Client\HackerNewsClientInterface;
use HackerNewsApi\Models\Item;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends Controller
{
    private const ITEMS_PER_PAGE = 10;

    /**
     * @Route("/", name="homepage")
     */
    public function index(
        HackerNewsClientInterface $client
    ): Response {
        $itemsIds = $client->getNewStories();
        $limitedItemsIds = \array_slice($itemsIds, 0, self::ITEMS_PER_PAGE);

        $items = \array_map(
            function (int $itemId) use ($client): ?Item {
                return $client->getItem($itemId);
            },
            $limitedItemsIds
        );

        return $this->render(
            'home.html.twig',
            [
                'items' => $items,
            ]
        );
    }
}
