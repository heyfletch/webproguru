module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    sass: {
      options: {
        includePaths: ['bower_components/foundation/scss']
      },
      dist: {
        options: {
          outputStyle: 'compressed'
        },
        files: {
          'css/app.css': 'scss/app.scss'
        }        
      }
    },

    autoprefixer: {
      options: {
        browsers: ['last 2 versions', 'ie 8', 'ie 9', 'android 2.3', 'android 4', 'opera 12'],
        // map: 'assets/css/'
      },
      your_target: {
        src: 'css/app.css'
      },
    },

    copy: {
      scripts: {
        expand: true,
        cwd: 'bower_components/',
        src: '**/*.js',
        dest: 'js'
      },

      maps: {
        expand: true,
        cwd: 'bower_components/',
        src: '**/*.map',
        dest: 'js'
      },
    },

    uglify: {
      dist: {
        files: {
          'js/modernizr/modernizr.min.js': ['js/modernizr/modernizr.js']
        }
      }
    },

    concat: {
      options: {
        separator: ';',
      },
      dist: {
        src: [
          'js/foundation/js/foundation.min.js',
          'js/init-foundation.js'
        ],

        dest: 'js/app.js',
      },

    },

    watch: {

      sass: {
        files: [
          'scss/app.scss'
        ],
        tasks: [
          'sass', 
          'autoprefixer'
          ]
      },

      js: {
        files: [

        ],
        tasks: [
        
        ]
      },
      
      php: { files: ['**/*.php'] },

      options: {
        livereload: true,
      }
    }
  });

  grunt.loadNpmTasks('grunt-sass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-autoprefixer');

  // grunt.registerTask('build', ['sass', 'autoprefixer']);
  // grunt.registerTask('default', ['copy', 'uglify', 'concat', 'watch']);



  // Register tasks
  grunt.registerTask('default', [
    'copy',
    'uglify',
    'concat',
    'sass',
    'autoprefixer',
    // 'version'
  ]);

  grunt.registerTask('dev', [
    'watch'
  ]);







};