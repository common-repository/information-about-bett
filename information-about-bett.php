<?php
/*
Plugin Name: Information about Bett
Plugin URI: http://wordpress.org/extend/plugins/information-about-bett/
Description: Adds a widget which displays the latest information about Bett by http://www.bett.de/
Version: 1.0
Author: Markus Maier
Author URI: http://www.bett.de/
License: GPL3
*/

function bettnews()
{
  $options = get_option("widget_bettnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Information about Bett',
      'news' => '5',
      'chars' => '30'
    );
  }

  // RSS Objekt erzeugen 
  $rss = simplexml_load_file( 
  'http://news.google.de/news?pz=1&cf=all&ned=de&hl=de&q=bett&cf=all&output=rss'); 
  ?> 
  
  <ul> 
  
  <?php 
  // maximale Anzahl an News, wobei 0 (Null) alle anzeigt
  $max_news = $options['news'];
  // maximale Länge, auf die ein Titel, falls notwendig, gekürzt wird
  $max_length = $options['chars'];
  
  // RSS Elemente durchlaufen 
  $cnt = 0;
  foreach($rss->channel->item as $i) { 
    if($max_news > 0 AND $cnt >= $max_news){
        break;
    }
    ?> 
    
    <li>
    <?php
    // Titel in Zwischenvariable speichern
    $title = $i->title;
    // Länge des Titels ermitteln
    $length = strlen($title);
    // wenn der Titel länger als die vorher definierte Maximallänge ist,
    // wird er gekürzt und mit "..." bereichert, sonst wird er normal ausgegeben
    if($length > $max_length){
      $title = substr($title, 0, $max_length)."...";
    }
    ?>
    <a href="<?=$i->link?>"><?=$title?></a> 
    </li> 
    
    <?php 
    $cnt++;
  } 
  ?> 
  
  </ul>
<?php  
}

function widget_bettnews($args)
{
  extract($args);
  
  $options = get_option("widget_bettnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Information about Bett',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  bettnews();
  echo $after_widget;
}

function bettnews_control()
{
  $options = get_option("widget_bettnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Information about Bett',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  if($_POST['bettnews-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['bettnews-WidgetTitle']);
    $options['news'] = htmlspecialchars($_POST['bettnews-NewsCount']);
    $options['chars'] = htmlspecialchars($_POST['bettnews-CharCount']);
    update_option("widget_bettnews", $options);
  }
?> 
  <p>
    <label for="bettnews-WidgetTitle">Widget Title: </label>
    <input type="text" id="bettnews-WidgetTitle" name="bettnews-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
    <label for="bettnews-NewsCount">Max. News: </label>
    <input type="text" id="bettnews-NewsCount" name="bettnews-NewsCount" value="<?php echo $options['news'];?>" />
    <br /><br />
    <label for="bettnews-CharCount">Max. Characters: </label>
    <input type="text" id="bettnews-CharCount" name="bettnews-CharCount" value="<?php echo $options['chars'];?>" />
    <br /><br />
    <input type="hidden" id="bettnews-Submit"  name="bettnews-Submit" value="1" />
  </p>
  
<?php
}

function bettnews_init()
{
  register_sidebar_widget(__('Information about Bett'), 'widget_bettnews');    
  register_widget_control('Information about Bett', 'bettnews_control', 300, 200);
}
add_action("plugins_loaded", "bettnews_init");
?>