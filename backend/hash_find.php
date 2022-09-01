<?php
	//Проверяем, пришёл ли хеш, не пуст ли он и не длиннее ли он 40 символов
	if(!isset($_POST['hash']) || empty($_POST['hash']) || strlen($_POST['hash']) > 40) exit(header('HTTP/1.1 400'));

	//Перезаписываем пришедший хеш
	$hash = $_POST['hash'];
	unset($_POST);

	//Адреса файлов хешей
	$file_results_link = __DIR__.'/hash_storage/results.txt';
	$file_inputs_link = __DIR__.'/hash_storage/inputs.txt';

	//Ищем вхождение по всему файлу
	if(strpos(file_get_contents($file_results_link), $hash) === FALSE) exit(json_encode(NULL));

	//Если искомая строка есть в файле, выясним её номер строки
	$line = 0; //Перебираемая циклом строка
	$searchable = NULL; //Найденная, отформатированная, исходная строка

	//Перебираем строки в файле результатов хеширования
	foreach(file($file_results_link) as $string) {
		//Если строка содержит вхождение искомой строки
	  if(strpos($string, $hash) !== FALSE) {
	  	//Записываем строку с тем же номером из файла исходных строк
	  	$searchable = file($file_inputs_link)[$line];
	  	//Удаляем перенос каретки и возможные теги
	  	$searchable = str_replace(PHP_EOL, '', htmlspecialchars($searchable));

	  	break;
	  }
	  $line++;
	}
	
	//Отправляем найденную исходную строку
	exit(json_encode($searchable ?: NULL));

?>