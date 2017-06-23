module.exports = {
	options: {
		stripBanners: true,
			banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
		' * <%= pkg.homepage %>\n' +
		' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
		
		' */\n'
	},
	main: {
		src: [
			'assets/js/src/post-finder.js'
		],
		dest: 'assets/js/post-finder.js'
	}
};
