<?php // main.php
// 초기화
require_once 'includes/init.php';
require_once 'includes/elements.php';

// 사이트 로직 ------------------------------------------------

$content = '';
$popups = [];
switch ($ACT) {
  case 'main':
    $content .= makePostPage($ACT, 1);
    $content .= makeList('최신 게시물', 'tile', 'all', 'all', 0, 12);
    $content .= makeList('바로가기', 'tile', 'main', 'link', 0, 4);
    break;
  case 'project':
    $content .= makePostPage($ACT, $ID);
    $content .= makeList('바로가기', 'tile', 'main', 'link', 0, 4);
    break;
  case 'study':
    $content .= makePostPage($ACT, $ID);
    include INC.'thread.php';
    checkPerm(PERM_USER_FRIEND)?$popups[]='thread_write':null;
    checkPerm(PERM_USER_FRIEND)?$popups[]='thread_update':null;
    checkPerm(PERM_USER_FRIEND)?$popups[]='thread_delete':null;
    checkPerm(PERM_USER_FRIEND)?$popups[]='reply_write':null;
    checkPerm(PERM_USER_FRIEND)?$popups[]='reply_delete':null;
    break;
  case 'diary':
    $content .= makePostPage($ACT, $ID);
    break;

  case 'board':
    // TODO: user, do 권한 체크해서 로그인 분기
    include INC.'thread.php';
    $content .= makeThreadList();
    checkPerm(PERM_THREAD_WRITE)?$popups[]='thread_write':null;
    checkPerm(PERM_THREAD_UPDATE)?$popups[]='thread_update':null;
    checkPerm(PERM_THREAD_DELETE)?$popups[]='thread_delete':null;
    checkPerm(PERM_REPLY_WRITE)?$popups[]='reply_write':null;
    checkPerm(PERM_REPLY_DELETE)?$popups[]='reply_delete':null;
    break;

  case 'user':
    if ($USER) {
      switch ($DO) {
        case 'mypage':
          $content .= makeUserPage();
          break;
        case 'logout':
          logout();
          header("Location: $MAIN");
          break;
        case 'signout':
          signout();
          header("Location: $MAIN");
          break;
          
        default:
          header("Location: $MAIN?action=$ACT&do=mypage");
          break;
      }
    } else {
      include INC.'user.php';
      switch ($DO) {
        case 'login':
          $content .= renderElement(TPL.'login.html');
          break;
        case 'signup':
          $content .= renderElement(TPL.'signup.html');
          break;

        default:
          pushLog('로그인 후 이용해주세요.');
          $content .= renderElement(TPL.'login.html');
          break;
      }
    }
    break;

  default : // main
    header("Location: $MAIN");
    break;
}
$popups[] = 'confirm';

// 랜더링 ------------------------------------------------
// 프리로드
preloadLibrary();

// 랜더링
$html_data = array(
  'head' => makeHead(),
  'header' => makeHeader(),
  'content' => $content,
  'leftmenu' => makeSidemenu('left'),
  'rightmenu' => makeSidemenu('right'),
  'footer' => makeFooter(),
  'postScript' => getLibraries('postscripts'),
  'popup' => makePopupList($popups),
);
echo renderElement(TPL.'template.html', $html_data);
