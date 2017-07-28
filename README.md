# ygg_crawler

Avec la fermeture du regretté tracker de torrent T411, nous sommes tous devenu orphelin de son incroyable API.
Ce POC permet de parcourir le site YggTorrent (petit frère de T411), et d'en extraire les torrents.

## Fonctionne
- Recherche par mot clef
- Récupération des torrents du moment

## A venir
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
