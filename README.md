# ygg_crawler

YGG Crawler est une librairie vous permettant de parcourir et de récupérer les fichiers torrents du site www.yggtorrent.com

Le but du projet est de permettre aux développeurs utilisant l'API du site T411 avant sa fermeture, de pouvoir récupérer des torrents sur le nouveau site YggTorrent.

/!\ Attention, intégrer la librairie en l'état dans vos application peut s'avérer dangeureux. Etant à l'état de POC, vous pouvez rencontrer des instabilités, lenteurs, erreurs ou autres failles de sécurités.

## Fonctionne
- Recherche par mot clef
- Récupération des torrents du moment
- Récupération des torrents des torents de la veille
- Récupération des torrents du jour

## Exemple d'utilisation

#### Recherche de torrent par mot clef
```
$search = 'Game of thrones s07e02';
$ygg = new Ygg($search);

if ($ygg->login()) {
    $ygg->searchTorrent();
    // Handle result
} else {
    echo 'Unable to login. Please check your credentials';
}
```

#### Recherche de torrent du moment, catégorie "Movies"
```
$category = 'movies';
$ygg = new Ygg();
if ($ygg->login()) {
    $category_id = $ygg::getCategoryId($category);
    $ygg->searchMoment($category_id);
    // Handle result
} else {
    echo 'Unable to login. Please check your credentials';
}
```

#### Ils utilisent YGG Crawler
- Guisch : YGG RSS Feed generator (https://github.com/Guisch/YGG-rss-feed-generator)