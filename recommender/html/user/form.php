<?php include(HTML_FILES_DIR . '/common/error.php') ?>
<div id="contents">
  <?php $_action = (isset($isLoginForm)) ? 'login.php' : 'register.php' ?>
  <form class="default" action="<?php echo get_uri($_action) ?>" method="post" enctype="multipart/form-data">
    <div class="item">
      <p class="title">
        Username
      </p>
      <p class="input">
        <input type="text" name="username" value="<?php if (isset($username)) echo h($username) ?>" />
      </p>
    </div>
    <div class="item">
      <p class="title">
        Password
      </p>
      <p class="input">
        <input type="password" name="password" value="<?php if (isset($password)) echo h($password) ?>" />
      </p>
    </div>
    <div class="submit">
      <input type="submit" name="submit" value="CONFIRM" />
      <button type="button" onclick="window.location.href='<?php echo get_uri('index.php?page=' . $page) ?>';">BACK</button>
    </div>
  </form>
</div>
