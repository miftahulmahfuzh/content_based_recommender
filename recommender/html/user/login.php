<?php include(HTML_FILES_DIR . '/common/header.php') ?>

<div id="contents">
  <div id="r1"> Halaman Login </div>
  <?php include(HTML_FILES_DIR . '/user/form.php') ?>
  <div class="register">
    <h1>Belum mempunyai akun? Daftar disini.</h1>
    <button type="button" onclick="window.location.href='<?php echo get_uri('register.php') ?>';">REGISTER</button>
  </div>
</div>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
