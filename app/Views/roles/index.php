<h1>Daftar Role</h1>
<a href="<?= site_url('admin/roles/edit') ?>">+ Tambah Role</a>
<table>
  <tr><th>Key</th><th>Name</th><th>Aksi</th></tr>
  <?php foreach($groups as $key=>$name): ?>
  <tr>
    <td><?= esc($key) ?></td>
    <td><?= esc($name) ?></td>
    <td>
      <a href="<?= site_url("admin/roles/edit/$key") ?>">Edit</a>
      <a href="<?= site_url("admin/roles/delete/$key") ?>" onclick="return confirm('Hapus?')">Hapus</a>
    </td>
  </tr>
  <?php endforeach ?>
</table>
