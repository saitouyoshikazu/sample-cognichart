import LStorage from "../localstorage/LStorage"
import PlaylistFactory from "./PlaylistFactory"
import PlaylistItemFactory from "./PlaylistItemFactory"

export default class Playlist
{

    constructor()
    {
        this.storage = new LStorage();
        this.version = "1.0.0";
    }

    list()
    {
        let playlists = this.storage.get("playlists");
        let list = new Array();
        if (playlists === null) {
            return list;
        }
        
        for (let index in playlists) {
            list.push(playlists[index]['name']);
        }
        return list;
    }

    getIndex(playlistName)
    {
        let trimmedPlaylistName = this.trimming(playlistName);
        if (trimmedPlaylistName.length == 0) {
            return null;
        }
        let playlists = this.storage.get("playlists");
        if (playlists === null) {
            return null;
        }
        for (let index in playlists) {
            if (playlists[index]['name'] === trimmedPlaylistName) {
                return index;
            }
        }
        return null;
    }

    get(playlistNameValue)
    {
        let playlistIndex = this.getIndex(playlistNameValue);
        if (playlistIndex === null) {
            return null;
        }

        let playlists = this.storage.get("playlists");
        return playlists[playlistIndex];
    }

    append(newPlaylistName)
    {
        let trimmedPlaylistName = this.trimming(newPlaylistName);
        if (trimmedPlaylistName.length == 0) {
            alert("Please input valid string.");
            return null;
        }
        if (this.exists(trimmedPlaylistName)) {
            alert(trimmedPlaylistName + " is already exists.");
            return null;
        }
        let playlists = this.storage.get("playlists");
        if (playlists === null) {
            playlists = new Array();
        }
        playlists.push(
            {
                'name': trimmedPlaylistName,
                'items': null,
                'version': this.version
            }
        );
        this.storage.save("playlists", playlists);
        return trimmedPlaylistName;
    }

    rename(oldPlaylistName, newPlaylistName)
    {
        oldPlaylistName = this.trimming(oldPlaylistName);
        newPlaylistName = this.trimming(newPlaylistName);
        if (oldPlaylistName === newPlaylistName) {
            return newPlaylistName;
        }
        if (oldPlaylistName.length == 0 || newPlaylistName.length == 0) {
            alert("Old or new playlist name is invalid string.");
            return null;
        }
        let playlists = this.storage.get("playlists");
        if (playlists === null) {
            return this.append(newPlaylistName);
        }
        let renameIndex = null;
        for (let index in playlists) {
            if (playlists[index]['name'] === oldPlaylistName) {
                renameIndex = index;
                break;
            }
        }
        if (renameIndex === null) {
            return this.append(newPlaylistName);
        }
        if (this.exists(newPlaylistName)) {
            alert(newPlaylistName + " is already exists.");
            return null;
        }
        playlists[renameIndex]['name'] = newPlaylistName;
        this.storage.save("playlists", playlists);
        return newPlaylistName;
    }

    remove(playlistName)
    {
        let playlists = this.storage.get("playlists");
        if (playlists === null) {
            return false;
        }
        playlistName = this.trimming(playlistName);
        if (playlistName.length == 0) {
            return false;
        }
        let removeIndex = null;
        for (let index in playlists) {
            if (playlists[index]['name'] === playlistName) {
                removeIndex = index;
                break;
            }
        }
        if (removeIndex === null) {
            return false;
        }
        if (confirm('You are going to delete ' + playlistName + '. Are you ok?')) {
            playlists.splice(removeIndex, 1);
            this.storage.save("playlists", playlists);
            return true;
        }
        return false;
    }

    rebuild(playlistNames)
    {
        if (!Array.isArray(playlistNames)) {
            alert("List of playlist names is invalid type.");
            return false;
        }
        if (playlistNames.length == 0) {
            alert("List of playlist names is empty.");
            return false;
        }
        let oldPlaylists = this.storage.get("playlists");
        let newPlaylists = new Array();
        for (let indexNew in playlistNames) {
            let playlistName = this.trimming(playlistNames[indexNew]);
            let items = null;
            let version = "1.0.0";
            for (let indexOld in oldPlaylists) {
                if (oldPlaylists[indexOld]['name'] === playlistName) {
                    items = oldPlaylists[indexOld]['items'];
                    if (oldPlaylists[indexOld]['version']) {
                        version = oldPlaylists[indexOld]['version'];
                    }
                    break;
                }
            }
            newPlaylists.push(
                {
                    'name': playlistName,
                    'items': items,
                    'version': version
                });
        }
        this.storage.save("playlists", newPlaylists);
        return true;
    }

    trimming(str)
    {
        let trimmedStr = jQuery.trim(str);
        trimmedStr = trimmedStr.replace(/^\p{blank}+/g, "");
        trimmedStr = trimmedStr.replace(/\p{blank}+$/g, "");
        return trimmedStr;
    }

    exists(playlistName)
    {
        let playlists = this.storage.get("playlists");
        if (playlists === null) {
            return false;
        }
        playlistName = this.trimming(playlistName);
        for (let index in playlists) {
            if (playlists[index]['name'] === playlistName) {
                return true;
            }
        }
        return false;
    }

    appendPlaylistItem(
        playlistNameValue,
        argArtistIdValue,
        argMusicIdValue,
        artistNameValue,
        musicTitleValue,
        youtubeIdValue,
        argITunesBaseUrlValue
    ) {
        let playlistIndex = this.getIndex(playlistNameValue);
        if (playlistIndex === null) {
            alert("Couldn't find playlist '" + playlistNameValue + "'.");
            return null;
        }
        let playlists = this.storage.get("playlists");
        let items = playlists[playlistIndex]['items'];
        if (items === null) {
            items = new Array();
        }
        if (items.length >= 200) {
            alert("You can only add up to 200 songs in a playlist.");
            return false;
        }
        let artistIdValue = "";
        let musicIdValue = "";
        let iTunesBaseUrlValue = "";
        if (argArtistIdValue) {
            artistIdValue = argArtistIdValue;
        }
        if (argMusicIdValue) {
            musicIdValue = argMusicIdValue;
        }
        if (argITunesBaseUrlValue) {
            iTunesBaseUrlValue = argITunesBaseUrlValue;
        }
        items.push({
            'artistId': artistIdValue,
            'musicId': musicIdValue,
            'artistName': artistNameValue,
            'musicTitle': musicTitleValue,
            'youtubeId': youtubeIdValue,
            'iTunesBaseUrl': iTunesBaseUrlValue
        });
        playlists[playlistIndex]['items'] = items;
        this.storage.save("playlists", playlists);
        return true;
    }

    items(playlistNameValue)
    {
        let playlistIndex = this.getIndex(playlistNameValue);
        if (playlistIndex === null) {
            return null;
        }
        let playlists = this.storage.get("playlists");
        return playlists[playlistIndex]['items'];
    }

    removePlaylistItem(playlistNameValue, indexValue, youtubeIdValue)
    {
        let playlistIndex = this.getIndex(playlistNameValue);
        if (playlistIndex === null) {
            alert("Couldn't find playlist '" + playlistNameValue + "'.");
            return false;
        }
        let playlists = this.storage.get("playlists");
        let items = playlists[playlistIndex]['items'];
        if (!items[indexValue]) {
            return false;
        }
        if (items[indexValue].youtubeId !== youtubeIdValue) {
            return false;
        }
        items.splice(indexValue, 1);
        playlists[playlistIndex]['items'] = items;
        this.storage.save("playlists", playlists);
        return true;
    }

    rebuildPlaylistItems(playlistNameValue, playlistItems)
    {
        let playlistIndex = this.getIndex(playlistNameValue);
        if (playlistIndex === null) {
            alert("Couldn't find playlist '" + playlistNameValue + "'.");
            return false;
        }
        let playlists = this.storage.get("playlists");
        playlists[playlistIndex]['items'] = playlistItems;
        this.storage.save("playlists", playlists);
        return true;
    }

    parse(playlistString)
    {
        let playlistFactory = new PlaylistFactory();
        let playlist = playlistFactory.create(playlistString);
        if (!playlist) {
            return;
        }
        let playlistItemFactory = new PlaylistItemFactory();
        let parsedItems = playlistItemFactory.create(playlistString);
        let items = new Array();
        let itemCount = 0;
        for (let itemIndex in parsedItems) {
            items.push(parsedItems[itemIndex]);
            itemCount++;
            if (itemCount >= 200) {
                break;
            }
        }
        playlist['items'] = items;

        let playlists = this.storage.get("playlists");
        if (playlists === null) {
            playlists = new Array();
        }
        if (this.exists(playlist['name'])) {
            if (confirm(
                playlist['name'] + " is already existing.\n" +
                "It will be overwritten with the contents of the file.\n" +
                "Is this OK ?"
            )) {
                let playlistIndex = this.getIndex(playlist['name']);
                if (playlistIndex === null) {
                    alert("Couldn't find playlist '" + playlist['name'] + "'.");
                    return;
                }
                playlists[playlistIndex] = playlist;
                this.storage.save("playlists", playlists);
            }
        } else {
            playlists.push(playlist);
            this.storage.save("playlists", playlists);
        }
        if (parsedItems.length > 200) {
            alert("You can only add up to 200 songs in a playlist.");
        }
    }

}
