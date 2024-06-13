<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title><?php echo $page->get('titrepage'); ?></title>

    <?php require __DIR__ . '/includes/head.php'; ?>
</head>

<body>

    
    <?php require __DIR__ . '/includes/logo.php'; ?>

    <h1><?php echo $page->get('title'); ?></h1>
    <?php
        $articles = $page->find('/blog')->children->sort('pos', 'desc')->slice(0,30); 
    ?>
    <ul id="archiveList">
        <?php foreach($articles as $article){ ?>
        <li><a href="<?php echo $article->get('link'); ?>"><?php echo $article->get('title'); ?></a> <a href="<?php echo $article->get('link'); ?>">[Read]</a></li>
        <?php } ?>         
        </ul>

<?php require __DIR__ . '/includes/menu.php'; ?>


<?php require __DIR__ . '/includes/footer.php'; ?>

</body>

</html>
