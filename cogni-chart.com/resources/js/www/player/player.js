import PlayerUsage from './PlayerUsage'
import Playlist from "../playlist/Playlist"

$('#display-player').on(
    'click',
    function () {
        if ($('#player-box').hasClass('d-none')) {
            showPlayer();
        } else {
            hidePlayer();
        }
    }
);

export function showPlayer()
{
    $('article.wwwbody').addClass('player-on');
    $('#player-box').removeClass('d-none');
    $('#display-player').removeClass('btn-gray');
    $('#display-player').addClass('btn-primary');
}

export function hidePlayer()
{
    $('article.wwwbody').removeClass('player-on');
    $('#player-box').addClass('d-none');
    $('#display-player').addClass('btn-gray');
    $('#display-player').removeClass('btn-primary');
}

export function cue(playlistName)
{
    if (!playlistName) {
        return;
    }
    let items = playlist.items(playlistName);
    if (items === null) {
        return;
    }
    let ytids = new Array();
    let ytidCount = 0;
    for (let index in items) {
        if (items[index]['youtubeId']) {
            ytids.push(items[index]['youtubeId']);
            ytidCount++;
        }
        if (ytidCount >= 200) {
            break;
        }
    }
    player.cuePlaylist(ytids, 0);
    let usage = playerUsage.getUsage();
    if (!usage['playlistLoop']) {
        loopOff();
    } else {
        loopOn();
    }
    if (!usage['playlistRandom']) {
        randomOff();
    } else {
        randomOn();
    }
    usage['playlistPlaying'] = playlistName;
    playerUsage.saveUsage(usage);
}

export let player;
let playlist;
let playerUsage;
$(window).on(
    'load',
    function () {
        player = new YT.Player('player', {
            events: {
                onReady: onPlayerReady
            },
            origin: location.protocol + "//" + location.hostname + "/"
        });

        playlist = new Playlist();

        playerUsage = new PlayerUsage();
    }
);

function onPlayerReady(event)
{
    let usage = playerUsage.getUsage();
    cue(usage['playlistPlaying']);
}

function loopOn()
{
    $('#set-playlist-loop').attr('data-onoff', 'on');
    $('#set-playlist-loop').data('onoff', 'on');
    $('#set-playlist-loop').removeClass('btn-gray');
    $('#set-playlist-loop').addClass('btn-primary');
    $('#set-playlist-loop > .playway-text').html('ON');
    player.setLoop(true);
}

function loopOff()
{
    $('#set-playlist-loop').attr('data-onoff', 'off');
    $('#set-playlist-loop').data('onoff', 'off');
    $('#set-playlist-loop').addClass('btn-gray');
    $('#set-playlist-loop').removeClass('btn-primary');
    $('#set-playlist-loop > .playway-text').html('OFF');
    player.setLoop(false);
}

function randomOn()
{
    $('#set-playlist-random').attr('data-onoff', 'on');
    $('#set-playlist-random').data('onoff', 'on');
    $('#set-playlist-random').removeClass('btn-gray');
    $('#set-playlist-random').addClass('btn-primary');
    $('#set-playlist-random > .playway-text').html('ON');
    player.setShuffle(true);
}

function randomOff()
{
    $('#set-playlist-random').attr('data-onoff', 'off');
    $('#set-playlist-random').data('onoff', 'off');
    $('#set-playlist-random').addClass('btn-gray');
    $('#set-playlist-random').removeClass('btn-primary');
    $('#set-playlist-random > .playway-text').html('OFF');
    player.setShuffle(false);
}

$('#set-playlist-loop').on(
    'click',
    function (event) {
        if ($('#set-playlist-loop').attr('data-onoff') === 'off') {
            playerUsage.setPlaylistLoop(true);
            loopOn();
        } else {
            playerUsage.setPlaylistLoop(false);
            loopOff();
        }
    }
);

$('#set-playlist-random').on(
    'click',
    function (event) {
        if ($('#set-playlist-random').attr('data-onoff') === 'off') {
            playerUsage.setPlaylistRandom(true);
            randomOn();
        } else {
            playerUsage.setPlaylistRandom(false);
            randomOff();
        }
    }
);
