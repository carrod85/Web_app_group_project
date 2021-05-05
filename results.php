<?php
error_reporting(E_ALL);
include("lib/library.php");
include(DATABASE);
include(SESSIONS);
validate_session("user");
include(TEMP_ENG);

$link = mysqli_connect($server, $user, $password, $database);
$welcome = "";
if (!$link)
    die("Connection failed: " . $mysqli_connect_error());
else $welcome = "Welcome!";

$formresults = "<form action='' target='_self' method='POST' enctype='multipart/form-data'>
<input type='text' class='write' id='search' name='search' placeholder = 'Search by any value'>&nbsp&nbsp&nbsp&nbsp<input class='but' type='submit' value='Filter'>
</form>";

// input sanitation
function sanitizeInputVar($link, $var) {
    $var = stripslashes($var);
    $var = htmlentities($var);
    $var = strip_tags($var);
    $var = mysqli_real_escape_string($link, $var);
    $var = htmlspecialchars($var); 
    return $var;
}

// sanitation of order input
function get_ord($ord) {
    if ($ord == 'evaluator') return 'evaluator';
    else if ($ord == 'agent') return 'agent';
    else if ($ord == 'agent_number') return 'agent_number';
    else if ($ord == 'communication') return 'communication';
    else if ($ord == 'troubleshooting') return 'troubleshooting';
    else if ($ord == 'documentation') return 'documentation';
    else if ($ord == 'total') return 'total';
    else if ($ord == '`date`') return '`date`';
    else return '`time`';
}

// sanitation of direction input
function get_dir($dir) {
    return $dir == 'DESC' ? 'DESC' : 'ASC';
}

// function to build the table, two different styles for ascending and descending are allowed, only one implemented
function listCourses($link){
    $style0 = ''; $style1 = ''; $style2 = ''; $style3 = ''; $style4 = ''; $style5 = ''; $style6 = ''; $style7 = '';
    $StyleA = "style='font weight: none; text-decoration: none; font-style: italic; color: var(--font-color); background-color: var(--button-color);'";
    $StyleB = "style='font weight: none; text-decoration: none; font-style: italic; color: var(--font-color); background-color: var(--button-color);'";

    $final = "<table>
    <tr><th>evaluator</th><th>agent</th><th>agent_number</th><th>communication</th><th>troubleshooting</th><th>documentation</th><th>total</th><th>date</th></tr>  
    <tr><th>
    <a ".$StyleB." href='results.php?ord=evaluator&dir=ASC' method='GET'>Ascend</a>&nbsp;
    <a ".$StyleA." href='results.php?ord=evaluator&dir=DESC' method='GET'>Descend</a>
    </th><th>
    <a ".$StyleB." href='results.php?ord=agent&dir=ASC' method='GET'>Ascend</a>&nbsp;
    <a ".$StyleA." href='results.php?ord=agent&dir=DESC' method='GET'>Descend</a>
    </th><th>
    <a ".$StyleB." href='results.php?ord=agent_number&dir=ASC' method='GET'>Ascend</a>&nbsp;
    <a ".$StyleA." href='results.php?ord=agent_number&dir=DESC' method='GET'>Descend</a>  
    </th><th>
    <a ".$StyleB." href='results.php?ord=communication&dir=ASC' method='GET'>Ascend</a>&nbsp;
    <a ".$StyleA." href='results.php?ord=communication&dir=DESC' method='GET'>Descend</a>
    </th><th>
    <a ".$StyleB." href='results.php?ord=troubleshooting&dir=ASC' method='GET'>Ascend</a>&nbsp;
    <a ".$StyleA." href='results.php?ord=troubleshooting&dir=DESC' method='GET'>Descend</a>
    </th><th>
    <a ".$StyleB." href='results.php?ord=documentation&dir=ASC' method='GET'>Ascend</a>&nbsp;
    <a ".$StyleA." href='results.php?ord=documentation&dir=DESC' method='GET'>Descend</a>
    </th><th>
    <a ".$StyleB." href='results.php?ord=total&dir=ASC' method='GET'>Ascend</a>&nbsp;
    <a ".$StyleA." href='results.php?ord=total&dir=DESC' method='GET'>Descend</a>  
    </th><th>
    <a ".$StyleB." href='results.php?ord=`date`&dir=ASC' method='GET'>Ascend</a>&nbsp;
    <a ".$StyleA." href='results.php?ord=`date`&dir=DESC' method='GET'>Descend</a>    
    </th></tr>";
    
    if (($_GET['ord'] == 'evaluator') && ($_GET['dir'] == 'ASC')){
        $style0 = $StyleB;
    } else if (($_GET['ord'] == 'evaluator') && ($_GET['dir'] == 'DESC')){
        $style0 = $StyleA;
    } else if (($_GET['ord'] == 'agent') && ($_GET['dir'] == 'ASC')){
        $style1 = $StyleB;
    } else if (($_GET['ord'] == 'agent') && ($_GET['dir'] == 'DESC')){
        $style1 = $StyleA;
    } else if (($_GET['ord'] == 'agent_number') && ($_GET['dir'] == 'ASC')){
        $style2 = $StyleB;
    } else if (($_GET['ord'] == 'agent_number') && ($_GET['dir'] == 'DESC')){
        $style2 = $StyleA;
    } else if (($_GET['ord'] == 'communication') && ($_GET['dir'] == 'ASC')){
        $style3 = $StyleB;
    } else if (($_GET['ord'] == 'communication') && ($_GET['dir'] == 'DESC')){
        $style3 = $StyleA;
    } else if (($_GET['ord'] == 'troubleshooting') && ($_GET['dir'] == 'ASC')){
        $style4 = $StyleB;
    } else if (($_GET['ord'] == 'troubleshooting') && ($_GET['dir'] == 'DESC')){
        $style4 = $StyleA;
    } else if (($_GET['ord'] == 'documentation') && ($_GET['dir'] == 'ASC')){
        $style5 = $StyleB;
    } else if (($_GET['ord'] == 'documentation') && ($_GET['dir'] == 'DESC')){
        $style5 = $StyleA;
    } else if (($_GET['ord'] == 'total') && ($_GET['dir'] == 'ASC')){
        $style6 = $StyleB;
    } else if (($_GET['ord'] == 'total') && ($_GET['dir'] == 'DESC')){
        $style6 = $StyleA;
    } else if (($_GET['ord'] == '`date`') && ($_GET['dir'] == 'ASC')){
        $style7 = $StyleB;
    } else if (($_GET['ord'] == '`date`') && ($_GET['dir'] == 'DESC')){
        $style7 = $StyleA;
    }  
  
    $search = sprintf('%%%s%%', sanitizeInputVar($link, $_POST['search']));
    $ord =  get_ord($_GET['ord']);
    $dir = get_dir($_GET['dir']);
  
    if (!$_GET['ord'] || !$_GET['dir']) {
      $stmt = mysqli_prepare($link, "SELECT evaluator, agent, agent_number, communication, troubleshooting, documentation, total, `date`
  FROM scores INNER JOIN agents ON agents.agent_name = scores.agent
  WHERE evaluator LIKE ?
  OR agent LIKE ?
  OR agent_number LIKE ? 
  OR communication LIKE ?
  OR troubleshooting LIKE ?
  OR documentation LIKE ? 
  OR total LIKE ?
  OR `date` LIKE ?
  ORDER BY `time` DESC");
     
      mysqli_stmt_bind_param($stmt, "ssssssss", $search, $search, $search, $search, $search, $search, $search, $search);
    
    } else {
      $stmt = mysqli_prepare($link, "SELECT evaluator, agent, agent_number, communication, troubleshooting, documentation, total, `date`
  FROM scores INNER JOIN agents ON agents.agent_name = scores.agent
  WHERE evaluator LIKE ?
  OR agent LIKE ?
  OR agent_number LIKE ? 
  OR communication LIKE ?
  OR troubleshooting LIKE ?
  OR documentation LIKE ? 
  OR total LIKE ?
  OR `date` LIKE ? 
  ORDER BY ".$ord." ".$dir);

    mysqli_stmt_bind_param($stmt, "ssssssss", $search, $search, $search, $search, $search, $search, $search, $search);
    
    }
  
    mysqli_stmt_execute($stmt);
    //mysqli_stmt_bind_result($stmt, $course_code, $course_name, $ects_credits, $semester_name);
    $results = mysqli_stmt_get_result($stmt);
  
    while($row = mysqli_fetch_array($results, MYSQLI_NUM)){
      $final = $final."<tr>
      <td ".$style0.">$row[0]</td>
      <td ".$style1.">$row[1]</td>
      <td ".$style2.">$row[2]</td>        
      <td ".$style3.">$row[3]</td> 
      <td ".$style4.">$row[4]</td>
      <td ".$style5.">$row[5]</td>
      <td ".$style6.">$row[6]</td>        
      <td ".$style7.">$row[7]</td>       
      </tr>";
    }
    return $final."</table>";
}

$t = new Template(TEMPLATE);

$t -> assign("admin", adminLink());
$t -> assign("user", "&#128100; User: ".$_SESSION['name']);
$t -> assign("title", "Results"); //title of the page
$t -> assign("title1", "Results Area");
$t -> assign("subtitle1", "Here all the employees records are collected and organised");
$t -> assign("content", "The entries are by default in cronological order (newest on top). Please click on ascend or descend for a different order.");
$t -> assign("form", $formresults);
$t -> assign("table", listCourses($link));
$t -> assign("title2", "The agents are evaluated");
$t -> assign("subtitle2", "Enjoy the data!");
$t -> assign("sidecontent1", $etMagnis);
$t -> assign("subtitle3", "More data");
$t -> assign("sidecontent2", $lorem.$etMagnis);

echo $t -> render();

mysqli_close($link);
?>
