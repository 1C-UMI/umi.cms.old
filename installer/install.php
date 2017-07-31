<?php
	ini_set("display_errors", "1");
	
	header("Content-type: text/html; charset=utf-8");

	class umiDistrReader {
		protected $distrFilePath, $fh;

		public		$signature = "ucp", $author, $comment, $timestamp, $totalSize;
		protected	$version = "1.0.0";


		public function __construct($distrFilePath) {
			if(!is_file($distrFilePath)) {
				trigger_error("Distributive file \"{$distrFilePath}\" doesn't exists", E_USER_ERROR);
			}

			$this->distrFilePath = $distrFilePath;
			$this->readHeader();
		}


		public function __destruct() {
			if(is_resource($this->fh)) {
				fclose($this->fh);
			}
		}


		protected function readHeader() {
			if(!is_readable($this->distrFilePath)) {
				trigger_error("Distributive file \"{$this->distrFilePath}\" is not readable", E_USER_ERROR);
			}

			$this->fh = $f = fopen($this->distrFilePath, "r");

			fseek($f, 0);
			if(stream_get_line($f, 5, "\0") != $this->signature) {
				trigger_error("Distributive file corrupted: wrong signature", E_USER_ERROR);
				return false;
			}

			fseek($f, 5);
			if(version_compare($needle_version = stream_get_line($f, 5, "\0"), $this->version, "<=") != 1) {
				trigger_error("You need installer at least version {$needle_version} to read this distribute file", E_USER_ERROR);
				return false;
			}

			fseek($f, 10);
			$this->timestamp = (int) stream_get_line($f, 15, "\0");

			fseek($f, 25);
			$this->totalSize = (int) stream_get_line($f, 25, "\0");

			fseek($f, 50);
			$this->author = (string) stream_get_line($f, 25, "\0");

			fseek($f, 75);
			$this->comment = (string) stream_get_line($f, 330, "\0");

			fseek($f, 331);
		}


		public function getNextResource($pos = false) {
			$f = $this->fh;

			if($pos !== false) {
				fseek($f, $pos);
			}

			$p = ftell($f);

			$blockSize = (int) stream_get_line($f, 25, "\0");

			fseek($f, $p + 25);
			$blockData = (string) stream_get_line($f, $blockSize);

			if(strlen($blockData) == $blockSize) {
				$obj = unserialize(base64_decode($blockData));
				return $obj;
			} else {
				return false;
			}
		}


		public function getCurrentPos() {
			return ftell($this->fh);
		}
	};

	abstract class umiDistrInstallItem {
		abstract public function __construct($filePath = false);

		abstract public function pack();
		abstract public static function unpack($data);

		abstract public function restore();

		abstract public function getDescription();
	};

	class umiDistrFolder extends umiDistrInstallItem {
		protected $filePath, $permissions;

		public function __construct($filePath = false) {
			if($filePath !== false) {
				$this->filePath = $filePath;
				$this->permissions = fileperms($filePath) & 0x1FF;
			}
		}

		public function pack() {
			return base64_encode(serialize($this));
		}

		public static function unpack($data) {
			return base64_decode(unserialize($data));
		}

		public function restore() {
			if(!file_exists($this->filePath)) {
				$pathinfo = pathinfo($this->filePath);

				if(is_dir($pathinfo['dirname'])) {
					mkdir($this->filePath);
				}
			}

			if(is_dir($this->filePath)) {
				if(function_exists("posix_getuid")) {
					//Posix-like system, so we must change file permissions

						chmod($this->filePath, $this->permissions);
					return true;
				}
			} else {
				return false;
			}
		}

		public function getDescription() {
			return $this->filePath;
		}
	};

	class umiDistrFile extends umiDistrInstallItem {
		protected $filePath, $permissions, $content;

		public function __construct($filePath = false) {
			if($filePath !== false) {
				$this->filePath = $filePath;
				$this->permissions = fileperms($filePath) & 0x1FF;
				$this->content = file_get_contents($filePath);
			}
		}

		public function pack() {
			return base64_encode(serialize($this));
		}

		public static function unpack($data) {
			return base64_decode(unserialize($data));
		}

		public function restore() {
			if(!is_file($this->filePath)) {
				file_put_contents($this->filePath, $this->content);
			}

			if(is_file($this->filePath)) {
				if(function_exists("posix_getuid")) {
					//Posix-like system, so we must change file permissions

						chmod($this->filePath, $this->permissions);
					return true;
				}
			} else {
				trigger_error("Failed to create file (\"{$this->filePath}\")", E_USER_ERROR);
				return false;
			}
		}


		public function getDescription() {
			return $this->filePath;
		}
	};

	class umiDistrMySql extends umiDistrInstallItem {
		protected $tableName, $permissions, $sqls = Array();

		public function __construct($tableName = false) {
			if($tableName !== false) {
				$this->tableName = $tableName;
				$this->readTableDefinition();
				$this->readData();
			}
		}

		public function pack() {
			return base64_encode(serialize($this));
		}

		public static function unpack($data) {
			return base64_decode(unserialize($data));
		}

		public function restore() {
			$sql = "TRUNCATE TABLE {$this->tableName}";
			mysql_query($sql);

			if($err = mysql_error()) {
			}

			$sz = sizeof($this->sqls);
			for($i = 0; $i < $sz; $i++) {
				$sql = $this->sqls[$i];

				mysql_query($sql);
				if($err = mysql_error()) {
				}
			}
		}


		protected function readTableDefinition() {
			$sql = "SHOW CREATE TABLE {$this->tableName}";
			$result = mysql_query($sql);
			list(, $cont) = mysql_fetch_row($result);
			$this->sqls[] = $cont;
		}


		protected function readData() {
			$sql = "SELECT * FROM {$this->tableName}";
			$result = mysql_query($sql);
			while($row = mysql_fetch_assoc($result)) {
				$this->sqls[] = $this->generateInsertRow($row);
			}
		}


		protected function generateInsertRow($row) {
			$sql = "INSERT INTO {$this->tableName} (";

			$fields = array_keys($row);
			$sz = sizeof($fields);
			for($i = 0; $i < $sz; $i++) {
				$sql .= $fields[$i];

				if($i < ($sz - 1)) {
					$sql .= ", ";
				}
			}
			unset($fields);

			$sql .= ") VALUES(";



			$values = array_values($row);
			$sz = sizeof($values);
			for($i = 0; $i < $sz; $i++) {
				$sql .= "'" . mysql_escape_string($values[$i]) . "'";

				if($i < ($sz - 1)) {
					$sql .= ", ";
				}
			}
			unset($values);


			$sql .= ")";

			return $sql;
		}

		public function getDescription() {
			return $this->tableName;
		}
	};
?><?php
header("Content-Type: text/html; charset=utf-8");
if(is_file("./installed")) {
	include "./errors/install_completed.html";
	exit();
}

if (version_compare(PHP_VERSION , "5.0.0", "<")) {
	exit("Для установки UMI.CMS необходим PHP 5 и выше");
} 

error_reporting(~E_ALL);


if($_REQUEST['action'] == "initInstall") {
	header("Content-type: text/javascript; charset=utf-8");

	$requestId = $_REQUEST['requestId'];
	$pos = (int) $_REQUEST['pos'];

	$package = "./packages/". $_REQUEST['package'];

	$size = filesize($package);

	$distr = new umiDistrReader($package);

	if(!$pos) $pos = $distr->getCurrentPos();


	for($i = 0; $i < 50; $i++) {
		$obj = $distr->getNextResource($pos);

		$descr = $obj->getDescription();
		$pos = $distr->getCurrentPos();

		if($obj instanceof umiDistrMySql) {
			include "./mysql.php";

			mysql_query("SET AUTOCOMMIT=0");

			if($err = mysql_error()) {
				trigger_error($err, E_USER_ERROR);
			}


			mysql_query("SET FOREIGN_KEY_CHECKS=0");

			if($err = mysql_error()) {
				trigger_error($err, E_USER_ERROR);
			}

			$obj->restore();
			unset($obj);

			mysql_query("SET AUTOCOMMIT=1");

			if($err = mysql_error()) {
				trigger_error($err, E_USER_ERROR);
			}

			mysql_query("SET FOREIGN_KEY_CHECKS=1");

			if($err = mysql_error()) {
				trigger_error($err, E_USER_ERROR);
			}


			mysql_query("COMMIT");

			if($err = mysql_error()) {
				trigger_error($err, E_USER_ERROR);
			}

			break;
		} else {
			$obj->restore();
			unset($obj);
		}
	}

	$is_done = ($pos >= $size) ? "1" : "0";
	$proc = round($pos / $size * 100);


	if($is_done) {
		include "./config.php";

		mysql_query("UPDATE cms3_domains SET host = '" . mysql_escape_string($_SERVER['HTTP_HOST']) . "' LIMIT 1");

		regedit::getInstance()->setVar("//settings/keycode", $_REQUEST['keycode']);

		$object_id = 14;
		$object = umiObjectsCollection::getInstance()->getObject($object_id);
		
		if(!$_REQUEST['fname']) {
			$_REQUEST['fname'] = "Supervisor";
		}
		
		$object->setValue("lname", $_REQUEST['lname']);
		$object->setValue("fname", $_REQUEST['fname']);
		$object->setValue("father_name", $_REQUEST['mname']);
		$object->setValue("e-mail", $_REQUEST['email']);
		$object->commit();
	}

	echo <<<JS

var response = {
	'status'		: 	'{$is_done}',
	'pos'			:	'{$pos}',
	'descr'			: 	'{$descr}',
	'proc'			:	'{$proc}'
};

requestsController.getSelf().reportRequest('{$requestId}', response);


JS;

	exit();
}








if(array_key_exists("s", $_REQUEST))
	$step = $_REQUEST['s'];
else
	$step = false;


function cms_mkfile($pdir, $cont = "") {
	if(!$pdir)
		return;
	if(!is_file($pdir)) {
		file_put_contents($pdir, $cont);
		chmod($pdir, 0777);
	}
}


$C_STEPS = "";
$steps = Array( "Ознакомление с лицензионным соглашением",
		"Проверка лицензионного ключа",
		"Проверка обязательных параметров и настроек сервера",
		"Выбор или загрузка пакета установки",
		"Настройка базы данных",
		"Установка",
		"Установка пароля для супервайзера"
		);


$sz = sizeof($steps);
for($i = 0; $i < $sz && $step; $i++) {
	$cs = $steps[$i];
	if($step == ($i + 1))
		$cs = "<b>" . $cs . "</b>";

	$C_STEPS .= "\t\t\t\t<li>" . $cs . "</li>\r\n";
}

if($C_STEPS && $step && $step != 8) {

	$C_STEPS = <<<END

			<ol>
$C_STEPS
			</ol>

END;

} else {
	$C_STEPS = "";
}


$C_TITLE = "Начало установки";
$C_H1 = "Установка системы";
$C_CONTENT = <<<END
<p>Для автоматической установки <b>UMI.CMS 2.0</b> необходимо пройти следующие этапы:</p>
<ol>
	<li>Ознакомление с лицензионным соглашением</li>
	<li>Проверка лицензионного ключа</li>
	<li>Проверка обязательных параметров и настроек сервера</li>
	<li>Выбор или загрузка пакета установки</li>
	<li>Настройка базы данных</li>
	<li>Установка</li>
	<li>Установка пароля для супервайзера</li>
</ol>

<form method="post">
	<input type="hidden" name="s" value="1" />
	<input type="submit" value="Приступить к установке >>" />
</form>
END;


if($step == 1) {
	$C_TITLE = "Шаг 1";
	$C_H1 = "Лицензионное соглашение";

	$C_CONTENT = <<<END
<textarea class="licence" readonly="readonly">
ЛИЦЕНЗИОННОЕ СОГЛАШЕНИЕ

Настоящее лицензионное соглашение (далее "лицензионное соглашение") является юридическим соглашением, заключаемым между физическим или юридическим лицом (Клиентом) и ООО "Юмисофт" (Лицензиар) относительно использования программного продукта Лицензиара, сопровождаемого настоящим лицензионным соглашением, который включает в себя программное обеспечение, а также может включать соответствующие носители, любые печатные материалы, и любую "встроенную" или "электронную" документацию (далее "программный продукт").

Программный продукт может сопровождаться документами, дополняющими или изменяющими настоящее лицензионное соглашение. 

УСТАНАВЛИВАЯ, КОПИРУЯ ИЛИ ИНЫМ ОБРАЗОМ ИСПОЛЬЗУЯ ПРОГРАММНЫЙ ПРОДУКТ, ВЫ ТЕМ САМЫМ ПОЛНОСТЬЮ И БЕЗОГОВОРОЧНО СОГЛАШАЕТЕСЬ С ПОЛОЖЕНИЯМИ И УСЛОВИЯМИ НАСТОЯЩЕГО ЛИЦЕНЗИОННОГО СОГЛАШЕНИЯ. 

ТЕРМИНЫ И ОПРЕДЕЛЕНИЯ
Лицензиар - обладатель исключительных прав на результат интеллектуальной деятельности - программный продукт "UMI.CMS" (далее программный продукт).
Лицензиат - юридическое и физическое лицо, реализующее программный продукт "UMI.CMS" и заключивший с Лицензиаром лицензионный договор.
Лицензия (лицензионное соглашение) - соглашение между Лицензиаром и Клиентом об использовании продукта как конечным пользователем.
Лицензионный ключ (ключ) - цифробуквенная последовательность взаимосвязанная с базой данных (программой), конкретным IP адресом и доменным именем Лицензиара с помощью которой активируется копия программного продукта.
Хостинговая площадка - аппаратно-программный комплекс, доступ к которому предоставляется третей стороной (хостингом) для размещения и поддержания сайта в сети Интернет.
Клиент (пользователь, конечный пользователь) - физическое или юридическое лицо приобретающее программный продукт для создания собственного сайта.
Программный продукт - результат интеллектуальной деятельности предназначенный  для управления содержимым сайта.

1. ОБЪЕМ ЛИЦЕНЗИИ. Лицензиар предоставляет Клиенту следующие права при условии соблюдения всех положений и условий настоящего лицензионного соглашения.
Предметом настоящего Лицензионного соглашения является право использования одного экземпляра программного продукта, предоставляемое Клиенту Лицензиаром, в порядке и на условиях, установленных настоящим соглашением.
Все положения настоящего Лицензионного соглашения распространяются как на весь программный продукт в целом, так и на его отдельные компоненты. Клиенту предоставляется право создать один Сайт на базе одного экземпляра программного продукта. Создание большего числа Сайтов возможно только при условии приобретения дополнительных экземпляров программного продукта.
При возникновении у Клиента потребности в расширении конфигурации программного продукта, Клиент в праве приобрести дополнительные модули на условиях приобретения самого программного продукта.

2. ПРОЧИЕ ПРАВА И ОГРАНИЧЕНИЯ. Лицензиар использует лицензионный ключ для управления авторскими правами для защиты и предотвращения незаконного присвоения, прав на интеллектуальную собственность.

3. СОХРАНЕНИЕ ПРАВ И СОБСТВЕННОСТИ. Все права, которые не предоставлены явно по настоящему лицензионному соглашению, сохраняются за Лицензиаром. Программный продукт защищен законами и международными соглашениями об авторских правах и иных правах на интеллектуальную собственность. Все права собственности, авторские права и другие права на интеллектуальную собственность в отношении программного продукта принадлежат Лицензиару и (или) его Лицензиатам (поставщикам) в объеме установленном отдельными договорами между Лицензиаром и Лицензиатом. Клиент не вправе удалять с сайта любую информацию об авторских правах Лицензиата на программный продукт.  

4. ОГРАНИЧЕНИЯ НА ВСКРЫТИЕ ТЕХНОЛОГИИ И ДЕКОМПИЛЯЦИЮ. Запрещается вскрывать технологию, расшифровывать, декодировать, производить обратный инжиниринг или декомпилировать продукт и любых его компонентов за исключением случаев и только в той степени, когда такие действия явно разрешены законодательством, несмотря на наличие в лицензионном соглашении данного ограничения.

5. ОБНОВЛЕНИЯ. Клиенту предоставляется право бесплатного обновления программного продукта в течение одного года с момента его приобретения, в том случае, если таковые подготовлены Лицензиаром. Клиент программного продукта может получать обновления посредством предусмотренного в Продукте механизма автоматических обновлений.

6. ПЕРЕДАЧА ПРОГРАММНОГО ПРОДУКТА. Допускается только внутренняя передача. Программный продукт разрешается переносить на другую хостинговую площадку Клиента. После переноса программного продукта на другую хостинговую площадку его следует полностью удалить с исходной хостинговой площадки. Передача третьим лицам в любой форме запрещена. 

7. РАСТОРЖЕНИЕ СОГЛАШЕНИЯ. Без ущерба для каких-либо иных своих прав Лицензиар вправе прекратить действие настоящего лицензионного соглашения при несоблюдении Клиентом его положений и условий. При прекращении действия лицензионного соглашения Клиент обязан уничтожить все имеющиеся у него копии программного продукта и всех его составных частей.

9. ПОЛНОТА СОГЛАШЕНИЯ. СТЕПЕНЬ ДЕЙСТВИЯ. Настоящее лицензионное соглашение (включая любые документы, дополняющие или изменяющие настоящее лицензионное соглашение, сопровождающие программный продукт) составляет полное соглашение между Клиентом и Лицензиаром относительно программного продукта и услуг по технической поддержке (если таковые предоставляются) и заменяет собой все предшествующие или одновременные устные или письменные договоренности, предложения и заверения относительно программного продукта и любых других положений настоящего лицензионного соглашения. В случае противоречий между положениями программного продукта и политики Лицензиара по оказанию технической поддержки и положениями настоящего лицензионного соглашения, данное лицензионное соглашение имеет преимущественную силу. Если какое-либо положение настоящего лицензионного соглашения будет признано аннулированным, недействительным, не имеющим юридической силы или незаконным, то остальные положения настоящего лицензионного соглашения сохраняют свою полную силу и действие.

10. ГАРАНТИЯ. Программный продукт предназначается и предоставляется в качестве системы управления сайтом, в состоянии "как есть" со всеми недостатками, которые он может иметь на момент предоставления. Вы соглашаетесь с тем, что никакой программный продукт не свободен от ошибок. При условии наличия у вас действительной лицензии Лицензиар гарантирует, что:
?	в течение 90 дней с даты получения лицензии на использование продукта либо в течение наименьшего срока, допускаемого законодательством, функционирование программного продукта будет в основном соответствовать сопровождающим продукт документам. В случае выявления попыток изменения кода или попыток иного несанкционированного вмешательства в программный продукт, гарантийные и любые иные обязательства Лицензиара аннулируются.
?	любые услуги по технической поддержке, предоставляемые Лицензиаром, будут в основном соответствовать описанию, содержащемуся в соответствующих документах, предоставляемых Клиенту Лицензиаром, и инженеры по технической поддержке Лицензиара приложат все разумные усилия, проявят разумную заботу и применят профессиональные навыки для разрешения проблемных вопросов. Если программный продукт не соответствует настоящей гарантии, Лицензиар либо осуществит исправление или замену продукта, либо вернет сумму уплаченной вами цены (если продукт приобретен у Лицензиара).
Настоящая гарантия недействительна, если сбой в работе продукта возник в результате неосторожности, неправильного обращения или применения. В случае замены, в отношении любого заменяющего продукта гарантия будет действовать в течение периода, оставшегося от изначального гарантийного срока, или в течение 30 дней, в зависимости от того, какой из указанных периодов будет больше. Клиент соглашается с тем, что вышеуказанная гарантия является единственной имеющейся у Клиента гарантией в отношении продукта и любых услуг по технической поддержке.
Любое обслуживание проданного программного продукта, в том числе гарантийное, а так же консультирование, устранение неисправностей, техническая поддержка, любая иная помощь Лицензиара, предусмотренная условиями настоящего соглашения оказывается по адресам электронной почты и реквизитам, указанным в настоящем соглашением в соответствии с правилами Лицензиара, в случае если продукт приобретен у Лицензиара.
Лицензиар не предоставляет на время соответствующих работ (согласно предыдущему пункту) какую-либо временную замену программного обеспечения, либо компьютера.
Лицензиар не производит гарантийных работ, если такие работы обусловлены неудовлетворительной работой программного продукта с иным программным обеспечением, установленным и используемым на компьютере незаконно, без соответствующих и необходимых в соответствии с законом лицензий и разрешений, как и в том случае, если компьютер неисправен, либо не соответствует минимальным требованиям, предъявляемым программным продуктом к компьютеру.
Лицензиар не несет ответственности за работу программного продукта и отказывает в его гарантийном обслуживании, если он был каким-либо образом изменен (изменены качества, свойства, функции, назначение, структура), способами, не предусмотренными в документации к продукту, а так же, если он был поврежден иным программным обеспечением, в силу свойств такого программного обеспечения, в случае несоответствия аппаратного обеспечения техническим условиям предъявляемым продутом, а так же, если программный продукт был поврежден компьютерным вирусом, иной вредоносной программой, либо поврежден Клиентом или третьими лицами умышленно, равно как и по неосторожности.
В случае, если настоящий программный продукт приобретен у Лицензиата (Поставщика), Клиент, во всех случаях предусмотренных настоящим соглашением обязан предъявлять требования, заявлять претензии, задавать вопросы, требовать технической поддержки и реализовывать иные права в отношении программного продукта, в том числе предусмотренные настоящим соглашением через такого Лицензиата (Поставщика). 
Пользователь может изменять, добавлять или удалять любые файлы приобретенного программного продукта в соответствии с Российским Законодательством об авторском праве. В этом случае Лицензиар не гарантирует бесперебойную работу программного продукта и обновлений.

11. ОГРАНИЧЕНИЕ ОТВЕТСТВЕННОСТИ. В максимальной степени, допускаемой законодательством и за исключением случаев, предусмотренных гарантией, Лицензиар и его поставщики не несут ответственность за какие-либо убытки и/или ущерб (в том числе, убытки в связи недополученной коммерческой выгодой, прерыванием коммерческой и производственной деятельности, утратой данных), возникающие в связи с использованием или невозможностью использования программного продукта, даже если Лицензиар был уведомлена о возможном возникновении таких убытков и/или ущерба. В любом случае ответственность Лицензиара по любому из положений настоящего лицензионного соглашения ограничивается суммой, фактически уплаченной Клиентом Лицензиару за программный продукт. Настоящие ограничения не применяются в отношении тех видов ответственности, которые не могут быть исключены или ограничены в соответствии с законом.
Лицензиар не несет ответственности ни при каких обстоятельствах за любую упущенную выгоду, ущерб, моральный ущерб, убытки и вред, причиненный кому бы то ни было в результате использования программного продукта, утраты информации и прочего, если не будет доказан умысел Лицензиара в причинении вышеуказанных последствий.
В случае, если настоящий программный продукт приобретен у Лицензиата (Поставщика), всю ответственность перед Клиентом несет такой Лицензиат (Поставщик).
При невозможности решить спор или претензии заявленные Лицензиару мирным путем, стороны настоящего соглашения договорились о подсудности такого спора по месту нахождения Лицензиара. Применимое материальное и процессуальное право Российской Федерации.
В случае возникновения у Клиента вопросов, касающихся настоящего лицензионного соглашения, или необходимости связаться с Лицензиаром необходимо использовать форму обращения на сайте http://www.umi-cms.ru

</textarea>

<p>
	<form method="post">
		<input type="button" value="Я НЕ согласен" onclick="javascript: nextStep(0);" />
		<input type="hidden" name="s" value="2" />
		<input type="submit" value="Я согласен (10 сек)" id="agreeButton" />
	</form>
</p>

<script type="text/javascript">
	readTime();
</script>

END;

}

function bool2str($arg1, $arg2, $arg3) {
	if($arg1)
		return $arg2;
	else
		return $arg3;
}

function colorize($txt, $bool) {
	if($bool)
		return "<span class='c_true'>" . $txt . "</span>";
	else
		return "<span class='c_false'>" . $txt . "</span>";
}

if($step == 2) {
	$C_TITLE = "Шаг 2";
	$C_H1 = "проверка лицензионного ключа";


	if($_SERVER['SERVER_ADDR'] == "127.0.0.1" && $_SERVER['HTTP_HOST'] == "localhost") {
		header("Location: /install.php?s=3?fname=Супервайзер");
		exit();
	}

	$C_CONTENT = "";


	$C_CONTENT .= <<<END

<form method="post" id="license_form">

<table border="0" width="600">
	<tr>
		<td width="150">
			<b>Лицензионный ключ:</b>
		</td>
		<td>
			<input type="text" id="keycode" class="text" style="width: 350px;" value="" />
		</td>
	</tr>
</table>


<div id="license_msg"></div>

<input type="hidden" name="domain_keycode" value="" />
<input type="hidden" name="lname" value="" />
<input type="hidden" name="fname" value="" />
<input type="hidden" name="mname" value="" />
<input type="hidden" name="email" value="" />


<p><input type="button" value="Проверить >>" onclick="javascript: checkLicenseCode();" id="licenseButton" /></p>

END;
}

if($step == 3) {
	$C_TITLE = "Шаг 3";
	$C_H1 = "проверка настроек сервера";

	$uname = PHP_OS;

	$safe_mode =  (int) ini_get('safe_mode');

	$current_dir = trim(dirname(__FILE__));

	$safe_mode_txt = colorize(bool2str($safe_mode, "Включен", "Выключен"), !$safe_mode);

	$cd_r = (int) is_readable($current_dir);
	$cd_w = (int) is_writeable($current_dir);

	$cd_r_txt = colorize(bool2str($cd_r, "Разрешено", "Запрещено <a href=\"@\" onclick='javascript: return switchLog(\"permsFix\");'>подробнее</a>"), $cd_r);
	$cd_w_txt = colorize(bool2str($cd_w, "Разрешено", "Запрещено <a href=\"@\" onclick='javascript: return switchLog(\"permsFix\");'>подробнее</a>"), $cd_w);

	$user = get_current_user();

	$max_etime = ini_get('max_execution_time');

	$server_good = (int) (!$safe_mode && $cd_r && $cd_w);

	$cc = $_REQUEST['domain_keycode'];
	$lname = $_REQUEST['lname'];
	$fname = $_REQUEST['fname'];
	$mname = $_REQUEST['mname'];
	$email = $_REQUEST['email'];
	$phone = $_REQUEST['phone'];


	$gtxt = <<<END
<p><b>Настройки хостинга полностью удовлетворяют требованиям UMI.CMS 2.0</b></p>

<form method="get" id="nextform">

<input type="hidden" name="s" value="4" />
<input type="hidden" name="keycode" value="$cc" />
<input type="hidden" name="lname" value="$lname" />
<input type="hidden" name="fname" value="$fname" />
<input type="hidden" name="mname" value="$mname" />
<input type="hidden" name="email" value="$email" />
<input type="hidden" name="phone" value="$phone" />
<input type="hidden" name="is_free" value="$is_free" />

<p><input type="submit" value="Продолжить >>" /></p>

</form>

<script type="text/javascript">
	/* added later - just usability fix */
	if(nf = document.getElementById('nextform'))
		nf.submit();
</script>

END;

	$server_good_txt = colorize(bool2str($server_good, $gtxt, "<b>Некоторые настройки хостинга не удовлетворяют требованиям UMI.CMS 2.0</b>"), $server_good);

	$C_CONTENT = <<<END

<ol>
	<li>ОС сервера: $uname</li>
	<li>Безопасный режим (php safe_mode): $safe_mode_txt</li>
	<li>Текущая директория: "$current_dir"</li>

	<ul>
		<li>Чтение: $cd_r_txt</li>
		<li>Запись: $cd_w_txt</li>
		<li>Владелец: $user</li>
	</ul>

	<li>Максимальное время выполнения скрипта: $max_etime сек</li>
</ol>

<div id="permsFix" style="display: none; color: darkblue;">
<p>На корневую директорию, в которую вы устанавливаете UMI.CMS ("$current_dir") дожны стоять права на чтение и запись (0777).</p>
<p>Чтобы исправить, зайдите через ваш FTP-клиент (например, Far, windows commander, cuteFTP) на этот хостинг, найдите там папку ("$current_dir"), и зайдите в редактирование аттрибутов этой папки (обычно CTRL+A или ALT+A). Там надо будет либо выделить все галочки, либо ввести 777.</p>
</div>

$server_good_txt

END;
	
}

if($step == 4) {

	$C_TITLE = "Шаг 4";
	$C_H1 = "Выбор пакета";

	$current_dir = trim(dirname(__FILE__));
	$pdir = $current_dir . "/packages";

	$dir = opendir($pdir);

	$pks = "";
	while($obj = readdir($dir)) {
		$opath = $pdir . "/" . $obj;
		if(!is_file($opath))
			continue;

		$pks .= "\t<option name='$obj'>". $obj . "</option>\r\n";

	}

	$cc = $_REQUEST['keycode'];
	$lname = $_REQUEST['lname'];
	$fname = $_REQUEST['fname'];
	$mname = $_REQUEST['mname'];
	$email = $_REQUEST['email'];
	$phone = $_REQUEST['phone'];

	$next_step = 5;

	$C_CONTENT = <<<END
<form method="get">
<input type="hidden" name="s" value="5" />
<p>Список доступных пакетов: 
<select style="width: 350px;" name="package">
$pks
</select><br />

</p>

<input type="hidden" name="keycode" value="$cc" />
<input type="hidden" name="lname" value="$lname" />
<input type="hidden" name="fname" value="$fname" />
<input type="hidden" name="mname" value="$mname" />
<input type="hidden" name="email" value="$email" />
<input type="hidden" name="phone" value="$phone" />


<p><input type="submit" value="Продолжить >>" /></p>
</form>
END;

}

if($step == 5) {
	$C_TITLE = "Шаг 5";
	$C_H1 = "Настройка параметров базы данных(БД)";

	$package = $_REQUEST['package'];

	$cc = $_REQUEST['keycode'];
	$lname = $_REQUEST['lname'];
	$fname = $_REQUEST['fname'];
	$mname = $_REQUEST['mname'];
	$email = $_REQUEST['email'];
	$phone = $_REQUEST['phone'];


	$C_CONTENT = <<<END

<p>Данная версия UMI.CMS 2 работает на БД MySql. Для установки необходимо задать параметры подключения к БД.</p>
<p>Если вы не знаете этих данных или не уверены в их корректности обратитесь к вашему системному администратору или к администратору вашего хостинга.</p>

<form method="get">
<input type="hidden" name="s" value="6" />
<input type="hidden" name="package" value="$package" />

<input type="hidden" name="keycode" value="$cc" />
<input type="hidden" name="lname" value="$lname" />
<input type="hidden" name="fname" value="$fname" />
<input type="hidden" name="mname" value="$mname" />
<input type="hidden" name="email" value="$email" />
<input type="hidden" name="phone" value="$phone" />

<table border="0" width="400">
	<tr>
		<td width="80">
			<b>Хост:</b>
		</td>
		<td>
			<input type="text" name="db_hostname" class="text" value="localhost" />
		</td>
	</tr>

	<tr>
		<td>
			<b>Логин:</b>
		</td>
		<td>
			<input type="text" name="db_login" class="text" />
		</td>
	</tr>

	<tr>
		<td>
			<b>Пароль:</b>
		</td>
		<td>
			<input type="password" name="db_password" class="text" />
		</td>
	</tr>

	<tr>
		<td>
			<b>Имя БД:</b>
		</td>
		<td>
			<input type="text" name="db_dbname" class="text" />
		</td>
	</tr>
</table>

<p><input type="submit" value="Установить >>" /></p>

</form>

END;
}

function addStep($txt, $err = "") {
	global $steps;

	if(!$err)
		$str = "\t<li>" . $txt . "</li>\r\n";
	else
		$str = "\t<li>" . $txt . " - <span class='c_false'>" . $err . "</span></li>\r\n";

	$steps .= $str;
}


if($step == 6) {

error_reporting(E_ALL);
	$C_TITLE = "Шаг 6";
	$C_H1 = "Распаковка архива и создание базы данных";

	$steps = "";

	$current_dir = trim(dirname(__FILE__));

	$db_hostname = trim($_REQUEST['db_hostname']);
	$db_login = trim($_REQUEST['db_login']);
	$db_password = trim($_REQUEST['db_password']);
	$db_dbname = trim($_REQUEST['db_dbname']);

	$db = @mysql_connect($db_hostname, $db_login, $db_password);

	$db_connected = false;
	if($db)
		addStep("Проверка соединения с БД");
	else
		addStep("Проверка соединения с БД", "Ошибка: " . mysql_error());

	if(@mysql_select_db($db_dbname)) {
		addStep("Проверка существования БД с указанным именем");
			$db_connected = true;
		}
	else
		addStep("Проверка существования БД с указанным именем", "Ошибка: " . mysql_error());

	mysql_query("SET NAMES utf8_general_ci");
	mysql_query("SET CHARSET utf8");

	mysql_query("SET CHARACTER SET utf8");
	mysql_query("SET SESSION collation_connection = 'utf8_general_ci'");


	if($db_connected) {
		$C_CONTENT = "ok";

	$config_cont = <<<END
<?php
	error_reporting(~E_ALL);

	/* UMI.CMS 2.0 mySQL - connection to database */
	mysql_connect("$db_hostname", "$db_login", "$db_password") or die(mysql_fatal());
	mysql_select_db("$db_dbname") or die(mysql_fatal());

	mysql_query("SET NAMES utf8_general_ci");
	mysql_query("SET CHARSET utf8");

	mysql_query("SET CHARACTER SET utf8");
	mysql_query("SET SESSION collation_connection = 'utf8_general_ci'");
?>

END;

	cms_mkfile("./mysql.php", $config_cont);

	$C_CONTENT = <<<END


<div id="installProgressBarContainer" style="display: none;">
	<div id="installProgressBarBox"><div id="installProgressBarNum">0%</div><div id="installProgressBarLine"></div></div>
	<div id="installProgressBarComment"></div>
</div>

<input type="button" onclick="javascript: document.getElementById('installProgressBarContainer').style.display = ''; this.style.display = 'none'; runNextIteration();" value="Начать распаковку" id="installButton" />



END;


	} else {
		$C_CONTENT = <<<END
<p><span class="c_false"><b>Введены неверные параметры доступа к базе данных.</b></span><br />
Если вы не знаете этих данных или не уверены в их корректности обратитесь к вашему системному администратору или к администратору вашего хостинга.</p>

<p><input type="button" value="<< Назад" onclick="javascript: history.back(1);" /></p>
END;
	}



}

if($step == 7) {
	$C_TITLE = "Шаг 7";
	$C_H1 = "Установка пароля для супервайзера";

	$C_CONTENT = <<<END

<p>На этом этапе нобходимо выбрать пароль для пользователя "Супервайзер".<br />
Этот пользователь имеет неограниченный доступ к системе и его нельзя удалить.</p>

<form method="post" onsubmit="javascript: return checkSvForm(this);" name="svForm">

<input type="hidden" name="s" value="8" />

<table border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td>
			Введите логин:
		</td>

		<td>
			<input type="text" name="svlogin" value="sv" class="text" style="width: 350px" />
		</td>
	</tr>

	<tr>
		<td>
			Введите пароль:
		</td>

		<td>
			<input type="password" name="svpass" class="text" style="width: 350px" />
		</td>
	</tr>

	<tr>
		<td>
			Подтвердите пароль:
		</td>

		<td>
			<input type="password" name="svpass_check" class="text" style="width: 350px" />
		</td>
	</tr>
</table>

<p><input type="submit" value="Продолжить >>"/></p>
</form>

END;
}

if($step == 8) {
	$C_TITLE = "Установка завершена";
	$C_H1 = "";

	$svlogin = $_REQUEST['svlogin'];
	$svpass = $_REQUEST['svpass'];

	include "config.php";

	if($svpass) {
		$object_id = 14;
		$object = umiObjectsCollection::getInstance()->getObject($object_id);
		$object->setName($svlogin);
		$object->setValue("login", $svlogin);
		$object->setValue("password", md5($svpass));
		$object->commit();
	}

	$crtime = time();

	$fol = $_SERVER['REQUEST_URI'];
	$fola = split("/", $fol);
	$sz = sizeof($fola) - 1;
	$fol = "";
	for($i = 0; $i < $sz; $i++)
		$fol .= $fola[$i] . "/";

	$current_dir = "http://" . $_SERVER['HTTP_HOST'] . $fol;
	$current_dir_admin = $current_dir . "admin/";

	$C_CONTENT = <<<END
<p>
Поздравляем! Установка UMI.CMS 2.0 завершена.<br />
<span style="color: red;"><b>Удалите файл "install.php"!</b></span>
</p>
<p>
<a href="$current_dir">Перейти на мой сайт</a><br />
<a href="$current_dir_admin">Администрирование моего сайта</a>
</p>
END;



	$htaccess_cont = <<<END
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^robots\.txt$  /sbots.php?path=$1%{QUERY_STRING} [L]

#Setting redirection rules for typical cms-mode
RewriteCond %{REQUEST_URI} !styles
RewriteCond %{REQUEST_URI} !css
RewriteCond %{REQUEST_URI} !^js
RewriteCond %{REQUEST_URI} !images
RewriteCond %{REQUEST_URI} !webstat
RewriteCond %{REQUEST_URI} !catalog_debug
RewriteCond %{REQUEST_FILENAME} !-f

RewriteRule ^([^\.]*)$ /index.php?path=$1&%{QUERY_STRING} [L]
#RewriteRule ^(.*)$ index.php?path=$1&%{QUERY_STRING} [L]
#RewriteRule ^(.*)$ index.html [L]


END;

	cms_mkfile("./.htaccess", $htaccess_cont);


	if(is_file(".htaccess")) {
		if(!is_writable(".htaccess")) {
			echo <<<END

<p>Файл .htaccess недоступен для перезаписи. Внесите вручную следующие строки:</p>
<pre>
RewriteEngine On
ErrorDocument 403 /errors/403/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^robots\.txt$  sbots.php?path=$1%{QUERY_STRING} [L]
RewriteCond %{REQUEST_URI} !styles
RewriteCond %{REQUEST_URI} !images
RewriteCond %{REQUEST_URI} !webstat
RewriteRule ^([^\.]*)$ index.php?path=$1&%{QUERY_STRING} [L]
</pre>

END;
		}
	}

	$sql = "SHOW TABLES";
	$result = mysql_query($sql);
	while(list($table_name) = mysql_fetch_row($result)) {
		mysql_query("OPTIMIZE TABLE `{$table_name}`");
	}

	touch("./installed");
}

error_reporting(~E_ALL);
?>
<html>
	<head>
		<title>Установка UMI.CMS 2.0 - <?php echo $C_TITLE; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<style type="text/css">
body, td, select {
	margin: 0px;
	background-color: #F7F7F7;
	font-family: Verdana;
	font-size: 11px;
}


h1 {
	font-family: Verdana, arial, helvetica, sans serif;
	font-weight: bold; font-size: 17px;
	color: #A7A7A7;
}

div.content {
	margin: 10px;
}

input {
	font-size: 11px;
	font-family: verdana;
	margin-top: 2px;
	margin-bottom: 2px;
	border: #C0C0C0 0.5pt solid;

	padding-left: 7px;
	padding-right: 7px;
	padding-bottom: 2px;
	height: 21px;
}

input.text {
	font-size: 11px;
	font-family: verdana;
	margin-top: 2px;
	margin-bottom: 2px;
	border: #C0C0C0 0.5pt solid;

	padding-left: 7px;
	padding-right: 7px;
	padding-bottom: 0px;
	height: 18px;
	width: 100%;
}

textarea.licence {
	width: 550px;
	height: 235px;
}

li {
	margin-top: 3px;
}

.c_true {
	color: green;
}

.c_false {
	color: red;
}

a {
	color: #008000;
	text-decoration: underline;
}

#log, #mods {
	margin-left: 15px;
}

#license_msg {
	color:		red;
	margin:		10px;
}


#installProgressBarBox {
	width:		400px;
	height:		14px;

	border:		#DDD 1px solid;
}

#installProgressBarLine {
	width:		0px;
	height:		14px;

	background-color:	darkblue;
}

#installProgressBarNum {
	position:	absolute;
	margin-left:	200px;
}

#installProgressBarContainer, #installButton {
	margin:		30px;
}
		</style>

	<script type="text/javascript">
var inc = 0;

function nextStep(step) {
	window.location = "?s=" + step;
}

function readTime() {
	tOut = 10;

	bObj = document.getElementById('agreeButton');

	if(inc < tOut) {
		bObj.disabled = true;
		bObj.value = "Я согласен (" + (tOut - inc) + " сек)";
	}

	if(inc == tOut) {
		bObj.value = "Я согласен >>";
		bObj.disabled = false;
		return true;
	}

	if(inc++ < tOut)
		setTimeout(readTime,1000);
}

function switchLog(dName) {
	if(!(lObj = document.getElementById(dName)))
		return false;
	if(lObj.style.display == '')
		lObj.style.display = 'none';
	else
		lObj.style.display = '';
	return false;
}

function checkSvForm(frm) {
	if(frm.svlogin.value.length == 0) {
		alert("Необходимо ввести логин");
		frm.svlogin.focus
		return false;
	}

	if(frm.svpass.value.length == 0) {
		alert("Необходимо ввести пароль");
		frm.svpass.focus();
		return false;
	}

	if(frm.svpass.value.length != frm.svpass_check.value.length) {
		alert("Пароли не совпадают.");
		frm.svpass_check.focus();
		return false;
	}
	return true;
}

function requestsController() {
	requestsController.self = this;
}

requestsController.prototype.requests = new Array();


requestsController.getSelf = function () {
	if(!requestsController.self) {
		requestsController.self = new requestsController();
	}
	return requestsController.self;
};



requestsController.prototype.sendRequest = function (url, handler, charset) {
	var requestId = this.requests.length;
	this.requests[requestId] = handler;

	var url = url;
	var scriptObj = document.createElement("script");
	scriptObj.src = url + "&requestId=" + requestId;
	
	if(charset) {
		scriptObj.charset = charset;
	}

	document.body.appendChild(scriptObj);
};

requestsController.prototype.reportRequest = function (requestId, args) {
	this.requests[requestId](args);
	this.requests[requestId] = undefined;
}


function checkLicenseCode(frm) {
	var keycodeInput = document.getElementById('keycode');
	var keycode = keycodeInput.value;

	var ip = "<?php echo $_SERVER['SERVER_ADDR']; ?>";
	var domain = "<?php echo $_SERVER['HTTP_HOST']; ?>";

	var url = "http://umi-cms-2.umi-cms.ru/updatesrv/initInstallation/?keycode=" + keycode + "&domain=" + domain + "&ip=" + ip;

	var handler = function (response) {
		if(response['status'] == "OK") {
			document.getElementById('license_msg').style.color = "green";

			var res = "Лицензия \"" + response['license_type'] + "\" активирована.<br />Владелец " + response['last_name'] + " " + response['first_name'] + " " + response['second_name'] + " (" + response['email'] + ")<br />";
			var frm = document.getElementById('license_form');
			frm.domain_keycode.value = response['domain_keycode'];
			frm.fname.value = response['first_name'];
			frm.mname.value = response['second_name'];
			frm.lname.value = response['last_name'];
			frm.email.value = response['email'];

			document.getElementById('licenseButton').value = "Продолжить >>";
//			document.getElementById('licenseButton').type = "submit";
			document.getElementById('licenseButton').onclick = function () {
				frm.submit();
			};

			frm.action = "?s=3";


			document.getElementById('license_msg').innerHTML = res;
		} else {
			document.getElementById('license_msg').innerHTML = "Ошибка: " + response['msg'];
		}
	};

	requestsController.getSelf().sendRequest(url, handler, "windows-1251");
}


function updateProgressBar(proc) {
	var obj = document.getElementById('installProgressBarNum');
	obj.innerHTML = proc + "%";

	if(proc >= 50) {
		obj.style.color = "#FFF";
	}


	var obj = document.getElementById('installProgressBarLine');
	obj.style.width = 400 * proc / 100;
}

function updateProgressComment(txt) {
	var obj = document.getElementById('installProgressBarComment');
	obj.innerHTML = "Устанавливается: " + txt;
}

var installPosition = 0;


function runNextIteration() {
	var url = "?action=initInstall&package=<?php echo $_REQUEST['package']; ?>&pos=" + installPosition + "&keycode=<?php echo $_REQUEST['keycode']; ?>&fname=<?php echo $_REQUEST['fname']; ?>&lname=<?php echo $_REQUEST['lname']; ?>&father_name=<?php echo $_REQUEST['father_name']; ?>&e-mail=<?php echo $_REQUEST['e-mail']; ?>";
	var handler = function (response) {
		updateProgressBar(response.proc);
		updateProgressComment(response.descr);

		if(response.status == 1) {
			window.location = "?s=7";
			return;
		}

		installPosition = response.pos;

		runNextIteration();
	};

	requestsController.getSelf().sendRequest(url, handler);

}

	</script>

	</head>
	<body>

<div>
<img src="images_install/cms/logo.gif" width="184" height="81" alt="UMI.CMS" /><img src="images_install/cms/wing.gif" width="90" height="81" alt="UMI.CMS" />
</div>
<table width="100%" cellspacing="0">
   <tr>
    <td colspan="5" height="3" style="background-image: url('images_install/cms/gray_line.gif')"></td>
   </tr>

   <tr>
    <td colspan="5" height="10" style="background-image: url('images_install/cms/top_line.gif')"></td>
   </tr>

   <tr>
    <td colspan="5" height="3" style="background-image: url('images_install/cms/gray_line.gif')"></td>
   </tr>

   <tr>
    <td colSpan="5" style="background-image: url('images_install/cms/gray_line.gif')" height="1"></td>
   </tr>
</table>

<div class="content">

<h1>Установка UMI.CMS 2.0 / <?php echo $C_TITLE; ?><?php if($C_H1) echo " - " . $C_H1; ?></h1>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td width="65%" valign="top">

			<?php echo $C_CONTENT; ?>

		</td>
		<td valign="top">
			<?php echo $C_STEPS; ?>
		</td>
	</tr>
</table>



	</body>
</html>