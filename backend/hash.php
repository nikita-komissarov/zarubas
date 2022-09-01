<?php
	//Проверяем пришёл ли параметр 'text' методом POST
	if(!isset($_POST['text'])) exit(header('HTTP/1.1 400'));

	//Флаг "тип данных" (TRUE - бинарный файл / FALSE - строка)
	$is_file = FALSE;
	//Перезаписываем параметр 'text', он бывает пуст
	$input = $_POST['text'];
	unset($_POST);

	//Если вместе со строкой пришёл файл - он главнее.
	if(!empty($_FILES['file'])){
		//Меняем флаг "тип данных" на "файл"
		$is_file = TRUE;
		//Перезаписываем строку в $input этим файлом
		$input = file_get_contents($_FILES['file']['tmp_name']);
	}

	//Вычисляем хеши для содержимого $input
	$results = [
		'MD5' => md5($input),
		'SHA1' => sha1($input),
		'CRC32' => (string)crc32($input), //CRC32 иногда даёт целочисленный результат
	];

	//Если это не файл, сохраним входную строку для возможности "обратного хеширования"
	if(!$is_file){
		if(!file_exists(__DIR__.'/hash_storage/')){
			mkdir(__DIR__.'/hash_storage/', 0755);
		}
		//Получаем содержимое файла с исходными строками
		//Он обеспечивает сопоставление строк с результатами хеширований
		$file_inputs_link = __DIR__.'/hash_storage/inputs.txt';
		$file_inputs = file_get_contents($file_inputs_link);

		//Если в файле сопоставлений ещё нет исходной строки из $input
		if(strpos($file_inputs, $input) === FALSE) {
			//Получаем содержимое файла с результатами хеширований
			$file_results_link = __DIR__.'/hash_storage/results.txt';
			$file_results = file_get_contents($file_results_link);

			//Дописываем исходную строку в файл исходных строк
			$file_inputs .= $input.PHP_EOL;
			//Дописываем результаты хеширования в виде JSON в файл результатов
			$file_results .= json_encode($results).PHP_EOL;

			//Сохраняем файлы исходных строк и результатов
			file_put_contents($file_inputs_link, $file_inputs);
			file_put_contents($file_results_link, $file_results);
    }
	}

	//Возвращаем клиенту результаты хеширования
	exit(json_encode($results));
?>