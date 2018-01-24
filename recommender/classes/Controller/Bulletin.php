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
      $this->redirect('login.php');
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
    
    $page = $this->getParam('page');
    $id = $this->getParam('id');
    $alg = $this->getParam('alg');

    if (empty($page)) {
      $page = 0;
    }

    if (empty($id)) {
      $this->err404();
    }

    if (empty($alg)) {
      $alg = 3;
    }

    $model = new Storage_Model($alg);

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

  public function like()
  {
    if (empty($this->getLoggedInId())) {
      $this->err400();
    }

    $page = $this->getParam('page');
    $id = $this->getParam('id');

    if (empty($id)) {
      $this->err404();
    }

    if (empty($page)) {
      $page = 0;
    }

    $category = $this->getParam('category');
    $session = $this->session->get('categories');
    $isliked = false;

    if (!empty($category)) {
      # count all liked category for re-weighting preference 
      $session[$category]++;
      $this->session->set('categories', $session);

      # save article id so it won't be showed anymore during session
      $sessionLiked = $this->session->get('liked');

      if (empty($sessionLiked)) {
        $sessionLiked = array($id);
      } else {
        $sessionLiked[] = $id;
      }

      $this->session->set('liked', $sessionLiked);
      unset($sessionLiked); 

      # set isliked to TRUE so "do you like it?" question won't be showed anymore
      $isliked = true;
    }

    unset($category);
    unset($session);

    $alg = $this->getParam('alg');

    if (!empty($alg)) {
      $accuration = new Storage_Accuration();

      $liked = $accuration->fetch(null, 'id=' . $alg);
      if (!empty($liked)) {
        $accuration->update($alg, array("`like`" => intval($liked[0]['like'])+1));
      }

      unset($liked);
    }

    $this->redirect('article.php', get_defined_vars());
  }

  public function dislike()
  {
    if (empty($this->getLoggedInId())) {
      $this->err400();
    }

    $page = $this->getParam('page');
    $id = $this->getParam('id');
    $alg = $this->getParam('alg');

    if (empty($id)) {
      $this->err404();
    }

    if (empty($page)) {
      $page = 0;
    }

    $sessionDisliked = $this->session->get('disliked');

    if (empty($sessionUnliked)) {
      $sessionDisliked = array($id);
    } else {
      $sessionDisliked[] = $id;
    }

    $this->session->set('disliked', $sessionDisliked);
  
    if (!empty($alg)) {
      $accuration = new Storage_Accuration($alg);

      $disliked = $accuration->fetch(null, 'id=' . $alg);
      if (!empty($disliked)) {
        $accuration->update($alg, array("`dislike`" => intval($disliked[0]['dislike'])+1));
      }
    }

    $this->redirect('index.php');
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
}
