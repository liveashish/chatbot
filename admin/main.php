<?php
//-----------------------------------------------------------------------------------------------
//My Program-O Version 2.0.9
//Program-O  chatbot admin area
//Written by Elizabeth Perreau and Dave Morton
//Aug 2011
//for more information and support please visit www.program-o.com
//-----------------------------------------------------------------------------------------------
// main.php
    $noRightNav    = $template->getSection('NoRightNav');
    $logo          = $template->getSection('Logo');
    $topNav        = $template->getSection('TopNav');
    $leftNav       = $template->getSection('LeftNav');
    $main          = $template->getSection('Main');
    $rightNav      = '';
    $footer        = trim($template->getSection('Footer'));
    #$lowerScripts  = '';
    #$pageTitleInfo = '';
    $divDecoration = $template->getSection('DivDecoration');
    $topNavLinks   = makeLinks('top', $topLinks, 12);
    $navHeader     = $template->getSection('NavHeader');
    $leftNavLinks  = makeLinks('left', $leftLinks, 12);
    $mainTitle     = 'Home';
    $rightNavLinks = '';
    $FooterInfo    = getFooter();
    $titleSpan     = $template->getSection('TitleSpan');
    $errMsgStyle   = (!empty($msg)) ? "ShowError" : "HideError";
    $errMsgStyle   = $template->getSection($errMsgStyle);
    $mediaType     = ' media="screen"';
    $upperScripts  = '';
    $noLeftNav     = '';
    $noTopNav      = '';
    $pageTitle     = 'My-Program O - Main Page';
    $headerTitle   = 'Actions:';
    $mainContent   = <<<endMain
        <p>
          Welcome to 'My Program-O', the Program-O chatbot admin area. Please
          use the links above or to the left to perform administrative tasks,
          as needed.
        </p>
        <div id="rssContainer">
         <div id="rssTitle">Latest News from Program-O.com</div>
         <div id="rssOutput">[rssOutput]</div>
        </div>
endMain;
  $mainContent = str_replace('[rssOutput]', getRSS(), $mainContent);

?>