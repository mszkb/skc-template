<?php
/**
 * Template Functions
 *
 * This file provides template specific custom functions that are
 * not provided by the DokuWiki core.
 * It is common practice to start each function with an underscore
 * to make sure it won't interfere with future core functions.
 */

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();

define('TPL_URL', __DIR__);

//error_reporting(E_ALL);

require_once( TPL_URL . "/overwrite.php" );

/**
 * Create link/button to discussion page and back
 *
 * @author Anika Henke <anika@selfthinker.org>
 */
function _tpl_discussion($discussionPage, $title, $backTitle, $link=0, $wrapper=0, $return=0) {
    global $ID;
    $output = '';

    $discussPage    = str_replace('@ID@', $ID, $discussionPage);
    $discussPageRaw = str_replace('@ID@', '', $discussionPage);
    $isDiscussPage  = strpos($ID, $discussPageRaw) !== false;
    $backID         = ':'.str_replace($discussPageRaw, '', $ID);

    if ($wrapper) $output .= "<$wrapper>";

    if ($isDiscussPage) {
        if ($link) {
            ob_start();
            tpl_pagelink($backID, $backTitle);
            $output .= ob_get_contents();
            ob_end_clean();
        } else {
            $output .= html_btn('back2article', $backID, '', array(), 'get', 0, $backTitle);
        }
    } else {
        if ($link) {
            ob_start();
            tpl_pagelink($discussPage, $title);
            $output .= ob_get_contents();
            ob_end_clean();
        } else {
            $output .= html_btn('discussion', $discussPage, '', array(), 'get', 0, $title);
        }
    }

    if ($wrapper) $output .= "</$wrapper>";
    if ($return) return $output;
    echo $output;
}

/**
 * Create link/button to user page
 *
 * @author Anika Henke <anika@selfthinker.org>
 */
function _tpl_userpage($userPage, $title, $link=0, $wrapper=0, $return=0) {
    if (empty($_SERVER['REMOTE_USER'])) return;

    global $conf;
    $output = '';
    $userPage = str_replace('@USER@', $_SERVER['REMOTE_USER'], $userPage);

    if ($wrapper) $output .= "<$wrapper>";

    if ($link) {
        ob_start();
        tpl_pagelink($userPage, $title);
        $output .= ob_get_contents();
        ob_end_clean();
    } else {
        $output .= html_btn('userpage', $userPage, '', array(), 'get', 0, $title);
    }

    if ($wrapper) $output .= "</$wrapper>";
    if ($return) return $output;
    echo $output;
}

/**
 * Wrapper around custom template actions
 *
 * @author Anika Henke <anika@selfthinker.org>
 */
function _tpl_action($type, $link=0, $wrapper=0, $return=0) {
    switch ($type) {
        case 'discussion':
            if (tpl_getConf('discussionPage')) {
                $output = _tpl_discussion(tpl_getConf('discussionPage'), tpl_getLang('discussion'), tpl_getLang('back_to_article'), $link, $wrapper, 1);
                if ($return) return $output;
                echo $output;
            }
            break;
        case 'userpage':
            if (tpl_getConf('userPage')) {
                $output = _tpl_userpage(tpl_getConf('userPage'), tpl_getLang('userpage'), $link, $wrapper, 1);
                if ($return) return $output;
                echo $output;
            }
            break;
    }
}

/**
 * copied to core (available since Detritus)
 */
if (!function_exists('tpl_toolsevent')) {
    function tpl_toolsevent($toolsname, $items, $view='main') {
        $data = array(
            'view'  => $view,
            'items' => $items
        );

        $hook = 'TEMPLATE_'.strtoupper($toolsname).'_DISPLAY';
        $evt = new Doku_Event($hook, $data);
        if($evt->advise_before()){
            foreach($evt->data['items'] as $k => $html) echo $html;
        }
        $evt->advise_after();
    }
}

/**
 * copied from core (available since Binky)
 */
if (!function_exists('tpl_classes')) {
    function tpl_classes() {
        global $ACT, $conf, $ID, $INFO;

        $restOfURL = $_SERVER['REQUEST_URI'];

        // If you want to remove the slash at the beginning you can use ltrim()
        $restOfURL = ltrim($restOfURL, "/");

        $classes = array(
            'dokuwiki',
            'mode_'.$ACT,
            'tpl_'.$conf['template'],
            !empty($_SERVER['REMOTE_USER']) ? 'loggedIn' : '',
            $INFO['exists'] ? '' : 'notFound',
            ($ID == $conf['start']) ? 'home' : '',
        );
        return join(' ', $classes);
    }
}

/**
 * Renders the logo URL as an img
 *
 * @param $logo_url
 * @return null
 */
function skc_tpl_render_logo($logo_url) {
    if (!filter_var($logo_url, FILTER_VALIDATE_URL)) {
        var_dump($logo_url);
        return null;
    }

    $whatINeed = explode('/', $_SERVER['REQUEST_URI']);
    $whatINeed = $whatINeed[1];

    if($whatINeed == "sepm") {
        $logo_url = 'https://wiki.superkawaiicrew.at/_media/sepm.png';
    } else if(!skc_tpl_is_image($logo_url)) {
        echo "URL is not an Image\n";
        return null;
    }
    echo ("<img src='$logo_url' />");
}

/**
 * Checks if given URL is an image
 *
 * @param $logo_url
 * @return bool
 */
function skc_tpl_is_image($logo_url) {

    if(!skc_tpl_url_exists($logo_url)) {
        echo 'URL does not exist<br>';
        return false;
    }

    $url_headers=get_headers($logo_url, 1);

    if(isset($url_headers['Content-Type'])){

        $type=strtolower($url_headers['Content-Type']);

        $valid_image_type=array();
        $valid_image_type['image/png']='';
        $valid_image_type['image/jpg']='';
        $valid_image_type['image/jpeg']='';
        $valid_image_type['image/jpe']='';
        $valid_image_type['image/gif']='';
        $valid_image_type['image/tif']='';
        $valid_image_type['image/tiff']='';
        $valid_image_type['image/svg']='';
        $valid_image_type['image/ico']='';
        $valid_image_type['image/icon']='';
        $valid_image_type['image/x-icon']='';

        if(isset($valid_image_type[$type])){
            return true;
        }
    }
    return false;
}

/**
 * Checks if URL exists - limit 5-10 seconds
 *
 * @param $url
 * @return bool
 */
function skc_tpl_url_exists($url) {
    ini_set("default_socket_timeout","05");
    set_time_limit(5);
    $f=fopen($url,"r");
    $r=fread($f,1000);
    fclose($f);
    if(strlen($r)>1) {
        return true;
    }
    else {
        return false;
    }
}

//global $EVENT_HANDLER;
//
//$EVENT_HANDLER->register_hook('TPL_TOC_RENDER', 'BEFORE', $this, 'handleToc');
//
//function handleToc(&$event, $param) {
//
//    var_dump($event);
//
//}


