<?php
/**
 * First theme
 *
 * @file
 * @author Towny Pooky
 * @license MIT
 * @package synary
 * @since 201502101615
 */

/** @var HtmlHelper $Html */
$Html = $this->Html;
/** @var SectionHelper $Section */
$Section = $this->Section;


$Section->siteTitle = __('Chalog');
$Section->rootPath = '/frontpage';

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <?php $Section->pageTitle(); ?>
    <?php echo $Html->meta('icon'); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <script src="https://www.google.com/jsapi"></script>
    <?php
    /**
     * Load these css files before others
     */
    echo $Html->css(array(
        'reset',
        'bootstrap.min',
        'bootstrap-theme.min',
        'bootstrap-responsive.min',
        'common'));
    ?>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <!--
    <link rel="shortcut icon" href="/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="/ico/apple-touch-icon-57-precomposed.png">
    -->
    <?php
    echo $this->fetch('meta');
    echo $this->fetch('css');

    /**
     * Please modefy file names as your installation
     */
    echo $Html->script(array(
        'jquery-2.1.3.min',
        'jquery-custom',
        'network-min.js'
    ));
    ?>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css" />
</head>

<body>


<header id="site-header">
    <div class="content" style="float:left;width:350px;margin:0;padding:0;">
        <?php $Section->title(); ?>
        <?php $Section->subtitle(); ?>
    </div>
    <menu class="content" style="float:left;width:250px;padding:0;">
        <li><?php echo $Html->link(
                __d('chips', 'Create a node'),
                '/create'); ?></li>
    </menu>
    <form id="quick-search" class="form-inline" style="float:right;width: 300px;">
        <input type="text" class="form-control" name="q" value="" placeholder="<?php echo __d('basic', 'Search'); ?>"/>
        <button class="btn btn-default"><?php echo __d('basic', 'Search'); ?></button>
    </form>
    <?php echo $Html->link(__d('basic', 'Login'), '/users/login'); ?>
    <div style="clear:both"></div>
</header>

<section id="site-main">
    <section class="content">
        <?php echo $this->fetch('content'); ?>
    </section>
</section>

<menu id="footer">
    <li><?php echo __d('basic', 'Help'); ?></li>
    <li><?php echo __d('basic', 'Terms'); ?></li>
    <li><?php echo __d('basic', 'Legal note'); ?></li>
</menu>

</body>
</html>

