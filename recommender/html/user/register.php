<?php include(HTML_FILES_DIR . '/common/header.php') ?>

<div id="contents">
<?php if (empty($errors) && isset($submit)) : ?>
  <div class="comments">
    <div class="comment">
      <div class="title">
        <?php echo 'Username : ' . h($username) ?>
      </div>
      <div class="title">
        <?php echo 'Password : ' . h($password) ?>
      </div>
    </div>
  </div>
  <div class="confirmForm">
    <form class="default" action="<?php echo get_uri('create.php') ?>" method="post">
      <div class="comment">
        <div id="r1">Isi preferensi topik berita anda</div>
        <div id="r1">1 = Kurang suka -- 5 = Sangat suka</div>
        <div class="preference">Bisnis&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp  : 1&nbsp&nbsp<input type="range" name="bisnis" min=1 max=5>&nbsp&nbsp5</div>
        <div class="preference">Olahraga &nbsp : 1&nbsp&nbsp<input type="range" name="olahraga" min=1 max=5>&nbsp&nbsp5</div>
        <div class="preference">Selebriti&nbsp&nbsp&nbsp : 1&nbsp&nbsp<input type="range" name="selebriti" min=1 max=5>&nbsp&nbsp5</div>
        <div class="preference">Otomotif &nbsp : 1&nbsp&nbsp<input type="range" name="otomotif" min=1 max=5>&nbsp&nbsp5</div>
        <div class="preference">Teknologi : 1&nbsp&nbsp<input type="range" name="teknologi" min=1 max=5>&nbsp&nbsp5</div>
      </div>
      <input type="hidden" name="username" value="<?php echo h($username) ?>" />
      <input type="hidden" name="password" value="<?php echo h($password) ?>" />
      <input type="submit" name="submit" value="SUBMIT">
      <input type="submit" value="BACK">
    </form>
  </div>
<?php else : ?>
  <div id="r1"> Registrasi Akun Baru </div>
  <?php include(HTML_FILES_DIR . '/user/form.php') ?>
<?php endif ?>
</div>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
