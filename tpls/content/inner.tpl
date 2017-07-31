<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" >
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link type="text/css" rel="stylesheet" href="/css/cms/style.css"/>
		<link type="text/css" rel="stylesheet" href="/css/cms/inner.css"/>
		<title>%title%</title>

		<meta name="DESCRIPTION" content="%describtion%"></meta>
		<meta name="KEYWORDS" content="%keywords%"></meta>
		<link rel="shortcut icon" href="/favicon.ico" />

		<script type="text/javascript" src="/js/easy.php"></script>
		<script type="text/javascript" src="/js/client/cookie.js"></script>

		%data getRssMeta(%pid%)%
		%data getAtomMeta(%pid%)%
	</head>
	
	<body id="umi-cms-demo">
		<div id="container">
			%system getOuterContent('./tpls/content/header.inc.tpl')%
			<div id="content">
				<div id="left" class="column">
					%content menu('sl')%

					%news lastlist('/akcii', 'akcii_inner', 1)%
				</div>
				<div id="center" class="column">
					%core navibar('default', 1, 0, 1)%

					<h2>%header%</h2>
					%content%
				</div>
				<div id="right" class="column">
					%search insert_form('inner')%
					%catalog getCategoryList('inner', '/market/')%
				</div>
			</div>
			<div id="footer">
				&copy; ООО "Юмисофт", 2007
			</div>
		</div>
	</body>
</html>