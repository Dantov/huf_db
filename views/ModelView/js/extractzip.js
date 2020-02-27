var butt3D = document.getElementById('butt3D');
	butt3D.onclick = function() {
		$$f({
			formid:'extractform',
			url: 'controllers/extractzip.php',
			onstart:function () {
			},
			onsend:function () {
			},
			error: function() {
				alert ("Ошибка отправки! Попробуйте снова.");
			}
		});
	}