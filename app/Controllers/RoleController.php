<?php
// app/Controllers/Admin/RoleController.php
namespace App\Controllers;
use App\Controllers\BaseController;
use CodeIgniter\Settings\Models\SettingModel;

class RoleController extends BaseController
{
    protected $settings;

    public function __construct()
    {
        $this->settings = service('settings');
    }

    // 1. List semua grup
    public function index()
    {
        // Ambil array ['groupKey' => 'Group Name', …]
        $groups = $this->settings->get('AuthGroups.groups');
        return view('roles/index', compact('groups'));
    }

    // 2. Form buat/edit grup
    public function edit(string $key = null)
    {
        $allGroups = $this->settings->get('AuthGroups.groups');
        $allPerms  = $this->settings->get('AuthPermissions.permissions');
        $matrix    = $this->settings->get('AuthGroups.matrix');

        $roleName  = $key ? $allGroups[$key] : '';
        $rolePerms = $key ? ($matrix[$key] ?? []) : [];

        return view('roles/form', compact('key','roleName','allPerms','rolePerms'));
    }

    // 3. Simpan hasil form
    public function save()
    {
        $post = $this->request->getPost();
        // Ambil daftar lama
        $groups = $this->settings->get('AuthGroups.groups');
        $matrix = $this->settings->get('AuthGroups.matrix');

        // Tambah/ubah nama grup
        $groups[$post['key']] = $post['name'];
        // Set matrix permission baru
        $matrix[$post['key']] = $post['permissions'] ?? [];

        // Simpan ke DB
        $this->settings->set('AuthGroups.groups', $groups);
        $this->settings->set('AuthGroups.matrix', $matrix);
        $this->settings->save();

        return redirect()->to('/admin/roles')->with('success','Role tersimpan.');
    }

    // 4. Hapus grup
    public function delete(string $key)
    {
        $groups = $this->settings->get('AuthGroups.groups');
        $matrix = $this->settings->get('AuthGroups.matrix');

        unset($groups[$key], $matrix[$key]);

        $this->settings->set('AuthGroups.groups', $groups);
        $this->settings->set('AuthGroups.matrix', $matrix);
        $this->settings->save();

        return redirect()->back()->with('success','Role dihapus.');
    }
}

?>