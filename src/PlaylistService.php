<?php

declare(strict_types=1);

namespace App;

use Google\Service\Exception as GoogleException;
use Google\Service\YouTube;

/**
 * Ugly class to fetch playlist items.
 */
final class PlaylistService
{
    /**
     * @throws GoogleException
     */
    public static function getItems(YouTube $youtube, string $list, ?callable $onSlice = null): array
    {
        // https://stackoverflow.com/a/32321633/23756482

        $items = [];
        $nextPageToken = '';
        do {
            $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet', [
                'playlistId' => $list,
                'maxResults' => 50,
                'pageToken' => $nextPageToken,
            ]);

            /** @var YouTube\PlaylistItem $playlistItem */
            foreach ($playlistItemsResponse['items'] as $playlistItem) {
                $items[] = [
                    'position' => $playlistItem->getSnippet()->getPosition(),
                    'title' => $playlistItem->getSnippet()->getTitle(),
                    'description' => $playlistItem->getSnippet()->getDescription(),
                    'videoId' => $playlistItem->getSnippet()->getResourceId()->getVideoId(),
                ];
            }

            $nextPageToken = $playlistItemsResponse['nextPageToken'];

            if ($onSlice !== null) {
                $onSlice($items);
            }
        } while ($nextPageToken <> '');

        return $items;
    }
}
