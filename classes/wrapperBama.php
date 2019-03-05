<?php

/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/19/2019
 * Time: 10:47 AM
 */
class wrapperBama
{

    public function __construct($params = array())
    {
        $ini = parse_ini_file("/common/settings/common.ini", TRUE);
        $this->params = array(
            'admin' => FALSE,
            'brand_bar' => TRUE,
            'site_title' => 'INFORMS Online',
            'site_url' => '/',
            'page_title' => '',
            'show_title_bar' => TRUE,
            'custom_utility_menu' => '',
            'thin' => FALSE,
            'nav_items' => array(),
            'meta' => array(),
            'css' => array(),
            'iecss' => array(),
            'js' => array(),
            'file' => '',
            'content' => '',
            'active_menu_item' => '',
            'web_path' => $ini['analytics_education_settings']['web_root'],
            'root_path' => $ini['analytics_education_settings']['root_dir'],
            'html_path' => $ini['analytics_education_settings']['root_dir'] . $ini['analytics_education_settings']['html_dir'],
            'users_path' => $ini['analytics_education_settings']['root_dir'] . $ini['analytics_education_settings']['html_dir'] . $ini['analytics_education_settings']['user_dir'],
            'scripts_path' => $ini['analytics_education_settings']['root_dir'] . $ini['analytics_education_settings']['html_dir'] . $ini['analytics_education_settings']['scripts_dir'],
            'images_path' => $ini['analytics_education_settings']['html_dir'] . $ini['analytics_education_settings']['images_dir'],
            'settings_path' => $ini['analytics_education_settings']['root_dir'] . $ini['analytics_education_settings']['settings_dir'],
            'log_path' => $ini['analytics_education_settings']['root_dir'] . $ini['analytics_education_settings']['log_dir']
        );
        foreach ($params as $param => $value) $this->params[$param] = $value;
    }

    public function addNavItem($label, $url)
    {
        $this->params['nav_items'][] = "<a href=\"$url\">$label</a>";
    }

    public function html($asString = FALSE)
    {
        //this section is to display certain things in the User menu, depending on whether a user is an INFORMS admin, program admin, or anon user
        $admin_login_link = (isset($_COOKIE['aes_admin'])) ? $admin_login_link = '<a class="nav-item nav-link" href="/users/admin_login.php">Admin Log In</a>' : '';
        //this will also be used for the Users nav tab, but I am not changing the name of the variable
        $admin_navtab = '';

        //set up the default (anon users) navbar
        $navbar = <<<EOT
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="https://www.informs.org" target="_blank">
        <img src="https://common.informs.org/images/informs_125x30.jpg" height="30" alt="INFORMS logo" />
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon" />
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <div class="navbar-nav">
            <a class="nav-item nav-link" href="/index.php">Home</a>
            <a class="nav-item nav-link" href="/users/register.php">Become an Institution Administrator</a>
            <a class="nav-item nav-link" href="/users/login.php">Log In</a>
            $admin_login_link
        </div>
    </div>
</nav>
EOT;

        //display the appropriate menu item as active based on admin, editor, or anon
        if (isset($_SESSION['admin']) && $_SESSION['admin'] == true) {
            $admin_navtab = <<<EOT
<div class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Admin</a>
    <div class="dropdown-menu">
        <a class="nav-item nav-link" href="/admin/users.php">Users</a>
        <a class="nav-item nav-link" href="/admin/invite.php">Invite User</a>
        <a class="nav-item nav-link" href="/admin/pendingUsers.php">Pending Users</a>
        <a class="nav-item nav-link" href="/admin/pendingUpdates.php">Pending Updates</a>
        <a class="nav-item nav-link" href="/admin/addInstitution.php">Add Institution</a>
    </div>
</div>
EOT;
        }
        else if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true) {
            $admin_navtab = <<<EOT
<div class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Users</a>
    <div class="dropdown-menu">
        <a class="nav-item nav-link" href="/users/profile.php">My Profile</a>
        <a class="nav-item nav-link" href="/users/resetPassword.php">Reset Password</a>
        $admin_login_link
    </div>
</div>
EOT;
        }

        //check if we should display the logged in (either admin or editor) or anon navbar
        if(isset($_SESSION['admin']) || isset($_SESSION['loggedIn'])) {
            $navbar = <<<EOT
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="https://www.informs.org" target="_blank">
        <img src="https://common.informs.org/images/informs_125x30.jpg" height="30" alt="INFORMS logo" />
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <div class="navbar-nav">
            <a class="nav-item nav-link" href="/index.php">Home</a>
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Institutions</a>
                <div class="dropdown-menu">
                    <a class="nav-item nav-link" href="/institutions/display.php">Display</a>
                    <a class="nav-item nav-link" href="/institutions/edit.php">Edit</a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Programs</a>
                <div class="dropdown-menu">
                    <a class="nav-item nav-link" href="/programs/add.php">Add</a>
                    <a class="nav-item nav-link" href="/programs/display.php">Display</a>
                    <a class="nav-item nav-link" href="/programs/edit.php">Edit</a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Instructors</a>
                <div class="dropdown-menu">
                    <a class="nav-item nav-link" href="/instructors/add.php">Add</a>
                    <a class="nav-item nav-link" href="/instructors/display.php">Display</a>
                    <a class="nav-item nav-link" href="/instructors/edit.php">Edit</a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Courses</a>
                <div class="dropdown-menu">
                    <a class="nav-item nav-link" href="/courses/add.php">Add</a>
                    <a class="nav-item nav-link" href="/courses/display.php">Display</a>
                    <a class="nav-item nav-link" href="/courses/edit.php">Edit</a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Textbooks</a>
                <div class="dropdown-menu">
                    <a class="nav-item nav-link" href="/textbooks/add.php">Add</a>
                    <a class="nav-item nav-link" href="/textbooks/display.php">Display</a>
                    <a class="nav-item nav-link" href="/textbooks/edit.php">Edit</a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Software</a>
                <div class="dropdown-menu">
                    <a class="nav-item nav-link" href="/software/add.php">Add</a>
                    <a class="nav-item nav-link" href="/software/display.php">Display</a>
                    <a class="nav-item nav-link" href="/software/edit.php">Edit</a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Case Studies</a>
                <div class="dropdown-menu">
                    <a class="nav-item nav-link" href="/cases/add.php">Add</a>
                    <a class="nav-item nav-link" href="/cases/display.php">Display</a>
                    <a class="nav-item nav-link" href="/cases/edit.php">Edit</a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Datasets</a>
                <div class="dropdown-menu">
                    <a class="nav-item nav-link" href="/datasets/add.php">Add</a>
                    <a class="nav-item nav-link" href="/datasets/display.php">Display</a>
                    <a class="nav-item nav-link" href="/datasets/edit.php">Edit</a>
                </div>
            </div>
            {$admin_navtab}
        </div>
    </div>
    <div class="navbar-nav">
        <a class="nav-item btn btn-sm btn-outline-danger" href="/users/logout.php" role="button">Log out</a>
    </div>
</nav>
EOT;
        }

        //set up the footer to display
        $footer = <<<EOT
		<!-- page_footer.tpl -->
		<div class="container-fluid">
			<div class="row">
			    <p class="lead">Report any problems with this site to <a href="mailto:webdev@mail.informs.org">webdev@mail.informs.org</a>.</p>
			</div>
		</div>
EOT;

        $html = <<<EOT
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#" xml:lang="en-US" lang="en-US">
    <head>
        <meta property="og:site_name" content="INFORMS" />
        <meta property="og:title" content="INFORMS" />
        <meta property="og:url" content="http://www.informs.org/" />
        <meta property="og:type" content="website" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="MobileOptimized" content="width" />
        <meta name="HandheldFriendly" content="true" />
        <meta name="Content-Type" content="text/html; charset=utf-8" />
        <meta name="Content-language" content="en-US" />
        <meta name="author" content="INFORMS" />
        <meta name="copyright" content="INFORMS 2014" />
        <meta name="description" content="The Institute for Operations Research and the Management Sciences" />
        <meta name="keywords" content="INFORMS, analytics, operations research, management science, modeling, decision analysis, simulation, marketing science" />
        <meta name="MSSmartTagsPreventParsing" content="TRUE" />
        <meta name="generator" content="eZ Publish" />
        <meta name="google-site-verification" content="dsannIGAUcyndCWD34xnmzdnPYCp8mwMi4i6Tn7jW1w" />
    
        {$this->metaHTML()}
    
        <title>{$this->params['page_title']}</title>
    
        <link rel="icon" type="image/png" href="https://common.informs.org/images/favicon-16x16.png" />
	    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
        
        {$this->cssHTML()}
        {$this->iecssHTML()}
            
	    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
	    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
	    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
        
        {$this->jsHTML()}
  
    </head>
    <body>
        $navbar
        <div class="container-fluid">
            <div class="flex-column">
                {$this->params['content']}
            </div>
            $footer
        </div>
    </body>
</html>
EOT;
        if ($asString) {
            return $html;
        } else {
            echo $html;
            return TRUE;
        }
    }

    public function cssHTML()
    {
        $html = '<style type="text/css">' . PHP_EOL;
        foreach ($this->params['css'] as $css) {
            if (isset($css['text'])) {
                $html .= $css['text'] . PHP_EOL;
            } elseif (isset($css['url'])) {
                $html .= "@import url('$css[url]');" . PHP_EOL;
            }
        }
        $html .= '</style>' . PHP_EOL;
        return $html;
    }

    public function iecssHTML()
    {
        $html = '';
        foreach ($this->params['iecss'] as $iecss_item) {

            if (isset($iecss_item['url'])) {
                $html = "@import url('{$iecss_item['url']}');";;
            }

            if (isset($css_item['text'])) {
                $html = $iecss_item['text'];
            }

            $test = 'IE';
            if (isset($iecss_item['test'])) {
                $test = $iecss_item['test'];
            }

            // need to do separate block for each css item to accommodate differing IE version rules
            $html = <<<EOT
<!--[if $test]>
<style type="text/css">
$html
</style>
<![endif]-->
EOT;
            return $html;
        }

        foreach ($this->params['iecss'] as $iecss) {
            if (isset($iecss['text'])) {
                $html .= $iecss['text'];
            } elseif (isset($iecss['url'])) {
                $html .= "@import url('$iecss[url]');";
            }
        }
        return $html;
    }

    public function jsHTML()
    {
        $html = '';
        foreach ($this->params['js'] as $js) {
            if (isset($js['text'])) {
                $html .= '<script type="text/javascript">' . PHP_EOL;
                $html .= $js['text'] . PHP_EOL;
                $html .= '</script>' . PHP_EOL;
            } elseif (isset($js['url'])) {
                $html .= '<script type="text/javascript" src="' . $js['url'] . '"></script>' . PHP_EOL;
            }
        }
        return $html;
    }

    public function metaHTML()
    {
        $html = '';
        foreach ($this->params['meta'] as $meta) {

        }
        return $html;
    }
}