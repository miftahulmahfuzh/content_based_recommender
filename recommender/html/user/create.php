<?php include(HTML_FILES_DIR . '/common/header.php') ?>

<div id="contents">
  <div class="comments">
    <div class="comment">
      <p>Terimakasih atas partisipasinya sebagai tester</p>
      <p>Anda diharuskan untuk login untuk bisa mengakses halaman utama</p>
    </div>
    <button type="button" onclick="window.location.href='<?php echo get_uri('index.php?page=' . $page) ?>';">LOGIN</button>
  </div>
</div>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
