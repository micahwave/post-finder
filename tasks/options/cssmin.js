module.exports = {
	options: {
		banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
		' * <%=pkg.homepage %>\n' +
		' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
		
		' */\n'
	},
	minify: {
		expand: true,

		cwd: 'assets/css/',
		src: ['post-finder.css'],

		dest: 'assets/css/',
		ext: '.min.css'
	}
};
