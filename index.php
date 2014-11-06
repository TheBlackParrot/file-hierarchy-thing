<html>

<head>
	<?php
		$root = "/srv/http/old/files/files/";
		$rel_root = "files/";
		if(!isset($_GET['dir']))
			$dir = "";
		else
			$dir = urldecode(htmlspecialchars($_GET['dir'])) . "/";


		$realBase = realpath($root);

		$userpath = $root . $_GET['dir'];
		$realUserPath = realpath($userpath);

		if($realUserPath === false || strpos($realUserPath,$realBase) !== 0)
		    $dir = "";

		include "./filetypes.php";

		function human_filesize($bytes, $decimals = 2) {
			$sz = 'BKMGTP';
			$factor = floor((strlen($bytes) - 1) / 3);
			return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
		}
	?>
	<title>TheBlackParrot's Files: <?php echo $dir; ?></title>
	<link rel="stylesheet" type="text/css" href="css/reset.css">

	<style>
		body {
			font-family: "FreeSans", "Arial", "Helvetica", sans-serif;
			font-size: 10pt;
			background-color: #ddf;
			line-height: 16px;
		}
		h1 {
			font-weight: 700;
			font-size: 14pt;
			margin-bottom: 16px;
		}
		.wrapper {
			padding: 16px;
			margin: 16px;
			border: 1px solid #bbf;
			background-color: #fff;
			box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
		}
		.listing {
			width: 100%;
			margin-bottom: 16px;
			table-layout: fixed;
			box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
		}
		a {
			color: #000;
			text-decoration: none;
		}
		a:hover {
			color: #4af;
		}
		.listing th {
			text-align: left;
			background-color: #ddf;
			border: 1px solid #bbf;
			font-weight: 700;
		}
		.listing th:nth-child(1) {
			width: 16px;
			max-width: 16px;
			border-right: none;
		}
		.listing th:nth-child(2) {
			width: 50%;
			max-width: 50%;
			border-left: none;
		}
		.listing th:nth-child(3) {
			width: 35%;
			max-width: 35%;
			border-left: none;
		}
		.listing td {
			border: 1px solid #ddd;
		}
		.listing td:nth-child(1) {
			border-right: none;
		}
		.listing td:nth-child(2) {
			border-left: none;
		}
		.listing td:nth-child(3) {
		}
		.listing th, .listing td {
			padding: 4px;
			min-height: 16px;
			line-height: 16px;
			vertical-align: middle;
			overflow: hidden;
			text-overflow: ellipsis;
		}
		.listing tr {
			border: 1px solid #ddd;
		}
		.listing tr:nth-child(odd) {
			background-color: #fff;
		}
		.listing tr:nth-child(even) {
			background-color: #eef;
		}
		.hidden {
			color: #aaa;
		}
	</style>
</head>

<body>
	<div class="wrapper">
		<?php
			if(isset($_GET['dir'])) {
				if($_GET['dir'] == ".")
					echo "<h1>Root folder</h1>";
				else {
					if(substr($_GET['dir'],0,1) == ".")
						echo "<h1>" . substr($_GET['dir'],1) . "</h1>";
					else
						echo "<h1>" . $_GET['dir'] . "</h1>";
				}
			}
		?>
		<table class="listing">
			<tr>
				<th></th>
				<th>Filename</th>
				<th>Date Modified</th>
				<th>Size</th>
			</tr>
			<?php
				$files = scandir($root . $dir);

				foreach ($files as $row) {
					if($row == "." || $row == "..")
						continue;
					$full_path = $root . $dir . $row;
					if(is_dir($full_path))
						$directory_list[] = $row;
					else
						$file_list[] = $row;
				}

				foreach ($directory_list as $row) {
					if(isset($_POST['search'])) {
						if(!fnmatch(htmlspecialchars("*" . $_POST['search'] . "*"), $row))
							continue;
					}
					$full_path = $root . $dir . $row;
					$rel_path = $rel_root . $dir . $row;
					echo "<tr>";

					echo "<td>";
					echo '<img src="icons/folder.png"/>';
					echo "</td>";
					
					echo "<td>";
					if(substr($row,0,1) == ".")
						echo '<a class="hidden" href="index.php?dir=' . $dir . $row . '">' . $row . '</a>' . "<br/>";
					else
						echo '<a href="index.php?dir=' . $dir . $row . '">' . $row . '</a>' . "<br/>";
					echo "</td>";

					echo "<td>";
					echo date('F j, Y H:i:s', filemtime($full_path));
					echo "</td>";

					echo "<td>";
					echo "</td>";

					echo "</tr>";
				}

				foreach ($file_list as $row) {
					if(isset($_POST['search'])) {
						if(!fnmatch(htmlspecialchars("*" . $_POST['search'] . "*"), $row))
							continue;
					}
					$full_path = $root . $dir . $row;
					$rel_path = $rel_root . $dir . $row;
					$ext = pathinfo($full_path, PATHINFO_EXTENSION);

					echo "<tr>";

					echo "<td>";
					if(array_key_exists($ext,$filetypes))
						$icon = $filetypes[$ext];
					else
						$icon = "page_white";
					$size = human_filesize(filesize($full_path));

					echo '<img src="icons/' . $icon . '.png"/>';
					echo "</td>";
					
					echo "<td>";
					if(substr($row,0,1) == ".")
						echo '<a class="hidden" href="' . $rel_path . '">' . $row . '</a>' . "<br/>";
					else
						echo '<a href="' . $rel_path . '">' . $row . '</a>' . "<br/>";
					echo "</td>";

					echo "<td>";
					echo date('F j, Y H:i:s', filemtime($full_path));
					echo "</td>";

					echo "<td>";
					echo human_filesize(filesize($full_path));
					echo "</td>";

					echo "</tr>";
				}
			?>
		</table>
		<div style="float: right;">
			<?php
				if(!isset($_GET['dir']))
					$temp = "";
				else
					$temp = $_GET['dir'];
			?>
			<form action="<?php echo basename($_SERVER['PHP_SELF']) . "?" . $_SERVER['QUERY_STRING']; ?>" method="post">
				<input type="text" name="search" placeholder="Search"> 
				<input type="hidden" name="dir" value="<?php echo $temp; ?>">
				<input type="submit" value="Search">
			</form>
		</div>
		<?php
			if(isset($_GET['dir'])) {
				if($_GET['dir'] != ".")
					echo '<a href="index.php?dir=' . dirname(urldecode($_GET['dir'])) . '"><img src="icons/arrow_up.png"/> Parent folder</a>';
				else
					echo '<img src="icons/cancel.png"/><span style="color: #aaa;"> Top folder</span>';
			}
			else
				echo '<img src="icons/cancel.png"/><span style="color: #aaa;"> Top folder</span>';

			if(isset($_POST['search']))
		?>
	</div>
</body>

</html>