<?php
/**
 * Created by PhpStorm.
 * User: asaugrain
 * Date: 09/11/2016
 * Time: 18:08
 */

require_once('Ygg.php');

switch ($_GET['action']) {
    case 'simple':
        if (isset($_GET['term'])) {
            searchByTeam($_GET['term']);
        } else {
            echo 'Term missing';
        }
        break;
    case 'moment':
        getMoment();
        break;

    case 'download':
        if (isset($_GET['file'])) {
            download($_GET['file']);
        }
        break;

    default:
        echo 'Bad action';
        break;
}

// Search by term sample
function searchByTeam($search)
{
    $ygg = new Ygg($search);

    if ($ygg->login()) {
        $ygg->searchTorrent();

        echo '<h2>Ratio</h2>';
        echo '<p>Up : ' . $ygg->getUp() . '</p>';
        echo '<p>Down : ' . $ygg->getDown() . '</p>';

        echo '<h2>Founded torrents</h2>';
        echo '<table>';
        echo '<tr>';
        echo '<th>Torrent</th>';
        echo '<th>Taille</th>';
        echo '<th>Seeds</th>';
        echo '<th>Leechs</th>';
        echo '</tr>';

        foreach ($ygg->getTorrents() as $torrent) {
            echo '<tr>';
            echo '<td><a href="' . $torrent['href'] . '">' . $torrent['name'] . '</a></td>';
            echo '<td>' . $torrent['size'] . '</td>';
            echo '<td>' . $torrent['seeds'] . '</td>';
            echo '<td>' . $torrent['leechs'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
}

function getMoment()
{
    $ygg = new Ygg();
    if ($ygg->login()) {
        echo 'search moment';
    }
}

// Sample download torrent
function download($file)
{
    $ygg = new Ygg();
    if ($ygg->download($file)) {
        echo 'Torrent downloaded';
    }
}
