<?php
// https://stackoverflow.com/questions/8226958/simple-php-editor-of-text-files

// configuration
$url = 'http://xxxxxxxxx.xx/maillist/list.php';
$file = 'mailfilter-catchall.log';

$errormsg = '';

$request = explode('/', trim($_SERVER['QUERY_STRING'],'/'));

// read the textfile
$filename = '/home/mbx/mailfilter/mailfilter-'.$request[0].'.log';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<title>log viewer</title>
<link href="https://fonts.googleapis.com/css?family=Source+Code+Pro&display=swap" rel="stylesheet">
<style type="text/css"> 
	/* normalize css for all elements */
	*, svg {
		font-family: 'Source Code Pro', UltimaPro, Verdana, Arial, sans-serif; font-size: 12px; color: DarkSlateGrey;
		vertical-align:baseline; /* wichtig alles auf einer Höhe bei Tabellen usw.*/
	}
	html, body { font-family: 'Source Code Pro', monospace; margin: 0px; padding: 0px;}
	hr { border: 0; height: 1px; background: #333; background: -webkit-gradient(linear, 0 0, 20% 0, from(#CCC), to(#CCC), color-stop(50%, #333)); }
	.navbar { font-family: UltimaPro, Verdana, Arial, sans-serif;  font-size: 14px; overflow: hidden; background-color: #f60; position: fixed; top: 0; width: 100%; }
	.navbar a { float: left; display: block; color: #f2f2f2; text-align: center; padding: 14px 16px; text-decoration: none; }
	.navbar a:hover { background: #ddd; color: black; }
	.main { margin-top: 30px; margin-bottom: 30px; padding-left: 10px;}
	pre, code { font-family: 'Source Code Pro', monospace; color: gray;}
	pre { overflow: auto; }
	pre > code { display: block; padding: 1rem; word-wrap: normal; }
	.num { float: left; color: gray; text-align: right; margin-right: 6pt; padding-right: 6pt; border-right: 1px solid gray;} 
</style>
</head>
<body>
--------------------------------------------	
	<div class="navbar">
		<a href="?catchall#end">catchall</a>
		<a href="?post#end">post</a>
		<a href="?doris#end">doris</a>
		<a href="?dieter#end">dieter</a>
		<a href="?nico#end">nico</a>
		<a href="?lina#end">lina</a>
		<a href="?hermann#end">hermann</a>
		<a href="?hildegard#end">hildegard</a>
		<a href="#top">start</a>
		<a href="#end">ende</a>
	</div>
	
	<div class="main">
		<a name="start"></a> <br /><br />
		<?php
// 			print_r( $request);
// 			show_source($filename); 
// 			$lines =  explode("\n",file_get_contents( $filename ));
			foreach(file($filename) as $line) {
				$line = preg_replace('/-{44}/', '<hr>', $line);
				$line = preg_replace('/(http[s]?:\/{2}[a-zA-Z0-9$-_@.&+\(\)]+)/', '<span style="text-decoration:underline; color:DarkRed;">$1</span>', $line);
				$line = preg_replace('/\s\/([a-zA-Z0-9-_]*\/?)*/', '<span style="text-decoration:underline; color:DarkRed;">$0</span>', $line);
				$line = preg_replace('/([A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]+)/', '<span style="text-decoration:underline; color:DarkRed;">$1</span>', $line);
				$line = preg_replace('/(\(.*\))/', '<span style="background-color:Gainsboro;">$1</span>', $line);
				$line = preg_replace('/(\[.*\])/', '<span style="background-color:Gainsboro;">$1</span>', $line);
				$line = preg_replace('/^(DATE.*)$/i', '<span style="font-weight: bold; color:CadetBlue;">$1</span>', $line);
				$line = preg_replace('/\b(From)\b/', '<span style="font-weight: bold; color:blue;">$1</span>', $line);
				$line = preg_replace('/\b(Delivered-to)\b/i', '<span style="font-weight: bold; color:green;">$1</span>', $line);
				$line = preg_replace('/\b(To)\b/', '<span style="font-weight: bold; color:green;">$1</span>', $line);
				$line = preg_replace('/\b(SUBJECT)\b/i', '<span style="font-weight: bold; color:orange;">$1</span>', $line);
				$line = preg_replace('/\b(Subj)\b/', '<span style="font-weight: bold; color:orange;">$1</span>', $line);
				$line = preg_replace('/\b(File)\b/', '<span style="font-weight: bold; color:green;">$1</span>', $line);

				$line = preg_replace('/\b(MAILDOMAIN)\b/', '<span style="font-weight: bold;">$1</span>', $line);
				$line = preg_replace('/\b(MAILUSER)\b/', '<span style="font-weight: bold;">$1</span>', $line);
				$line = preg_replace('/\b(Dropped)\b/', '<span style="font-weight: bold; color:OrangeRed;">$1</span>', $line);
				$line = preg_replace('/(X-Rspamd-Bar\sScore>\s\d+)/', '<span style="font-weight: bold; color: DarkRed;">$1</span>', $line);
				$line = preg_replace('/\b(SpamScore=\d+)\b/', '<span style="font-weight: bold; color: DarkRed;">$1</span>', $line);
				
 				echo $line. "<br />";
			}
			
		?>
		<br /><br />
		<a name="end"></a> 
	</div>


</body>
</html>
