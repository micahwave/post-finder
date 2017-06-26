module.exports = {
	all: {
		files: {
			'assets/js/post-finder.min.js': ['assets/js/post-finder.js']
		},
		options: {
			banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
			' * <%= pkg.homepage %>\n' +
			' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
			
			' */\n',
			mangle: {
				except: ['jQuery']
			}
		}
	}
};
