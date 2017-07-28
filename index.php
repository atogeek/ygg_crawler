<?php
/**
 * User: Anthony Saugrain
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
        if (isset($_GET['category'])) {
            getMoment();
        } else {
            echo 'Category id missing';
        }
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

// SAMPLE -- Search by term
function searchByTeam($search)
{
    $ygg = new Ygg($search);

    if ($ygg->login()) {
        $ygg->searchTorrent();
        trashDisplay($ygg);
    } else {
        echo 'Unable to login. Please check your credentials';
    }
}

// SAMPLE -- get torrent of the moment (for a given category)
function getMoment()
{
    $ygg = new Ygg();
    if ($ygg->login()) {
        $category_id = $ygg::getCategoryId($_GET['category']);
        $ygg->searchMoment($category_id);
        trashDisplay($ygg);
    }
}

// SAMPLE -- download torrent
function download($file)
{
    $ygg = new Ygg();
    if ($ygg->download($file)) {
        echo 'Torrent downloaded';
    }
}

// Quick & dirty display retrieven torrents
function trashDisplay($ygg)
{
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