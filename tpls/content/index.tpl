<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" >
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link type="text/css" rel="stylesheet" href="/css/cms/style.css"/>
		<link type="text/css" rel="stylesheet" href="/css/cms/home.css"/>
		<title>%title%</title>

		<meta name="DESCRIPTION" content="%describtion%">
		<meta name="KEYWORDS" content="%keywords%">
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
				<div class="column">
					<div id="welcome" class="block">
						<h2>%h1%</h2>
						%content%
					</div>

					%vote insertlast('home')%
				</div>

				%news lastlist('/vse_novosti/politicheskie_novosti/ /vse_novosti/novosti_ekonomiki/', 'home', 2)%

				<div class="column">
					%search insert_form('home')%

					%forum confs_list('home')%

					%news lastlist('/akcii', 'akcii', 1)%
				</div>

				%catalog category('home', '/market/')%

				%faq projects('home', '/faq/')%

				%photoalbum album('/butterfly/', 'home', 4)%
			</div>
			%system getOuterContent('./tpls/content/footer.inc.tpl')%
		</div>
	</body>
</html>