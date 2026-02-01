/** @type {import('tailwindcss').Config} */

module.exports = {
    content: [
        // Laravel Blade templates
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',

        // Filament specific paths
        './app/Filament/**/*.php',
        './app/Livewire/**/*.php',
        './app/Http/Livewire/**/*.php',

        // Modules (if using modular structure)
        './Modules/**/app/**/*.php',
        './Modules/**/resources/**/*.blade.php',
        './Modules/**/resources/**/*.js',
        './Modules/Units/Auth/My/resources/views/filament/layout/auth.blade.php',
        './Modules/Units/Chat/Common/resources/views/livewire/chat-component.blade.php',
        // Vendor packages
        './vendor/filament/**/*.blade.php',
        './vendor/codewithdennis/filament-simple-alert/resources/**/*.blade.php',
        './vendor/livewire/**/*.blade.php',

        // Additional paths for any custom components
        './app/View/**/*.php',
        './app/Components/**/*.php',

        // Stubs and templates
        './stubs/**/*.blade.php',
        './stubs/**/*.php',

        // Filament panel-specific views
        './app/Providers/Filament/**/*.php',
        './Modules/Units/**/Filament/**/*.php',
        './Modules/Units/**/Resources/**/*.php',

        // Livewire components
        './app/Livewire/**/*.php',
        './Modules/**/Livewire/**/*.php',

        // Blade components
        './app/View/Components/**/*.php',
        './Modules/**/View/Components/**/*.php',
        './vendor/guava/filament-knowledge-base/src/**/*.php',
        './vendor/guava/filament-knowledge-base/resources/**/*.blade.php',
        './Modules/Units/**/Common/resources/views/livewire/*',
    ],
    theme: {
      colors: {
        'blue': '#1fb6ff',
        'purple': '#7e5bef',
        'pink': '#ff49db',
        'orange': '#ff7849',
        'green': '#13ce66',
        'yellow': '#ffc82c',
        'gray-dark': '#273444',
        'gray': '#8492a6',
        'gray-light': '#d3dce6',
      },
      fontFamily: {
        sans: ['Graphik', 'sans-serif'],
        serif: ['Merriweather', 'serif'],
        shabnam: ['Shabnam'],
      },
      extend: {
        spacing: {
          '8xl': '96rem',
          '9xl': '128rem',
        },
        borderRadius: {
          '4xl': '2rem',
          '2xl': '2rem',
        }
      }
    },
    // Enable all Tailwind features
    corePlugins: {
        preflight: true,
    },
    // Ensure all variants are available
    variants: {
        extend: {
            opacity: ['disabled'],
            cursor: ['disabled'],
            backgroundColor: ['disabled', 'hover', 'focus'],
            textColor: ['disabled', 'hover', 'focus'],
            borderColor: ['disabled', 'hover', 'focus'],
        },
    },
}
