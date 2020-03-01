var butt3D = document.getElementById('butt3D');
	butt3D.onclick = function() {
		var extractform = document.getElementById('extractform');
		let formData = new FormData(extractform);
		$.ajax({
			url: "controllers/extractzip.php",
			type: "POST",
			data: {
				formData
			},
			dataType:"json",
			success:function(data) {}
		})
	}