<?php
    function map($link,$curLang,$linkValue) {
        $pattern = '<br />      &lt;add key="' . $curLang . $linkValue . '" value="' . $link . '" /&gt;';
        echo $pattern;
    }
    function rules($link,$curLang,$linkValue,$name4rules) {
        $pattern = '<br />    &lt;rule name="' . $name4rules . '" patternSyntax="Wildcard" stopProcessing="true"&gt;
      &lt;match url="' . $curLang . $linkValue . '" /&gt;
      &lt;action type="Redirect" url="' . $link . '" /&gt;
    &lt;/rule&gt;';
        echo $pattern;
    }
?>