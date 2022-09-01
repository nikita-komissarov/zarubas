// Кнопка "выбрать файл"
$('#btn-file-input').on('click', function(){
	$('#file-input').trigger('click');
});

// Скрытое поле ввода файлов, событие изменения
$('#file-input').on('change', function(){
	//Если в поле ввода есть файлы
	if($(this).prop('files').length != 0){
		//Меняем текст в кнопке выбора файла, чтобы уведомить пользователя об успешном выборе
		$('#btn-file-input').text($(this).prop('files')[0].name);
		//Запрещаем ввод и очищаем строку ввода текста,
		//так как файл обрабатывается главнее на серверной стороне
		$('#text-input').attr('disabled', true).val('');
	}
	//Если в поле ввода нет файлов
	else {
		//Возвращаем исходное состояние кнопке выбора файла
		$('#btn-file-input').text('Выбрать файл');
		//Активируем поле ввода текста
		$('#text-input').attr('disabled', false);
	}
});

// Форма
$('#form-input').submit(function(event) {
	event.preventDefault();
	
	let form = $(this), //Перезаписываем эту форму для упрощения и краткости
			formData = new FormData(); //Создаём пустой объект формы 

	//Меняем текст в кнопке отправки формы
	$(form).find('button[type="submit"]').html('Загрузка...');
	
	//Если поле ввода файла не пусто
	if($('#file-input').prop('files').length){
		//Добавляем файл к объекту формы
		formData.append('file', $('#file-input').prop('files')[0]);
	}
	//Добавляем текст к объекту формы
	formData.append('text', $('#text-input').val());

	//Выводим содержимое объекта формы в консоль перед отправкой
	console.table(Object.fromEntries(formData));

	$.ajax({
		url: '/backend/hash.php',
		type: 'POST',
		dataType: 'json',
		processData: false,
    contentType: false,
		data: formData,
	})
	.done(function(data) {
		//Выводим результат хеширования в консоль
		console.table(data);
		//Ищем тело таблицы результатов
		let tbody = $('#table-results').find('tbody');
		//Очищаем тело таблицы результатов от старых данных
		$(tbody).html('');
		//Перебираем пришедшие результаты от сервера
		$.each(data, function(type, val) {
			//Дописываем каждый результат в конец таблицы по отдельным строкам
			$(tbody).append(`
				<tr>
					<td>` + type + `</td>
					<td>` + val + `</td>
				</tr>
			`);
		});
	})
	.fail(function() {
		alert('Что-то пошло не так, попробуйте ещё раз');
	})
	.always(function() {
		//Всегда возвращаем кнопку отправки формы к первоначальному состоянию
		$(form).find('button[type="submit"]').html('Вычислить хеш');
	});
});

$('#btn-hash-find').on('click', function(){
	let result = prompt('Введите искомый хеш');
	if(!result.length) return alert('Укажите хеш, исходную строку которого Вы ищите')
	$.ajax({
		url: '/backend/hash_find.php',
		type: 'POST',
		dataType: 'json',
		data: {
			hash: result,
		},
	})
	.done(function(data) {
		if(!data.length) return alert('Ничего не нашлось :(');
		alert(data);
	})
	.fail(function() {
		alert('Что-то пошло не так, попробуйте ещё раз');
	});
})