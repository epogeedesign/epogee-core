tinymce.PluginManager.add('ep_imgix', function (editor, url) {
	editor.on('NodeChange', function(e) {
		if (e.element.nodeName == 'IMG') {
			const width = parseInt(editor.dom.getAttrib(e.element, 'width'));
			const height = parseInt(editor.dom.getAttrib(e.element, 'height'));

			const curWidth = parseInt(e.element.style.getPropertyValue('--img-w'));
			const curHeight = parseInt(e.element.style.getPropertyValue('--img-h'));

			if (width != curWidth || height != curHeight) {
				// Compute new Imgix url
				const src = e.element.src
					.replace(/(w=)[^\&]+/, '$1' + width)
					.replace(/(h=)[^\&]+/, '$1' + height);

				// Set new url
				editor.dom.setAttrib(e.element, 'src', src);

				// Set css vars, twice because of old jQuery
				e.element.style.setProperty('--img-w', width);
				editor.dom.setStyle(e.element, '--img-w', width);
				e.element.style.setProperty('--img-h', height);
				editor.dom.setStyle(e.element, '--img-h', height);
			}
		}
	});

	return {
		getMetadata: function () {
			return {
				name: 'MCE Imgix',
				url: 'https://epogeedesign.com'
			};
		}
	};
});
