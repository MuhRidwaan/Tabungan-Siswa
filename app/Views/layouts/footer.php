  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>&copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 3.2.0
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="<?= base_url('plugins/jquery/jquery.min.js') ?>"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?= base_url('plugins/jquery-ui/jquery-ui.min.js') ?>"></script>
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?= base_url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- ChartJS -->
<script src="<?= base_url('plugins/chart.js/Chart.min.js') ?>"></script>
<!-- Sparkline -->
<script src="<?= base_url('plugins/sparklines/sparkline.js') ?>"></script>
<!-- JQVMap -->
<script src="<?= base_url('plugins/jqvmap/jquery.vmap.min.js') ?>"></script>
<script src="<?= base_url('plugins/jqvmap/maps/jquery.vmap.usa.js') ?>"></script>
<!-- jQuery Knob Chart -->
<script src="<?= base_url('plugins/jquery-knob/jquery.knob.min.js') ?>"></script>
<!-- daterangepicker -->
<script src="<?= base_url('plugins/moment/moment.min.js') ?>"></script>
<script src="<?= base_url('plugins/daterangepicker/daterangepicker.js') ?>"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?= base_url('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
<!-- Summernote -->
<script src="<?= base_url('plugins/summernote/summernote-bs4.min.js') ?>"></script>
<!-- overlayScrollbars -->
<script src="<?= base_url('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('dist/js/adminlte.js') ?>"></script>
<!-- Select2 -->
<script src="<?= base_url('plugins/select2/js/select2.full.min.js') ?>"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- DataTables & Plugins -->
<script src="<?= base_url('plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') ?>"></script>

<script>
$(function() {
  if ($.fn.select2) {
    $('.select2').select2({
      theme: 'bootstrap4',
      width: '100%'
    });
  }

  // Auto initialize DataTables
  if ($.fn.DataTable) {
    // 1. Full Client-Side DataTables (.data-table or .table-datatable)
    $('.data-table, .table-datatable').each(function() {
      if (!$.fn.DataTable.isDataTable(this)) {
        $(this).DataTable({
          "responsive": true,
          "lengthChange": true,
          "autoWidth": false,
          "ordering": true,
          "order": [],
          "language": {
            "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
            "sProcessing":   "Sedang memproses...",
            "sLengthMenu":   "Tampilkan _MENU_ data",
            "sZeroRecords":  "Tidak ditemukan data yang sesuai",
            "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 data",
            "sInfoFiltered": "(disaring dari _MAX_ data keseluruhan)",
            "sInfoPostFix":  "",
            "sSearch":       "Cari Data:",
            "sUrl":          "",
            "oPaginate": {
              "sFirst":    "Pertama",
              "sPrevious": "Sebelumnya",
              "sNext":     "Selanjutnya",
              "sLast":     "Terakhir"
            }
          }
        });
      }
    });

    // 2. Server-Side CI Pager Tables (.data-table-server): Column sorting & styling without double pagination or double search
    $('.data-table-server').each(function() {
      if (!$.fn.DataTable.isDataTable(this)) {
        $(this).DataTable({
          "responsive": true,
          "paging": false,
          "info": false,
          "searching": false,
          "lengthChange": false,
          "autoWidth": false,
          "ordering": true,
          "order": []
        });
      }
    });
  }
});

document.addEventListener('DOMContentLoaded', function() {
  <?php if (session()->getFlashdata('success')) : ?>
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: <?= json_encode(session()->getFlashdata('success')) ?>,
      timer: 3000,
      showConfirmButton: false
    });
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')) : ?>
    Swal.fire({
      icon: 'error',
      title: 'Gagal!',
      text: <?= json_encode(session()->getFlashdata('error')) ?>
    });
  <?php endif; ?>

  <?php if (session()->getFlashdata('warning')) : ?>
    Swal.fire({
      icon: 'warning',
      title: 'Perhatian!',
      text: <?= json_encode(session()->getFlashdata('warning')) ?>
    });
  <?php endif; ?>

  <?php if (session()->getFlashdata('info')) : ?>
    Swal.fire({
      icon: 'info',
      title: 'Informasi',
      text: <?= json_encode(session()->getFlashdata('info')) ?>
    });
  <?php endif; ?>
});
</script>
</body>
</html>
