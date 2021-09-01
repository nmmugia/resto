module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify: {
            min: {
                files: [{
                    expand: true,
                    cwd: 'assets/js/app',
                    src: '**/*.js',
                    dest: 'assets/js/app_minify'
                }]
            },
            minLib: {
               /* options: {
                    mangle: false
                },*/
                files: {
                    // 'assets/js/libs/tree.jquery.min.js': ['assets/js/libs/tree.jquery.js'],
                    // 'assets/js/libs/jquery.flexslider.min.js': ['assets/js/libs/jquery.flexslider.js'],
                    'assets/js/plugins/virtual-keyboard/jquery.keyboard.js': ['assets/js/plugins/virtual-keyboard/jquery.keyboard.js'],
                    'assets/js/plugins/easyautocomplete/jquery.easy-autocomplete.js': ['assets/js/plugins/easyautocomplete/jquery.easy-autocomplete.js'],
                    // 'assets/js/libs/jquery.stepper.min.js': ['assets/js/libs/jquery.stepper.js'],
                    // 'assets/js/libs/navgoco/jquery.navgoco.min.js': ['assets/js/libs/navgoco/jquery.navgoco.js'],
                    // 'assets/js/libs/select2/js/select2.min.js': ['assets/js/libs/select2/js/select2.js']
                    // //'assets/js/libs/ckeditor/ckeditor.min.js': ['assets/js/libs/ckeditor/ckeditor.js']
                }
            }
        },
        watch: {
            js: {
                files: 'assets/js/app/*.js',
                tasks: ['uglify:min'],
                options: {
                    atBegin: true,
                    event: ['added', 'deleted', 'changed']
                }
            }
        },
        cssmin: {
          target: {
            files: [{
              expand: true,
              cwd: 'assets/css',
              src: ['*.css', '!*.min.css'],
              dest: 'assets/css_min',
              ext: '.css'
            }]
          }
        }
    });

    // Load the plugin that provides the "uglify" task.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');

    // Default task(s).
    grunt.registerTask('default', ['uglify:min','uglify:minLib']);
};