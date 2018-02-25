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
    
    $status = $this->getParam('status');

    $userPrefsTmp = $preference->fetch(null, 'id = '.$userid);
    $articles = array();

    if (!empty($userPrefsTmp)) {
      $userPrefs = $userPrefsTmp[0];
      unset($userPrefs['id']);
      arsort($userPrefs);

      # Get value of variable A for each category
      $prefsA = array(); 
      foreach (array_keys($userPrefs) as $category) {
        $prefsA[$category] = $userPrefs[$category] / array_sum($userPrefs); 
        $prefsA[$category] *= $pager->getItemsPerPage();
      }
      $this->session->set('prefs_A', $prefsA);

      $liked = $this->session->get('liked');
      $likedId = null;

      if (!empty($liked)) {
        $likedId = 'id != ' . implode(" AND id != ", $liked); 
      }

      $disliked = $this->session->get('disliked');
      $dislikedId = null;

      if (!empty($disliked)) {
        $dislikedId = 'id != ' . implode(" AND id != ", $disliked); 
      }

      $allIdShowed = $this->session->get('all_id_showed');
      $showedId = null;

      if (!empty($allIdShowed)) {
        $showedId = 'id != ' . implode(" AND id != ", $allIdShowed); 
      }

      # Fetch articles for each category
      foreach (array_keys($userPrefs) as $category) {
        $categoryQ = "category='" . $category . "'";

        if (!empty($likedId)) {
          $categoryQ = $likedId . " AND " . $categoryQ;
        } 

        if (!empty($dislikedId)) {
          $categoryQ = $dislikedId . " AND " . $categoryQ;
        } 

        if (!empty($showedId)) {
          $categoryQ = $showedId . " AND " . $categoryQ;
        } 

        $articlesTmp = $bulletin->fetch(null, $categoryQ, null, null, round($prefsA[$category]));
        $articles = array_merge($articles, $articlesTmp);
      } 

      # save fetched articles id in session
      $currentIdShowed = array();
      foreach ($articles as $article) {
        array_push($currentIdShowed, $article['id']);
      }

      $this->session->set('current_id_showed', $currentIdShowed);

      # update all showed article for refresh recommendation
      $allIdShowed = $this->session->get('all_id_showed');
      if (empty($allIdShowed)) {
        $allIdShowed = $currentIdShowed;
      }

      $status = $this->getParam('status');
      if ($status == 'r') { 
        $allIdShowed = array_unique(array_merge($allIdShowed, $currentIdShowed), SORT_REGULAR);
        $this->session->set('all_id_showed', $allIdShowed);
      }
    }

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

    $id = $this->getParam('id');

    if (empty($id)) {
      $this->err404();
    }

    $category = $this->getParam('category');
    $session = $this->session->get('l_categories');
    if ($session == null) {
      $session = array();
    }

    $isliked = false;

    if (!empty($category)) {
      # count all liked category for re-weighting preference 
      if ($session[$category] == null) {
        $session[$category] = 0;
      }

      $session[$category]++;
      $this->session->set('l_categories', $session);

      # save article id so it won't be showed anymore during session
      $sessionLiked = $this->session->get('liked');

      if (empty($sessionLiked)) {
        $sessionLiked = array($id);
      } else {
        $sessionLiked[] = $id;
      }

      $this->session->set('liked', array_unique($sessionLiked, SORT_REGULAR));
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

    $id = $this->getParam('id');

    if (empty($id)) {
      $this->err404();
    }

    $category = $this->getParam('category');
    $session = $this->session->get('h_categories');
    if ($session == null) {
      $session = array();
    }

    $isliked = false;

    if (!empty($category)) {
      # count all liked category for re-weighting preference 
      if ($session[$category] == null) {
        $session[$category] = 0;
      }

      $session[$category]++;
      $this->session->set('h_categories', $session);

      # save article id so it won't be showed anymore during session
      $sessionDisliked = $this->session->get('disliked');

      if (empty($sessionDisliked)) {
        $sessionDisliked = array($id);
      } else {
        $sessionDisliked[] = $id;
      }

      $this->session->set('disliked', array_unique($sessionDisliked, SORT_REGULAR));
    }
  
    $alg = $this->getParam('alg');

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
