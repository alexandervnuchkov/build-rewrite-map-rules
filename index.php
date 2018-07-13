<?php
    ini_set("auto_detect_line_endings", true);
    error_reporting(E_ALL);
    ini_set("display_errors", 0);
    include('shared/map.php');
?>
<html>
<head>
    <title>Build rewrite maps</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:900,800,700,600,500,400,300&amp;subset=latin,cyrillic-ext,cyrillic,latin-ext" rel="stylesheet" type="text/css" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
</head>
<body id="index_page">
    <h1>The &lt;rewrite&gt; section of <i>web.config</i></h1>
    <form action="" method="get">
        <div class="formDiv">
            <select name="whatToCheck">
                <option <?php if($_GET['whatToCheck'] == 'www') echo 'selected'; ?> value="www">www.avs4you.com</option>
                <option <?php if($_GET['whatToCheck'] == 'guides') echo 'selected'; ?> value="guides">www.avs4you.com/guides</option>
                <option <?php if($_GET['whatToCheck'] == 'mobile') echo 'selected'; ?> value="mobile">mobile.avs4you.com</option>
                <option <?php if($_GET['whatToCheck'] == 'onlinehelp') echo 'selected'; ?> value="onlinehelp">onlinehelp.avs4you.com</option>
                <option <?php if($_GET['whatToCheck'] == 'videoguides') echo 'selected'; ?> value="videoguides">videoguides.avs4you.com</option>
            </select>
            <select name="rewriteType">
                <option <?php if($_GET['rewriteType'] == 'map') echo 'selected'; ?> value="map">Rewrite map</option>
                <option <?php if($_GET['rewriteType'] == 'rules') echo 'selected'; ?> value="rules">Rewrite rules</option>
            </select>
            <input name="submitWhatToCheck" type="submit" value="Start!">
        </div>
    </form>
<?php
    if(isset($_GET['submitWhatToCheck'])) {
        $site = $_GET['whatToCheck'];
        $mapType = $_GET['rewriteType'];
        if($site == 'www'){
            $fileURL = 'rewrite_www.avs4you.csv';
            $websiteName = 'https://www.avs4you.com/';
        }
        else if($site == 'guides'){
            $fileURL = 'rewrite_www.avs4you.com-guides.csv';
            $websiteName = 'https://www.avs4you.com/guides/';
        }
        else if($site == 'onlinehelp'){
            $fileURL = 'rewrite_onlinehelp.avs4you.csv';
            $websiteName = 'https://onlinehelp.avs4you.com/';
        }
        else if($site == 'mobile'){
            $fileURL = 'rewrite_mobile.avs4you.csv';
            $websiteName = 'https://mobile.avs4you.com/';
        }
        else if($site == 'videoguides'){
            $fileURL = 'rewrite_videoguides.avs4you.csv';
            $websiteName = 'https://videoguides.avs4you.com/';
        }
        echo '<p>Here is the <code>&lt;rewrite&gt;</code> section of the <b>web.config</b> file for the <u>' . $websiteName . '</u> website, built using the <b>rewrite ' . $mapType . '</b> method:</p><p></p>';
        echo '<p><a onclick="selectText(\'selectable\')">Select all</a></p><p></p>';
        echo '<pre  id="selectable">';
        if($mapType == 'map'){
            $delimiter = '/';
            echo '&lt;rewrite>
  &lt;rules&gt;
  &lt;clear /&gt;
    &lt;rule name="Redirect rule for rewriteMap"&gt;
      &lt;match url=".*" /&gt;
      &lt;conditions&gt;
        &lt;add input="{rewriteMap:{REQUEST_URI}}" pattern="(.+)" /&gt;
      &lt;/conditions&gt;
      &lt;action type="Redirect" url="{C:1}" appendQueryString="false" /&gt;
    &lt;/rule&gt;
  &lt;/rules&gt;
  &lt;rewriteMaps&gt;
    &lt;rewriteMap name="rewriteMap" defaultValue=""&gt;';
            }
            else if($mapType == 'rules'){
                $delimiter = '';
                echo '&lt;rewrite>
  &lt;rules&gt;
    &lt;clear /&gt;';
        }
        $guides_folder = '';
        if($site == 'guides'){
            $guides_folder = 'guides/';
        }
        $csv = array_map('str_getcsv', file($fileURL));
        foreach($csv as $csvarray){
            $counter = count($csvarray);
            foreach($csvarray as $csvitem){
                if($csvitem == 'source'){
                    $arrayLang = $csvarray;
                    continue;
                }
            }
        }
        for( $i = 1; $i<count($csv); $i++ ){
            for( $x = 1; $x<$counter; $x++ ){                
                $link = '';
                $curLang = '';
                if (strpos($csv[$i][1], 'http') !== false && $csv[$i][$x] == '/') {
                    $link = $csv[$i][1];
                }
                else if (strpos($csv[$i][1], 'http') !== false && $csv[$i][$x] == '+') {
                    $comPos = strpos($csv[$i][1], '.com');
                    $orig_string = $csv[$i][1];
                    $insert_string = '/' . $arrayLang[$x];
                    $position = $comPos + 4;
                    $link = substr_replace($orig_string, $insert_string, $position, 0);
                }
                else if (strpos($csv[$i][1], 'http') !== false && $x == 1) {
                    $link = $csv[$i][1];
                }
                else if($csv[$i][$x] == '+'){
                    $link = $delimiter . $arrayLang[$x] . '/' . $guides_folder . $csv[$i][1];
                }
                else if($csv[$i][$x] == '/'){
                    $link = $delimiter . $guides_folder . $csv[$i][1];
                }
                else{
                    $link = $delimiter . $guides_folder . $csv[$i][1];
                }
                if($arrayLang[$x] != 'en'){
                    $curLang = $delimiter . $arrayLang[$x] . '/' . $guides_folder;
                }
                else{
                    $curLang = $delimiter . $guides_folder;
                }
                $nameReplace = str_replace('.aspx', '', $csv[$i][0]);
                $nameReplace = str_replace('/', '-', $nameReplace);
                $name4rules = $arrayLang[$x] . '-' . strtolower($nameReplace);
                $linkValue = $csv[$i][0];
                if($link != '' && $csv[$i][1] !== '-'){
                    if($mapType == 'map'){
                        map($link,$curLang,$linkValue);
                    }
                    else if($mapType = 'rules'){
                        rules($link,$curLang,$linkValue,$name4rules);
                    }
                }
                else if($csv[$i][$x] !== '-' && $csv[$i][1] == '-'){
                    if($mapType == 'map'){
                        $link = '/' . $arrayLang[$x] . '/' . $guides_folder . $csv[$i][$x];
                        map($link,$curLang,$linkValue);
                    }
                    else if($mapType = 'rules'){
                        $link = $arrayLang[$x] . '/' . $guides_folder . $csv[$i][$x];
                        rules($link,$curLang,$linkValue,$name4rules);
                    }
                }
                else if($link != '' && $csv[$i][$x] !== '-'){
                    if($mapType == 'map'){
                        map($link,$curLang,$linkValue);
                    }
                    else if($mapType = 'rules'){
                        rules($link,$curLang,$linkValue,$name4rules);
                    }
                }
            }
        }
        if($mapType == 'map'){
            echo '<br />    &lt;/rewriteMap&gt;
  &lt;/rewriteMaps&gt;
&lt;/rewrite&gt;';
        }
        else if($mapType == 'rules'){
            echo '<br />  &lt;/rules&gt;
&lt;/rewrite&gt;';
        }
        echo '</pre>';
    }
?>
    <p id="back-top" style="display: none">
        <a title="Scroll up" href="#top"></a>
    </p>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="js/arrowup.min.js"></script>
    <script type="text/javascript" src="js/selector.js"></script>
</body>
</html>