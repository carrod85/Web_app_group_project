<?php
 class Template {
     public $tpl;
     public $assignedValues = array();
     
     function __construct($filename = "") {
         if (!empty($filename)) {
             if (file_exists($filename)) {
                 $this ->tpl = file_get_contents($filename);
                } else {
                    exit ("ERROR: template file not found!");
                }
        }
    }

    function assign($searchFor , $replaceWith) {
        if (!empty($searchFor)) {
            $this ->assignedValues[strtoupper($searchFor)] = $replaceWith;
        }
    }

    function render() {
        if (count($this ->assignedValues) > 0) {
            foreach ($this ->assignedValues as $key => $value){
                $this ->tpl = preg_replace ("/\{" . $key . "\}/", $value , $this ->tpl);
            }
        }
        $placeholders = ["{{TITLE}}", "{{ADMIN}}", "{{USER}}", "{{TITLE1}}", "{{SUBTITLE1}}", "{{ERROR1}}", "{{CONTENT}}", "{{FORM}}", "{{TABLE}}", "{{TITLE2}}", "{{SUBTITLE2}}", "{{ERROR2}}", "{{SIDECONTENT1}}", "{{TITLE3}}", "{{SUBTITLE3}}", "{{ERROR3}}", "{{SIDECONTENT2}}"];
        $tallinnTime =date('jS').' '.'of'.' '.date('F').' '.date('Y').' '.'|'.' '.date('h:i').' '.date('A').' '.'|'.' '.'TALLINN';
        $this ->tpl = preg_replace ("{{TIME}}", $tallinnTime, $this ->tpl);
        $this ->tpl = preg_replace ($placeholders, false , $this ->tpl);
        return $this ->tpl;
    }
 }

 ?>