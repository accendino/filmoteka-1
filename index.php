<?php 

// СОЕДИНЯЕМСЯ С БД
$link = mysqli_connect('localhost', 'root', '', 'filmoteka');

//делаем проверку на ошибки и прекращаем работу программы если true 
if (mysqli_connect_error()) {
	die("Ошибка подключения к базе данных.");
}


//СОХРАНЯЕМ ДАННЫЕ ИЗ ФОРМЫ В БД
$resultSuccess = "";  //создаем переменную для результата запроса mysqli_query($link, $query) 
$resultError = "";  //создаем переменную для результата запроса mysqli_query($link, $query) 
$errors = array(); //создаем массив для ошибок, если будут

if (array_key_exists('add-film', $_POST)) { //если присутствует указанный ключ в массиве $_POST(значит именно форма "добавить фильм" была отправлена), то выполняем следующее..

	//проверяем поля на заполненность, если пустые, формируем массив с ошибками и вывести его, если ошибок нет - будем сохранять данные в БД
	//обработка ошибок, если поля не заполнены - записываем в массив с ошибками
	if ($_POST['title'] == '') {
		$errors[] = "<p>Введите название фильма.</p>";
	}
	if ($_POST['genre'] == '') {
		$errors[] = "<p>Введите жанр фильма.</p>";
	}
	if ($_POST['year'] == '') {
		$errors[] = "<p>Введите год фильма.</p>";
	}
	// Если ошщибок нет - сохраняем фильм
	if ( empty($errors) ) {
	//запись в БД
	// формируем запрос, к-й отправляет полученные данные в БД
	//$query = "INSERT INTO films ('title', 'genre', 'year')" VALUES ($_POST['title']); эта запись ненадежна по причине mysqli-инъекций, поэтому обрабатываем функцией mysqli_real_escape_string, которая экранирует все опасные символы:
		$query = "INSERT INTO films (title, genre, year) VALUES (
			'". mysqli_real_escape_string($link, $_POST['title']) ."', 
			'". mysqli_real_escape_string($link, $_POST['genre']) ."',
			'". mysqli_real_escape_string($link, $_POST['year']) ."'
			)"; 
	

		if (mysqli_query($link, $query)) { //выполняем запрос и проверяем, был ли запрос успешно выполнен (должно вернуть true)
			$resultSuccess = "<p>Фильм добавлен!</p>";
		} else {
			$resultError = "<p>Что-то пошло не так.</p>";
		}

	}

}

//проверяем
//echo "<pre>";
//print_r($_POST);
//echo "</pre>";


// ПОЛУЧАЕМ ФИЛЬМЫ ИЗ БД
//подготовили запрос
$query = "SELECT * FROM films"; 


//создаем пустой массив куда будем записывать полученные данные, наша таблица
$films = array();


//выполняем этот запрос с помощью функции mysql_query() и записываем результат в переменную $result
$result = mysqli_query($link, $query); //если результат положительный, то формируем массив с фильмами


//выполняем ф-ю mysqli_fetch_array(result) - при каждом её вызове получает новую строчку из запроса к базе данных и возвращает её. если функция mysqli_query($link, $query) выполняется успешно и результат записывается в звпрос, то 
if ($result = mysqli_query($link, $query)) {  
	while ($row = mysqli_fetch_array($result)) {   //то разбираем результат и каждый следующий новый ряд из таблицы записываем в переменную $row
		$films[] = $row; //формируем наш массив films, 'наполняем' полученными данными добавляем каждый новый полученный ряд в массив films
	}
}


//проверяем результат
//echo "<pre>";
//print_r($films);
//echo "</pre>";


?>

<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="UTF-8"/>
		<title>UI-kit и HTML фреймворк - Документация</title>
		<!--[if IE]>
			<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<![endif]-->
		<meta name="viewport" content="width=device-width,initial-scale=1"/>
		<meta name="keywords" content=""/>
		<meta name="description" content=""/><!-- build:cssVendor css/vendor.css -->
		<link rel="stylesheet" href="libs/normalize-css/normalize.css"/>
		<link rel="stylesheet" href="libs/bootstrap-4-grid/grid.min.css"/>
		<link rel="stylesheet" href="libs/jquery-custom-scrollbar/jquery.custom-scrollbar.css"/><!-- endbuild -->
		<!-- build:cssCustom css/main.css -->
		<link rel="stylesheet" href="./css/main.css"/><!-- endbuild -->
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800&amp;subset=cyrillic-ext" rel="stylesheet">
		<!--[if lt IE 9]>
		<script src="http://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.min.js"></script><![endif]-->
	</head>
	<body>
		<div class="container user-content pt-35">


		<!-- если $resultSuccess не равен пустой строке, то выводим сообщение о резульате -->
		<?php if ( $resultSuccess != '' ) { ?> 
			<div class="info-success"><?=$resultSuccess?></div>
				<?php } ?>

		<!-- если $resultError не равен пустой строке, то выводим сообщение о резульате -->
		<?php if ( $resultError != '' ) { ?> 
			<div class="error"><?=$resultError?></div>
			   <?php } ?>


		<h1 class="title-1"> Фильмотека</h1>

		<!-- цикл, который обходит $films и по очереди выводит все данные -->
		<?php
			foreach ($films as $key => $film) {
		?>


		<!-- в карточке с фильмом выводим инфо офильме ИЗ массива -->
		<div class="card mb-20">
			<h4 class="title-4"><?php echo @$film['title']?></h4> <!-- < ? @$films[0]['title']?> сокращенная запись(!) --> <!-- @ гасит ошибки --> 
			<div class="badge"><?=@$film['genre']?></div>
			<div class="badge"><?=@$film['year']?></div>
		</div>
		<?php } ?>
		<!--  <div class="card mb-20">
			<h4 class="title-4">Облачный атлас</h4>
			<div class="badge">драма</div>
			<div class="badge">2012</div>
		</div> -->

		<div class="panel-holder mt-80 mb-100"> <!-- mb-40 -->
			<div class="title-4 mt-0">Добавить фильм</div>
			<form action="index.php" method="POST">

				<!-- делаем проверку - если массив с ошибками НЕ пустой(!) то будем выводить каждую ошибку через .errors -->
				<?php 

					if ( !empty($errors)) {
						foreach ($errors as $key => $value) {
						echo "<div class='error'>$value</div>";
						}
					}
				?>
				
				<!-- <div class="error">Название фильма не может быть пустым.</div> -->
				<label class="label-title">Название фильма</label>
				<input class="input" type="text" placeholder="Такси 2" name="title"/>
				<div class="row">
					<div class="col">
						<label class="label-title">Жанр</label>
						<input class="input" type="text" placeholder="комедия" name="genre"/>
					</div>
					<div class="col">
						<label class="label-title">Год</label>
						<input class="input" type="text" placeholder="2000" name="year"/>
					</div>
				</div>
				<input type="submit" class="button" value="Добавить" name="add-film">
			</form>
		  </div>
		</div>
		<!-- build:jsLibs js/libs.js -->
		<script src="libs/jquery/jquery.min.js"></script><!-- endbuild -->
		<!-- build:jsVendor js/vendor.js -->
		<script src="libs/jquery-custom-scrollbar/jquery.custom-scrollbar.js"></script><!-- endbuild -->
		<!-- build:jsMain js/main.js -->
		<script src="js/main.js"></script><!-- endbuild -->
		<script defer="defer" src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
	</body>
</html>