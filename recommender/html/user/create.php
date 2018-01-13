<?php include(HTML_FILES_DIR . '/common/header.php') ?>

<div id="contents">
  <div class="comments">
    <div class="comment">
      <p>Thank you for membership register.</p>
      <p>We send confirmation email to you (expired in 24 hours).</p>
      <p>Please complete registration by clicking the confirmation URL in the email.</p>
    </div>
    <button type="button" onclick="window.location.href='<?php echo get_uri('index.php?page=' . $page) ?>';">BACK TO TOP</button>
  </div>
</div>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
