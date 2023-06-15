export default class PlaylistFactory
{

    create(playlistString)
    {
        let parsed = null;
        try {
            parsed = JSON.parse(playlistString);
        } catch (e) {
            return null;
        }
        if (!parsed['name'] || !parsed['version']) {
            return null;
        }
        return {
            name: parsed['name'],
            items: null,
            version: parsed['version']
        };
    }

}
