Upload-class
============

		$upload = new Upload($_FILES);

		$upload->load();
		$upload->scaleImage(160,160);
		$upload->save('newName' .'_t');

		$upload->cropImage(160,160);
		$upload->save('newName' .'_b');
