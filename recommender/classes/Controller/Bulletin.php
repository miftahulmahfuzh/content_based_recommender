<?php

class Controller_Bulletin extends Controller_Application
{
  const PAGER_ITEMS_PER_PAGE = 10;
  const PAGER_WINDOW_SIZE    = 5;

  protected $imageDir = '';

  public function __construct()
  {
    $this->imageDir = Uploader_File::UPLOAD_DIR_NAME . '/bulletin';
  }

  public function index()
  {
    if (!$this->getLoggedInId()) {
      $this->redirect('login.php', get_defined_vars());
    }

    $username = $this->session->get('username');

    $bulletin = new Storage_Bulletin();
    
    $pager = $this->createPager($bulletin->getCount());

    $page = $this->getParam('page');

    if ($page && !$pager->isValidPageNumber($page)) {
      $this->err404();
    }
    
    $pager->setCurrentPage($page);
    
    $comments = $bulletin->fetch(
      null, null, null,
      $pager->getOffset(),
      $pager->getItemsPerPage()
    );

    $this->render('bulletin/index.php', get_defined_vars());
  }

  public function article()
  {
    $bulletin = new Storage_Bulletin();
    $model = new Storage_Model();
    
    $page = $this->getParam('page');
    $id = $this->getParam('id');

    if (empty($page)) {
      $page = 0;
    }

    if (empty($id)) {
      $this->err404();
    }

    $article = $bulletin->fetch(null, 'id = ' . $bulletin->escape($id)); 
    $similar = $model->fetch(null, 'id = ' . $model->escape($id));

    if (empty($article) || empty($similar)) {
      $this->err404();
    }

    $similarIdList = explode(" ",$similar[0]['similar_articles']);
    $similarIdList = array_slice($similarIdList, 0, 5, true); 
    $similarId = 'id = ' . implode(" OR id = ", $similarIdList);

    $others = $bulletin->fetch(null, $similarId);

    $this->render('bulletin/article.php', get_defined_vars());
  }

  public function post()
  {
    $username = $this->getParam('username');
    $title    = $this->getParam('title');
    $body     = $this->getParam('body');
    $pass     = $this->getParam('pass');

    $data = array(
      'username' => $username,
      'title'    => $title,
      'body'     => $body,
      'pass'     => $pass,
    );

    $bulletin = new Storage_Bulletin();
    $errors   = $bulletin->validate($data);

    $uploader = $this->createImageUploader();
    $image    = $this->getFile('image');
    $hasImage = !empty($image);
    
    if ($hasImage) {
      $errors = array_merge($errors, $uploader->validate($image));
    }
    
    if (empty($errors)) {
      if ($hasImage) {
        $data['image'] = $uploader->uploadImage($image['data']);
      } else {
        $data['image'] = null;
      }

      $userId = $this->getLoggedInId();

      if ($userId) {
        $data['user_id'] = $userId;
      }
      
      $bulletin->insert($data);

      $this->redirect('index.php');
    } else {
      $this->render('bulletin/post.php', get_defined_vars());
    }
  }
  
  public function delete()
  {
    $id   = $this->getParam('comment_id');
    $pass = $this->getParam('pass');
    $page = $this->getParam('page');

    if (empty($id)) {
      $this->err400();
    }

    $page = (empty($page)) ? 1 : (int)$page;

    $bulletin = new Storage_Bulletin();
    $results  = $bulletin->fetch(null, 'id = ' . $bulletin->escape($id));

    if (!isset($results[0]) || $results[0]['is_deleted'] === '1') {
      $this->err404();
    }

    $comment = $results[0];
    $member  = $comment['user_id'];

    if (!empty($member) && $member !== $this->getLoggedInId()) {
      $this->err404();
    }

    $errors = array();

    if (!empty($comment['pass']) || !empty($member)) {
      if (empty($member)) {
        if (hash_password($pass) !== $comment['pass']) {
          $errors[] = 'The password you entered, do not match.';
        }
      }

      if (empty($errors) && $this->getParam('do_delete') === '1') {
        if (!empty($comment['image'])) {
          $this->createImageUploader()->delete($comment['image'], true);
        }
        
        $count = $bulletin->getCount(null, 'is_deleted = 0');
        if ($page > 1 &&
            ($count % self::PAGER_ITEMS_PER_PAGE) === 1 &&
            ($page === (int)ceil($count / self::PAGER_ITEMS_PER_PAGE))) {
          $page--;
        }
        
        $bulletin->softDelete($id);
         
        $this->redirect('index.php', array('page' => $page));
      }
    }
    
    $this->render('bulletin/delete.php', get_defined_vars());
  }

  public function edit()
  {
    $id    = $this->getParam('comment_id');
    $pass  = $this->getParam('pass');
    $page  = $this->getParam('page');

    if (empty($id)) {
      $this->err400();
    }

    if (empty($page)) {
      $page = 1;
    }

    $bulletin = new Storage_Bulletin();

    $results = $bulletin->fetch(null, 'id = ' . $bulletin->escape($id));

    if (!isset($results[0]) || $results[0]['is_deleted'] === '1') {
      $this->err404();
    }

    $comment = $results[0];
    $member  = $comment['user_id'];

    if (!empty($member) && $member !== $this->getLoggedInId()) {
      $this->err404();
    }

    $username     = $comment['username'];
    $title        = $comment['title'];
    $body         = $comment['body'];
    $currentImage = $comment['image'];

    $isEditForm      = true;
    $isPasswordMatch = false;

    $errors = array();
    
    if (!empty($comment['pass']) || !empty($member)) {
      if (empty($member)) {
        if (hash_password($pass) === $comment['pass']) {
          $isPasswordMatch = true;
        } else {
          $errors[] = 'The password you entered, do not match.';
        }
      } 
      
      if (empty($errors) && $this->getParam('do_edit') === '1') {
        $username = $this->getParam('username');
        $title    = $this->getParam('title');
        $body     = $this->getParam('body');

        $data = array(
          'username' => $username,
          'title'    => $title,
          'body'     => $body,
        );

        $doDeleteImage = ($this->getParam('del_image') === '1');
        
        $bulletin = new Storage_Bulletin();
        $errors   = $bulletin->validate($data);
        
        $uploader = $this->createImageUploader();
        $image    = $this->getFile('image');
        $hasImage = !empty($image);

        if (!$doDeleteImage && $hasImage) {
          $errors = array_merge($errors, $uploader->validate($image));
        }
        
        if (empty($errors)) {
          if ($doDeleteImage) {
            if (!empty($currentImage)) {
              $uploader->delete($currentImage, true);
              $data['image'] = null;
            }
          } elseif ($hasImage) {
            $data['image'] = $uploader->uploadImage($image['data']);
          }
          
          $bulletin->update($id, $data);

          $this->redirect('index.php', array('page' => $page));
        }
      }
    }
    
    $this->render('bulletin/edit.php', get_defined_vars());
  }

  protected function createPager($itemsCount)
  {
    $pager = new Pager(
      $itemsCount,
      self::PAGER_ITEMS_PER_PAGE,
      self::PAGER_WINDOW_SIZE
    );

    $pager->setUri($this->getEnv('Request-Uri'));

    return $pager;
  }

  protected function createImageUploader()
  {
    return new Uploader_Image($this->imageDir);
  }
}
