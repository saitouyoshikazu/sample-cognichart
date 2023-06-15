export default class PlaylistItemFactory
{

    create(playlistString)
    {
        let parsed = null;
        try {
            parsed = JSON.parse(playlistString);
        } catch (e) {
            return null;
        }
        if (!parsed['items']) {
            return null;
        }
        let items = new Array();
        for (let index in parsed['items']) {
            let parsedItem = parsed['items'][index];
            if (!parsedItem['artistName'] || !parsedItem['musicTitle'] || !parsedItem['youtubeId']) {
                continue;
            }
            let artistIdValue = "";
            let musicIdValue = "";
            let iTunesBaseUrlValue = "";
            if (parsedItem['artistId']) {
                artistIdValue = parsedItem['artistId'];
            }
            if (parsedItem['musicId']) {
                musicIdValue = parsedItem['musicId'];
            }
            if (parsedItem['iTunesBaseUrl']) {
                iTunesBaseUrlValue = parsedItem['iTunesBaseUrl'];
            }
            items.push({
                artistId: artistIdValue,
                musicId: musicIdValue,
                artistName: parsedItem['artistName'],
                musicTitle: parsedItem['musicTitle'],
                youtubeId: parsedItem['youtubeId'],
                iTunesBaseUrl: iTunesBaseUrlValue
            });
        }
        return items;
    }

}
