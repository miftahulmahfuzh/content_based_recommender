<?php include(HTML_FILES_DIR . '/common/header.php') ?>

<script type="text/javascript">
function submit_action_form(action, form_id) {
  var form = document.getElementById(form_id);
  form.setAttribute('action', action);
  form.submit();
}
</script>

<div id="contents">
  <?php if ($comments) : ?>
  <div class="comments">
    <?php foreach ($comments as $comment) : ?>
      <div class="comment">
        <div class="title">
          <input type="button" value='<?php echo h($comment['title']) ?>' onclick="window.location.href='<?php echo get_uri('article.php').'?page='.$pager->getCurrentPage().'&id='.$comment['id'] ?>';">
        </div>
        <div class="category">
          <?php echo "Category : ".h($comment['category'])?>
        </div>
        <div class="body">
          <?php echo nl2br(h(substr($comment['content'],0,150))."...") ?>
        </div>
        <div class="date">
          <?php echo h($comment['date']) ?>
        </div>
      </div>
    <?php endforeach ?>
  </div>
  <?php endif ?>

  <?php include(HTML_FILES_DIR . '/common/pager.php') ?>
</div>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
