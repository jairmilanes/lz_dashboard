module.exports  = function(grunt){

    var plugin_name = 'lz_dashboard',
        base_dir = '',
        build_dir = '../../../../../PACKAGES/PLUGINS/'+plugin_name,
        build_zip_dir = '../../../../../PACKAGES/PLUGINS/ZIPS/'+plugin_name;

        // configure the tasks
        grunt.initConfig({
            clean: {
                src: {
                    src: [ build_dir ]
                },
                build: {
                    src: [ build_dir+'/node_modules' ]
                }
            },
            copy: {
                src: {
                    cwd: '',
                    src: [ '**', '!node_modules' ],
                    dest: build_dir,
                    expand: true
                }
            },
            cssmin: {
                build: {
                    files: [{
                        cwd: build_dir+'/application/assets/css',
                        src: ['*.css', '!*.min.css'],
                        dest: build_dir+'/application/assets/css',
                        expand: true
                    }]
                }
            },
            uglify: {
                build: {
                    options: {
                        mangle: false
                    },
                    files: [{
                        cwd: build_dir+'/application/assets/js',
                        src: ['*.js', '!*.min.js'],
                        dest: build_dir+'/application/assets/js',
                        expand: true
                    }]
                }
            }
        });

    require('load-grunt-tasks')(grunt);

    grunt.registerTask(
        'build',
        'Compiles all of the assets and copies the files to the build directory.',
        [ 'clean:src', 'copy', 'cssmin', 'uglify', 'clean:build' ]
    );
};