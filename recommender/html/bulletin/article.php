<?php include(HTML_FILES_DIR . '/common/header.php') ?>

<div id="contents">
  <?php include(HTML_FILES_DIR . '/common/error.php') ?>
      
  <div class="comments">
    <div class="comment">
      <div class="title">
        <?php echo h($article[0]['title']) ?>
      </div>
      <div class="body">
        <?php echo nl2br(h($article[0]['content'])) ?>
      </div>
      <div class="date">
         <?php echo h($article[0]['date']) ?>
      </div>
      <div class="title">
        <input type="button" value='source : <?php echo h(substr($article[0]['url'],0,37))."..." ?>' onclick="window.location.href='<?php echo h($article[0]['url']) ?>';">
      </div>
    </div>
  </div>

  <div id="contents">
    <?php if ($others) : ?>
      <div class="title">
      <h2>Artikel lain yang serupa</h2>
      </div>
      <?php foreach ($others as $other) : ?>
        <div class="comment">
          <div class="title">
            <input type="button" value='<?php echo h($other['title']) ?>' onclick="window.location.href='<?php echo get_uri('article.php').'?id='.$other['id'] ?>';">
          </div>
          <div class="body">
            <?php echo nl2br(h(substr($other['content'],0,150))."...") ?>
          </div>
          <div class="date">
            <?php echo h($other['date']) ?>
          </div>
        </div>
      <?php endforeach ?>
    <?php endif ?>
  </div>

  <div class="confirmForm">
    <?php if (!empty($comment['pass']) || $this->getLoggedInId()) : ?>
      <form class="default" action="<?php echo get_uri('delete.php') ?>" method="post">
        <input type="hidden" name="comment_id" value="<?php echo $comment['id'] ?>" />
        <input type="hidden" name="page" value="<?php echo $page ?>" />
        <input type="hidden" name="pass" value="<?php echo $pass ?>" />
        <?php if (empty($errors)) : ?>
          <div class="message">
            Are you sure ?
          </div>
          <div class="submit"> 
            <input type="hidden" name="do_delete" value="1" />
            <input type="submit" value="DELETE" />
            <input type="button" value="CANCEL" onclick="window.location.href='<?php echo get_uri('index.php') ?>?page=<?php echo $page ?>';">
          </div>
        <?php else : ?>
          <div class="submit">
            <input type="password" name="pass" value="<?php echo $pass ?>" />
            <input type="submit" value="DELETE" />
          </div>
        <?php endif ?>
      </form>
    <?php else : ?>
      <form class="default" action="<?php echo get_uri('index.php') ?>" method="get">
        <div class="message">
          This comment can't be deleted.
        </div>
        <div class="submit">
          <input type="hidden" name="page" value="<?php echo $page ?>" />
          <input type="submit" value="BACK">
        </div>
      </form>
    <?php endif ?>
  </div>
</div>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
