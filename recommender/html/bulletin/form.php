<?php include(HTML_FILES_DIR . '/common/error.php') ?>
<?php $_action = (isset($isEditForm)) ? 'edit.php' : 'post.php' ?>
<form class="default" action="<?php echo get_uri($_action) ?>" method="post" enctype="multipart/form-data">
  <div class="user">
    <?php if($this->getLoggedInId()) : ?>
      <button type="button" onclick="window.location.href='<?php echo get_uri('logout.php') ?>';"/>LOGOUT</button>
    <?php else : ?>
      <button type="button" onclick="window.location.href='<?php echo get_uri('login.php') ?>';"/>LOGIN</button>
    <?php endif ?>
      <button type="button" onclick="window.location.href='<?php echo get_uri('register.php') ?>?page=<?php echo $page ?>';">REGISTER</button>
  </div>
  <div class="item">
    <p class="title">
      Name (Optional)
    </p>
    <p class="input">
      <?php if (isset($username)) : ?>
        <input type="text" name="username" value="<?php echo h($username) ?>" />
      <?php else : ?>
        <input type="text" name="username" value="<?php echo h($this->getLoggedInId() ? $this->session->get('username') : '') ?>" />
      <?php endif ?>
    </p>
  </div>
  <div class="item">
    <p class="title">
      Title
    </p>
    <p class="input">
      <input type="text" name="title" value="<?php if (isset($title)) echo h($title) ?>" />
    </p>
  </div>
  <div class="item">
    <p class="title">
      Body
    </p>
    <p class="input">
      <textarea style="height: 80px;" name="body"><?php if (isset($body)) echo h($body) ?></textarea>
    </p>
  </div>
  <div class="item">
    <p class="title">
      Photo (Optional)
    </p>
    <p class="input">
      <input type="file" name="image" />
    </p>
  </div>
  <?php if (isset($isEditForm)) : ?>
    <?php if (!empty($currentImage)) : ?>
    <div class="item">
      <p class="title">
        Current Photo
      </p>
      <p class="input">
        <img class="photo" src="<?php echo $imageDir ?>/<?php echo $currentImage ?>" /><br />
        <input id="cpd" type="checkbox" name="del_image" value="1" />
        <label for="cpd">Delete Current Photo</label>
      </p>
    </div>
    <?php endif ?>
    <div class="submit"> 
      <input type="hidden" name="do_edit" value="1" />
      <input type="hidden" name="comment_id" value="<?php if (isset($id)) echo $id ?>" />
      <input type="hidden" name="page" value="<?php if (isset($page)) echo h($page) ?>" />
      <input type="hidden" name="pass" value="<?php if (isset($pass)) echo h($pass) ?>" />
      <input type="submit" value="EDIT" />
      <input type="button" value="CANCEL" onclick="window.location.href='<?php echo get_uri('index.php?page=' . $page) ?>';">
    </div>
  <?php else : ?>
    <?php if (!$this->getLoggedInId()) : ?>
      <div class="item">
        <p class="title">
          Password (Optional)
        </p>
        <p class="input">
          <input type="password" name="pass" value="<?php if (isset($pass)) echo h($pass) ?>" />
        </p>
      </div>
    <?php endif ?>
    <div class="submit">
      <input type="submit" value="POST COMMENT" />
    </div>
  <?php endif ?>
</form>
