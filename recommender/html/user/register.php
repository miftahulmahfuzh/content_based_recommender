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
      <input type="hidden" name="username" value="<?php echo h($username) ?>" />
      <input type="hidden" name="password" value="<?php echo h($password) ?>" />
      <input type="submit" name="submit" value="SUBMIT">
      <input type="submit" value="BACK">
    </form>
  </div>
<?php else : ?>
  <?php include(HTML_FILES_DIR . '/user/form.php') ?>
<?php endif ?>
</div>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
