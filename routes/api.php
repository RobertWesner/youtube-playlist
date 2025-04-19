<?php

use Google\Client;
use Google\Service\YouTube;
use RobertWesner\SimpleMvcPhp\Route;
use RobertWesner\SimpleMvcPhp\Routing\Request;

Route::post('/api/list', function (Request $request) {
    $uri = $request->getRequestParameter('uri');

    // https://stackoverflow.com/a/32321633/23756482
    $client = new Client();
    $client->setDeveloperKey($_ENV['YOUTUBE_API_KEY']);
    $youtube = new YouTube($client);

    $list = null;
    $matches = [];
    if (preg_match('/https:\/\/(?:m|www)\.youtube\.com\/(@[^\/?#]+).*/', $uri, $matches)) {
        $list = 'UULF' . substr($youtube->channels->listChannels('id', ['forHandle' => $matches[1]])->getItems()[0]['id'], 2);
    } elseif (preg_match('/https:\/\/(?:m|www)\.youtube\.com\/playlist.*?(?:\?|&)list=([^&]+)/', $uri, $matches)) {
        $list = $matches[1];
    }

    $items = [];
    $nextPageToken = '';
    do {
        $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
            'playlistId' => $list,
            'maxResults' => 50,
            'pageToken' => $nextPageToken));

        foreach ($playlistItemsResponse['items'] as $playlistItem) {
            $items[] = $playlistItem;
        }

        $nextPageToken = $playlistItemsResponse['nextPageToken'];
    } while ($nextPageToken <> '');

    return Route::json([
        'uri' => $uri,
        'items' => $items,
    ]);
});
