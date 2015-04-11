'use strict';
module.exports = function(grunt) {
  // Load all tasks
  require('load-grunt-tasks')(grunt);
  // Show elapsed time
  require('time-grunt')(grunt);

  grunt.initConfig({
    sass: {
      options: {
        loadPath: [
          'bower_components/bourbon/dist',
          'bower_components/foundation/scss'
        ]
      },
      dev: {
        options: {
          style: 'expanded'
        },
        files: {
          'assets/css/acf-fancy-repeater-field-v5.css': 'assets/scss/acf-fancy-repeater-field-v5.scss'
        }
      },
      build: {
        options: {
          style: 'compressed'
        },
        files: {
          'assets/css/acf-fancy-repeater-field-v5.min.css': 'assets/scss/acf-fancy-repeater-field-v5.scss'
        }
      }
    },
    autoprefixer: {
      options: {
        browsers: ['last 2 versions', 'ie 8', 'ie 9', 'android 2.3', 'android 4', 'opera 12']
      },
      dev: {
        options: {
          map: {
            prev: 'assets/css/'
          }
        },
        src: 'assets/css/acf-fancy-repeater-field-v5.css'
      },
      build: {
        src: 'assets/css/acf-fancy-repeater-field-v5.min.css'
      }
    },
    watch: {
      sass: {
        files: [
          'assets/scss/*.scss',
          'assets/scss/**/*.scss'
        ],
        tasks: ['sass:dev', 'autoprefixer:dev']
      }
    }
  });

  // Register tasks
  grunt.registerTask('default', [
    'dev'
  ]);
  grunt.registerTask('dev', [
    'sass:dev',
    'autoprefixer:dev'
  ]);
  grunt.registerTask('build', [
    'sass:dev',
    'autoprefixer:dev',
    'sass:build',
    'autoprefixer:build'
  ]);
};
