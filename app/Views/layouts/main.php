<?= view('layouts/header') ?>
<?= view('layouts/navbar') ?>
<?= view('layouts/sidebar') ?>

<main>
    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <?= $this->renderSection('content') ?>
      </div>
  <!-- /.content-wrapper -->
</main>

<?= view('layouts/footer') ?>
