<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title><?php echo $page->get('title'); ?></title>

    <?php require __DIR__ . '/includes/head.php'; ?>

</head>

<body>

    <?php require __DIR__ . '/includes/logo.php'; ?>

    <h1><?php echo $page->get('title'); ?></h1>
    <p>
      <p><?php echo $page->get('content'); ?></p>
    </p>   


<?php require __DIR__ . '/includes/menu.php'; ?>


<?php require __DIR__ . '/includes/footer.php'; ?>

</body>

</html>
