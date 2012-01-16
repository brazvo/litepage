<?php 
/* 
 * Created on: 3/19/2010 
 *  
 * page.inc
 *
 */

// echo get_magic_quotes_gpc();exit(); //check if needed addslashes()
?>

<h1 class="title"><?php eval($pageTitle) ?></h1>

<div class="content">
<?=Application::getErrors()?>
<?=Application::getMessages()?>
<?php eval($pageContent) ?>
</div>