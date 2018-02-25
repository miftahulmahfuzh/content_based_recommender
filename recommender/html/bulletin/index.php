<?php include(HTML_FILES_DIR . '/common/header.php') ?>

<script type="text/javascript">
function submit_action_form(action, form_id) {
  var form = document.getElementById(form_id);
  form.setAttribute('action', action);
  form.submit();
}
</script>

<div id="contents">
  <?php if ($articles) : ?>
  <div class="comments">
    <?php foreach ($articles as $article) : ?>
      <div class="comment">
        <div class="title">
          <input type="button" value='<?php echo h(substr($article['title'],0,67))."..." ?>' onclick="window.location.href='<?php echo get_uri('article.php').'?page='.$pager->getCurrentPage().'&id='.$article['id'] ?>';">
        </div>
        <div class="category">
          <?php echo "Category : ".h($article['category'])?>
        </div>
        <div class="body">
          <?php echo nl2br(h(substr($article['content'],0,150))."...") ?>
        </div>
        <div class="date">
          <?php echo h($article['date']) ?>
        </div>
      </div>
    <?php endforeach ?>
    <input type="button" value='REFRESH RECOMMENDATION' onclick="window.location.href='<?php echo get_uri('index.php').'?status=r' ?>';">
  </div>
  <?php endif ?>
</div>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
