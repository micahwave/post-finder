module.exports = {
	main: {
		options: {
			mode: 'zip',
			archive: './release/post-finder.<%= pkg.version %>.zip'
		},
		expand: true,
		cwd: 'release/<%= pkg.version %>/',
		src: ['**/*'],
		dest: 'post-finder/'
	}
};