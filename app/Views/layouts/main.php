<?= view('layouts/header') ?>
<?= view('layouts/navbar') ?>
<?= view('layouts/sidebar') ?>

<main>
    <?= $this->renderSection('content') ?>
</main>

<?= view('layouts/footer') ?>
