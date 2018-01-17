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
    <?php if ($isliked) : ?>
      <div id="r1"> Anda menyukai artikel ini </div>
    <?php else : ?>
      <div class="confirmForm">
        <?php if ($this->getLoggedInId()) : ?>
          <div id="rate">Apakah anda menyukai artikel ini?</div>
          <form class="default" action="<?php echo get_uri('rate.php') ?>" method="post">
            <input type="hidden" name="id" value="<?php echo $article[0]['id'] ?>" />
            <input type="hidden" name="category" value="<?php echo $article[0]['category'] ?>" />
            <input type="hidden" name="page" value="<?php echo $page ?>" />
            <div class="submit">
              <input type="submit" value="YA" />
              <input type="button" value='TIDAK' onclick="window.location.href='<?php echo get_uri('index.php').'?page='.$page ?>';">
            </div>
          </form>
        <?php endif ?>
      </div>
    <?php endif ?>
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
</div>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
