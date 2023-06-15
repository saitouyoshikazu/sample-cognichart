import LStorage from '../localstorage/LStorage'

export default class PlayerUsage
{

    constructor()
    {
        this.storage = new LStorage();

    }

    saveUsage(usage)
    {
        this.storage.save("player-usage", usage);
    }

    getUsage()
    {
        let usage = this.storage.get("player-usage");
        if (!usage) {
            return {
                'playlistLoop': false,
                'playlistRandom': false,
                'playlistPlaying': null
            };
        }
        return usage;
    }

    setPlaylistLoop(playlistLoop)
    {
        let usage = this.getUsage();
        if (!playlistLoop) {
            usage['playlistLoop'] = false;
        } else {
            usage['playlistLoop'] = true;
        }
        this.saveUsage(usage);
    }

    setPlaylistRandom(playlistRandom)
    {
        let usage = this.getUsage();
        if (!playlistRandom) {
            usage['playlistRandom'] = false;
        } else {
            usage['playlistRandom'] = true;
        }
        this.saveUsage(usage);
    }

    setPlaylistPlaying(playlistPlaying)
    {
        let usage = this.getUsage();
        if (!playlistPlaying) {
            usage['playlistPlaying'] = null;
        } else {
            usage['playlistPlaying'] = playlistPlaying;
        }
        this.saveUsage(usage);
    }

}
