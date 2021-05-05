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

    function assigntable($searchFor, $link, $query){
        //$query = "SELECT agent_name, agent_number FROM agents";
        $results = mysqli_query($link,$query);
        $table = '<input class="write" type="text" id="myInput" onkeyup="searchEmployeesName()" placeholder="Search for names...">&nbsp&nbsp&nbsp&nbsp<input class="write" type="text" id="myIDInput" onkeyup="searchEmployeesID()" placeholder="Search for ID..."><br>'.
                "<table id='myTable'><tr><th onclick='sortTable(0)'>Employee name:</th><th onclick='sortTable(1)'>Employee ID:</th></tr>";
        while($row = mysqli_fetch_array($results, MYSQLI_BOTH)) {
            $table = $table."<tr><td>".$row["agent_name"]."</td><td>".$row["agent_number"]."</td></tr>";
        }
        $tableAndDelete = $table."</table><p></p><form action='employees.php' method='POST'><label for='delete'>Delete employee:</label>&nbsp&nbsp&nbsp&nbsp<select class='write' name = 'delete'>";
        //$query = "SELECT agent_name FROM agents ORDER BY agent_name ASC";
        $results = mysqli_query($link,$query);
        while($row = mysqli_fetch_array($results, MYSQLI_BOTH)) {
            $tableAndDelete = $tableAndDelete."<option value='".$row['agent_name']."'>".$row['agent_name']."</option>";
        }
        $tableAndDelete = $tableAndDelete."</select>&nbsp&nbsp&nbsp&nbsp<input class='but' type='submit' id='delete' value='Delete' onclick='deleteAlert()'></form>";
    

        $this ->assignedValues[strtoupper($searchFor)] = $table;
        $this ->assignedValues[strtoupper($searchFor)] = $tableAndDelete;
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