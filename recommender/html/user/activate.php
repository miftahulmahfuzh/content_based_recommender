<?php include(HTML_FILES_DIR . '/common/header.php') ?>

<div id="contents">
  <div class="comments">
    <div class="comment">
      <?php if ($expired) : ?>
        <p>We are sorry, your 24 hours activation time is over..</p>
        <p>Please register again</p>
      <?php else : ?>
        <p>Thank you for registration.</p>
        <p>Registration is now complete.</p>
      <?php endif ?>
    </div>
    <button type="button" onclick="window.location.href='<?php echo get_uri('index.php') ?>';">BACK TO TOP</button>
  </div>
</div>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
