<h1><?= $key ? 'Edit' : 'Tambah' ?> Role</h1>
<form method="post" action="<?= site_url('admin/roles/save') ?>">
  <?php if($key): ?>
    <input type="hidden" name="key" value="<?= esc($key) ?>">
  <?php else: ?>
    Key: <input name="key" required><br>
  <?php endif ?>
  Name:<br>
  <input name="name" value="<?= esc($roleName) ?>" required><br><br>

  <h4>Permissions</h4>
  <?php foreach($allPerms as $perm): ?>
    <label>
      <input type="checkbox" name="permissions[]" value="<?= $perm ?>"
        <?= in_array($perm, $rolePerms) ? 'checked' : '' ?>>
      <?= esc($perm) ?>
    </label><br>
  <?php endforeach ?>

  <button type="submit">Simpan</button>
</form>
