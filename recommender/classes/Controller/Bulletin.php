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
    $userid = $this->getLoggedInId();

    if (!$userid) {
      $this->redirect('login.php', get_defined_vars());
    }

    $bulletin = new Storage_Bulletin();
    $preference = new Storage_Preference();
    
    $pager = $this->createPager($bulletin->getCount());

    $page = $this->getParam('page');

    if ($page && !$pager->isValidPageNumber($page)) {
      $this->err404();
    }
    
    $pager->setCurrentPage($page);
    
    $userPrefs = $preference->fetch(null, 'id = '.$userid);
    $userPrefq = null;

    if (!empty($userPrefs)) {
      unset($userPrefs[0]['id']);
      arsort($userPrefs[0]);
      $userPrefq = "FIELD(category,'".implode("','", array_keys($userPrefs[0]))."')";
    }

    $liked = $this->session->get('liked');
    $likedId = null;

    if (!empty($liked)) {
      $likedId = 'id != ' . implode(" AND id != ", $liked); 
    }

    $comments = $bulletin->fetch(
      null, $likedId, $userPrefq,
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
    $liked = $this->session->get('liked');
    $isliked = false;

    if (!empty($liked)) {
      if (in_array($id, $liked)) {
        $isliked = true;
      }
    }

    $this->render('bulletin/article.php', get_defined_vars());
  }

  public function rate()
  {
    if (empty($this->getLoggedInId())) {
      $this->err400();
    }

    $page = $this->getParam('page');
    $id = $this->getParam('id');

    if (empty($page)) {
      $page = 0;
    }

    $category = $this->getParam('category');
    $isliked = false;
    $session = $this->session->get('categories');

    if (!empty($category)) {
      $session[$category]++;
      $this->session->set('categories', $session);
      $liked = $this->session->get('liked');

      if (empty($liked)) {
        $liked = array($id);
      } else {
        $liked[] = $id;
      }

      $this->session->set('liked', $liked);
      $isliked = true;
      unset($liked); 
    }

    unset($category);
    unset($session);

    $this->redirect('article.php', get_defined_vars());
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
