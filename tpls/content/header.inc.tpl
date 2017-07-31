			<h1><span>Демо-сайт UMI.CMS</span></h1>
			<div id="quick-links">
				<a href="/" id="home" title="Главная"><span>Главная</span></a>
				<a href="/content/sitemap/" id="sitetree"  title="Дерево сайта"><span>Дерево сайта</span></a>
				<a href="/contacts/" id="mailto"  title="Написать письмо"><span>Написать письмо</span></a>
			</div>

			<div id="banner468x60">
				%banners insert('top_banner')%
			</div>

			<div id="header">
				<div id="langs">
					<a class="active">рус</a> <a href="/en/">eng</a>
				</div>

				%users auth('header')%

				<div class="banner">
					%banners insert('text_banner')%
				</div>
			</div>
			%menu%
