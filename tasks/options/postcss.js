module.exports = {
	dist: {
		options: {
			processors: [
				require('autoprefixer')({browsers: 'last 2 versions'})
			]
		},
		files: { 
			'assets/css/post-finder.css': [ 'assets/css/post-finder.css' ]
		}
	}
};