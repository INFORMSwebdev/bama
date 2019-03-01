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
        $brand_bar = '';
        if ($this->params['brand_bar'] && !$this->params['admin']) $brand_bar = <<<EOT
		<div class="brand-bar">
			<ul>
				<li><a href="http://www.informs.org/">INFORMS.org</a></li>
				<li><a href="https://www.certifiedanalytics.org/">Certified Analytics Professional</a></li>
				<li><a href="http://pubsonline.informs.org/">PubsOnline</a></li>
				<li><a href="http://careercenter.informs.org/">Career Center</a></li>
				<li><a href="http://meetings2.informs.org/wordpress/phoenix2018/">2018 Annual Conference</a></li>
			</ul>
		</div>
EOT;
        $title_bar = '';
        if ($this->params['show_title_bar'] && !$this->params['admin']) $title_bar = <<<EOT
		<div class="siteName"><a href="{$this->params['site_url']}">{$this->params['site_title']}</a></div>
EOT;
        //this section is to display certain things in the User menu, depending on whether a user is an INFORMS admin, program admin, or anon user

        $admin_login_link = (isset($_COOKIE['aes_admin'])) ? $admin_login_link = '<a class="nav-item nav-link" href="/users/admin_login.php">Admin Log In</a>' : '';
        //display the appropriate menu item as active based on admin, editor, or anon
        if (isset($_SESSION['admin']) && $_SESSION['admin'] == true) {
            //navbar for admins (INFORMS Admins)
            # ToDo: add in more links to the nav bar
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
					<a class="nav-item nav-link" href="/index.php">Program Dashboard</a>
					<div class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Admin</a>
						<div class="dropdown-menu">
						    <a class="nav-item nav-link" href="/admin/users.php">Users</a>
							<a class="nav-item nav-link" href="/admin/invite.php">Invite User</a>
							<a class="nav-item nav-link" href="/admin/pendingUsers.php">Pending Users</a>
							<a class="nav-item nav-link" href="/admin/pendingUpdates.php">Pending Updates</a>
						</div>
					</div>
				</div>
			</div>
			<div class="navbar-nav">
				<a class="nav-item btn btn-sm btn-outline-danger" href="/users/logout.php" role="button">Log out</a>
			</div>
		</nav>
EOT;
        } else if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true) {
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
					<div class="nav-item dropdown">
					    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Institutions</a>
					    <div class="dropdown-menu">
					        <a class="nav-item nav-link" href="/institutions/add.php">Add</a>
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
					<div class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Users</a>
						<div class="dropdown-menu">
							<a class="nav-item nav-link" href="/users/profile.php">My Profile</a>
							$admin_login_link
						</div>
					</div>
				</div>
			</div>
			<div class="navbar-nav">
				<a class="nav-item btn btn-sm btn-outline-danger" href="/users/logout.php" role="button">Log out</a>
			</div>
		</nav>
EOT;
        }
        else {
            //anon users navbar
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
        }

        $utility_menu = '';
        if ($this->params['custom_utility_menu']) {
            $utility_menu = $this->params['custom_utility_menu'];
        }

        $year = date('Y', time());

        $addthis = '';
        if (!$this->params['admin']) $addthis = '<script type="text/javascript" src="https://s7.addthis.com/js/300/addthis_widget.js#pubid=ra-52370e143f8732e1" async="async"></script>';

        $header = '';
        $footer = '';
        if ($this->params['admin']) {
            $header = <<<EOT
		<div class="module admin-header">
			INFORMS ADMIN <div class="siteName"><a href="{$this->params['site_url']}">{$this->params['site_title']}</a></div>
		</div><!-- header -->
EOT;
            $footer = <<<EOT
		<!-- page_footer.tpl -->
		<div class="site-footer">
		<div class="container-fluid">
			<div class="row">
			    <p class="lead">Report any problems with this site to <a href="mailto:webdev@mail.informs.org">webdev@mail.informs.org</a></p>
			</div>
		</div>
		</div>
EOT;
        } else {
            $header = <<<EOT
		<div class="module header">
		<div class="utilitymenu">
		
		</div>
		<div class="container-fluid">
			<div class="row">
			<div class="logo">
				<a href="https://www.informs.org">
				<img src="https://www.informs.org/design/user_inform_v2/images/logo.png" alt="INFORMS&reg; Online - Institute for Operations Research and the Management Sciences" border="0" width="299" height="76">
				</a>
			</div><!-- .logo -->
			<div class="breadcrumbs"></div>
			<div class="moved-breadcrumbs"></div>
			</div>
		</div>
		</div><!-- header -->
EOT;
            $footer = <<<EOT
<!-- page_footer.tpl -->
<div class="site-footer">
  <div class="container-fluid">
    <div class="row">
      <div class="footer-branding">
        <div class="logo">
          <a href="/">
            <img src="https://www.informs.org/design/user_inform_v2/images/logo.png" alt="INFORMS&reg; Online - Institute for Operations Research and the Management Sciences"
             border="0" width="299" height="76">
          </a>
        </div><!-- .logo -->
        <p class="site-name"><strong>The Institute for Operations Research and the Management Sciences</strong></p>
        <address>5521 Research Park Drive, Suite 200<br>Catonsville, MD 21228 USA</address>
        <p><b>phone 1</b> <a class="tel" href="tel:4437573500">443-757-3500</a></p>
        <p><b>phone 2</b> <a class="tel" href="tel:8004463676">800-4INFORMS (800-446-3676)</a></p>
        <p><b>fax</b> 443-757-3515</p>
        <p><b>email</b> <a href="mailto:informs@informs.org">informs@informs.org</a></p>
      </div><!-- .footer-branding -->
      <div class="footer-menu">
        <div class="get-updates">
          <h3>Get the Latest Updates</h3>
          <div class="quick-subscribe">
            <form name="newsletter_signup_form" id="mc-embedded-subscribe-form" 
		action="//informs.us12.list-manage.com/subscribe/post?u=4492fb58343bb51422b7df9d1&amp;id=ce54ae418d" 
		method="post" class="newsletter_signup_form validate" target="_blank" novalidate>
		<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="Email Address" required>
		<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
		<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_4492fb58343bb51422b7df9d1_7aea1af2e4" tabindex="-1" value=""></div>
		<input type="submit" value="Sign Up" name="subscribe" id="mc-embedded-subscribe" class="submit">
	    </form>
          </div><!-- .quick-subscribe -->
        </div><!-- .get-updates -->
        <nav>
          <ul class="footer-menu__list">
            <li class="footer-menu__item nav-discover-informs">
              <a href="https://www.informs.org/Discover" class="footer-menu__link">
                <span class="link-label">Discover INFORMS</span>
              </a>
            </li>
            <li class="footer-menu__item nav-explore-or-analytics">
              <a href="https://www.informs.org/Explore" class="footer-menu__link">
                <span class="link-label">Explore OR &amp; Analytics</span>
              </a>
            </li>
            <li class="footer-menu__item nav-get-involved">
              <a href="https://www.informs.org/Get-Involved" class="footer-menu__link">
                <span class="link-label">Get Involved</span>
              </a>
            </li>
            <li class="footer-menu__item nav-impact">
              <a href="https://www.informs.org/Impact" class="footer-menu__link">
                <span class="link-label">Impact</span>
              </a>
            </li>
            <li class="footer-menu__item nav-join-us">
              <a href="https://online.informs.org/informsssa/informsmempubssarenew.choose?p_cust_id=1234" class="footer-menu__link">
                <span class="link-label">Join Us</span>
              </a>
            </li>
          </ul>
          <ul class="footer-menu__list">
            <li class="footer-menu__item nav-recognizing-excellence">
                		<a href="https://www.informs.org/Recognizing-Excellence" class="footer-menu__link">
	<span class="link-label">
Recognizing Excellence</span>
				</a>            </li>
                    <li class="footer-menu__item nav-professional-development">
                		<a href="https://www.informs.org/Professional-Development" class="footer-menu__link">
	<span class="link-label">
Professional Development</span>
				</a>            </li>
                    <li class="footer-menu__item nav-resource-center">
                		<a href="https://www.informs.org/Resource-Center" class="footer-menu__link">
	<span class="link-label">
Resource Center</span>
				</a>            </li>
                    <li class="footer-menu__item nav-meetings-conferences">
                		<a href="https://www.informs.org/Meetings-Conferences" class="footer-menu__link">
	<span class="link-label">
Meetings &amp; Conferences</span>
				</a>            </li>
                    <li class="footer-menu__item nav-publications-multimedia">
                		<a href="https://www.informs.org/Publications" class="footer-menu__link">
	<span class="link-label">
Publications</span>
				</a>            </li>
                    <li class="footer-menu__item nav-about-informs">
                		<a href="https://www.informs.org/About-INFORMS" class="footer-menu__link">
	<span class="link-label">
About INFORMS</span>
				</a>            </li>
                    <li class="footer-menu__item nav-communities">
                		<a href="https://www.informs.org/Communities" class="footer-menu__link">
	<span class="link-label">
Communities</span>
				</a>            </li>
            </ul>
    <ul class="footer-menu__list">
                    <li class="footer-menu__item nav-pubsonline">
                					<a href="http://pubsonline.informs.org" class="footer-menu__link">
	<span class="link-label">
PubsOnLine</span>
				</a>            </li>
                    <li class="footer-menu__item nav-various-meeting">
                					<a href="http://meetings2.informs.org/wordpress/phoenix2018/" class="footer-menu__link">
	<span class="link-label">
2018 Annual Conference</span>
				</a>            </li>
                    <li class="footer-menu__item nav-certified-analytics-professional">
                					<a href="http://certifiedanalytics.org/about.php" class="footer-menu__link">
	<span class="link-label">
Certified Analytics Professional</span>
				</a>            </li>
                    <li class="footer-menu__item nav-career-center">
                					<a href="http://careercenter.informs.org/" class="footer-menu__link">
	<span class="link-label">
Career Center</span>
				</a>            </li>
                    <li class="footer-menu__item nav-informs-connect">
                					<a href="http://connect.informs.org" class="footer-menu__link">
	<span class="link-label">
INFORMS Connect</span>
				</a>            </li>
            </ul>
</nav>            </div>
        </div>
        <div class="row">
            <div class="footer-info">
              <span class="copyright">Copyright 2017 INFORMS. All Rights Reserved</span>
              <span class="utility-nav">
                <nav>
                  <ul class="footer-utility__list">
                    <li class="footer-utility__item  nav-terms-of-use">
        	      <a href="https://www.informs.org/About-INFORMS/Terms-and-Conditions" class="footer-utility__link">
                        <span class="link-label">Terms of Use</span>
                      </a>
                    </li>
                    | 
                    <li class="footer-utility__item  nav-terms-of-use">
                      <a href="https://www.informs.org/About-INFORMS/Privacy-Policy" class="footer-utility__link">
                        <span class="link-label">Privacy</span>
                      </a>
                    </li>
                    | 
                    <li class="footer-utility__item  nav-contact-informs">
                      <a href="https://www.informs.org/About-INFORMS/Contact-Us" class="footer-utility__link">
                        <span class="link-label">Contact INFORMS</span>
                      </a>
                    </li>
                    | 
                    <li class="footer-utility__item  nav-sitemap">
                      <a href="https://www.informs.org/About-INFORMS/Sitemap" class="footer-utility__link">
                        <span class="link-label">Sitemap</span>
                      </a>
                    </li>
                  </ul>
                </nav>
              </span>
            </div>
            <div class="footer-social">
                <nav>
    <dl class="footer-social__deflist">
        <dt class="footer-social__item">Follow INFORMS on:</dt>
                    <li class="footer-social__item nav-twitter">
                					<a href="https://twitter.com/INFORMS" class="icon-twitter">
	<span class="link-label">
Twitter</span>
				</a>            </li>
                    <li class="footer-social__item nav-facebook">
                					<a href="https://www.facebook.com/INFORMSpage/" class="icon-facebook">
	<span class="link-label">
Facebook</span>
				</a>            </li>
                    <li class="footer-social__item nav-linkedin">
                					<a href="https://www.linkedin.com/company/informs_2" class="icon-linkedin">
	<span class="link-label">
LinkedIn</span>
				</a>            </li>
            </dl>
</nav>            </div>
        </div>
    </div>
</div>

      <!-- #page_footer.tpl -->
EOT;
        }

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
    <link rel="image_src" href="https://www.informs.org/design/user_inform/images/informsbutton.jpg" />
    <!--<link rel="stylesheet" type="text/css" href="https://www.informs.org/design/user_inform_v2/stylesheets/core.css" />
    <link rel="stylesheet" type="text/css" href="https://www.informs.org/design/standard/stylesheets/debug.css" />
    <link rel="stylesheet" type="text/css" href="https://www.informs.org/extension/ezflow/design/ezflow/stylesheets/pagelayout.css" />
    <link rel="stylesheet" type="text/css" href="https://www.informs.org/design/user_inform/stylesheets/content.css" />
    <link rel="stylesheet" type="text/css" href="https://www.informs.org/extension/eztags/design/standard/stylesheets/jqmodal.css" />
    <link rel="stylesheet" type="text/css" href="https://www.informs.org/extension/eztags/design/standard/stylesheets/tagssuggest.css" />
    <link rel="stylesheet" type="text/css" href="https://www.informs.org/extension/eztags/design/standard/stylesheets/contentstructure-tree.css" />
    <link rel="stylesheet" type="text/css" href="https://www.informs.org/extension/eztags/design/standard/stylesheets/jstree/eztags/style.css" />-->
    <!--<link rel="stylesheet" type="text/css" href="https://www.informs.org/extension/ezfind/design/standard/stylesheets/ezfind.css" />
    <link rel="stylesheet" type="text/css" href="https://www.informs.org/extension/ezfind/design/ezflow/stylesheets/ezajax_autocomplete.css" />-->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

    <!--[if lt IE 8]>
    <style>
    /* Terminate floating elements flow in IE < 8 */
    .float-break
    {
      height: 1%;
    }
    </style>
    <![endif]-->

    <style type="text/css">
      //@import url("https://common.informs.org/css/styles.min.css");
      //@import url("https://www.informs.org/extension/ezsurvey/design/standard/stylesheets/survey.css");
      //@import url("https://www.informs.org/design/user_inform_v2/stylesheets/handheld.css") handheld;
      //@import url("https://common.informs.org/css/wrapperBama.css");
    </style>
    
    <!--[if lt IE 7]>
    <style>
      @import url("https://www.informs.org/design/user_inform_v2/stylesheets/ie.css");
    </style>
    <![endif]-->
    
    <!--[if lt IE 9]>
    <style type="text/css">
      @import url("https://www.informs.org/design/user_inform_v2/stylesheets/ie_lt_9.css");
    </style>
    <![endif]-->
    
    {$this->cssHTML()}
    {$this->iecssHTML()}
        <script type="text/javascript" src="https://www.informs.org/extension/ezjscore/design/standard/lib/yui/3.15.0/build/yui/yui-min.js" charset="utf-8"></script>
    <script type="text/javascript">
    var YUI3_config = {"base":"https:\/\/www.informs.org\/extension\/ezjscore\/design\/standard\/lib\/yui\/3.15.0\/build\/","combine":false,"modules":{}};
    </script>
    <script type="text/javascript" src="https://www.informs.org/extension/ezjscore/design/standard/javascript/jquery-1.10.2.min.js" charset="utf-8"></script>
    <script type="text/javascript">
    
    (function($) {
        var _rootUrl = '/', _serverUrl = _rootUrl + 'ezjscore/', _seperator = '@SEPERATOR$',
            _prefUrl = _rootUrl + 'user/preferences';
    
        // FIX: Ajax is broken on IE8 / IE7 on jQuery 1.4.x as it's trying to use the broken window.XMLHttpRequest object
        if ( window.XMLHttpRequest && window.ActiveXObject )
            $.ajaxSettings.xhr = function() { try { return new window.ActiveXObject('Microsoft.XMLHTTP'); } catch(e) {} };
    
        // (static) jQuery.ez() uses jQuery.post() (Or jQuery.get() if post paramer is false)
        //
        // @param string callArgs
        // @param object|array|string|false post Optional post values, uses get request if false or undefined
        // @param function Optional callBack
        function _ez( callArgs, post, callBack )
        {
            callArgs = callArgs.join !== undefined ? callArgs.join( _seperator ) : callArgs;
            var url = _serverUrl + 'call/';
            if ( post )
            {
                var _token = '', _tokenNode = document.getElementById('ezxform_token_js');
                if ( _tokenNode ) _token = _tokenNode.getAttribute('title');
                if ( post.join !== undefined )// support serializeArray() format
                {
                    post.push( { 'name': 'ezjscServer_function_arguments', 'value': callArgs } );
                    post.push( { 'name': 'ezxform_token', 'value': _token } );
                }
                else if ( typeof(post) === 'string' )// string
                {
                    post += ( post ? '&' : '' ) + 'ezjscServer_function_arguments=' + callArgs + '&ezxform_token=' + _token;
                }
                else // object
                {
                    post['ezjscServer_function_arguments'] = callArgs;
                    post['ezxform_token'] = _token;
                }
                return $.post( url, post, callBack, 'json' );
            }
            return $.get( url + encodeURIComponent( callArgs ), {}, callBack, 'json' );
        };
        _ez.url = _serverUrl;
        _ez.root_url = _rootUrl;
        _ez.seperator = _seperator;
        $.ez = _ez;
    
        $.ez.setPreference = function( name, value )
        {
            var param = {'Function': 'set_and_exit', 'Key': name, 'Value': value};
                _tokenNode = document.getElementById( 'ezxform_token_js' );
            if ( _tokenNode )
                param.ezxform_token = _tokenNode.getAttribute( 'title' );
    
            return $.post( _prefUrl, param );
        };
    
        // Method version, for loading response into elements
        // NB: Does not use json (not possible with .load), so ezjscore/call will return string
        function _ezLoad( callArgs, post, selector, callBack )
        {
            callArgs = callArgs.join !== undefined ? callArgs.join( _seperator ) : callArgs;
            var url = _serverUrl + 'call/';
            if ( post )
            {
                post['ezjscServer_function_arguments'] = callArgs;
                post['ezxform_token'] = jQuery('#ezxform_token_js').attr('title');
            }
            else
                url += encodeURIComponent( callArgs );
    
            return this.load( url + ( selector ? ' ' + selector : '' ), post, callBack );
        };
        $.fn.ez = _ezLoad;
    })(jQuery);
            
    </script>
    <!--<script type="text/javascript" src="https://www.informs.org/design/user_inform_v2/javascript/libs/modernizr.custom.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/design/user_inform_v2/javascript/libs/jquery-migrate-1.2.1.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/design/user_inform_v2/javascript/libs/jquery.waypoints.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/design/user_inform_v2/javascript/libs/inview.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/design/user_inform_v2/javascript/libs/masonry.pkgd.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/design/user_inform_v2/javascript/libs/parallax.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/design/user_inform_v2/javascript/libs/slick.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/design/user_inform_v2/javascript/scripts.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/extension/ezjscore/design/standard/javascript/jquery-ui-1.10.3.custom.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/extension/eztags/design/standard/javascript/jqmodal.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/extension/eztags/design/standard/javascript/jstree.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/extension/eztags/design/standard/javascript/jquery.eztags.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/extension/eztags/design/standard/javascript/jquery.eztags.select.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/extension/eztags/design/standard/javascript/jquery.eztags.tree.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/extension/eztags/design/standard/javascript/tagsstructuremenu.js" charset="utf-8"></script>

    <script type="text/javascript" src="https://www.informs.org/extension/ezdemo/design/ezdemo/javascript/init_ua.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/extension/ezdemo/design/ezdemo/javascript/handle_transition.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/extension/ezdemo/design/ezdemo/javascript/toggle_class.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/extension/ezdemo/design/ezdemo/javascript/ezflyout.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/extension/ezdemo/design/ezdemo/javascript/ezsimplegallery.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/extension/ezdemo/design/ezdemo/javascript/ezgallerynavigator.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.informs.org/extension/ezdemo/design/ezdemo/javascript/ezgallery.js" charset="utf-8"></script>-->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    
    <script type="text/javascript">
    $(document).ready( function() {
      var blacklist = [ '/user/login', '/Announcements/Member-Log-Out', '/sso_login.php' ];
      if (jQuery.inArray( location.pathname, blacklist ) < 0) document.cookie = 'remember_page=' + escape(location.pathname) + ';domain=.informs.org;path=/';
    });
    </script>

    <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-3721440-1', 'auto');
    ga('require', 'displayfeatures');
    ga('send', 'pageview');
    </script>
    
    {$this->jsHTML()}
    
  </head>
  
  <!--[if lt IE 7 ]><body class="ie6"><![endif]-->
  <!--[if IE 7 ]>   <body class="ie7"><![endif]-->
  <!--[if IE 8 ]>   <body class="ie8"><![endif]-->
  <!--[if (gt IE 8)|!(IE)]><!--><body><!--<![endif]-->

    $navbar                                            
    <!--<div class="wrapper node_103 full-frontpage">-->
    <div class="container-fluid">
      $brand_bar
      <!--<div class="skip"><a href="#content">skip to content</a></div>-->

      

      <!--<main class="contentwrap">-->
      <div class="container-fluid">
      <div class="row">
      <div class="col-md-12">
        <div class="content">
          $title_bar
          <div class="container-fluid">
            {$this->params['content']}
          </div>
        </div><!-- content -->
        </div>
        </div>
        </div><!-- content container -->
      <!--</main>-->

      $footer
    
    </div><!-- wrapper -->

    <div style="clear:both"></div>
    $addthis
    

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
                $html .= '</script>';
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