# Documentation

Welcome to the SPARXSTAR WordPress Plugin Starter documentation.

## Quick Links

- **[Build & Development Guide](BUILD.md)** - Comprehensive guide for building and developing
- **[Tooling Configuration](TOOLING.md)** - Detailed tooling setup and customization
- **[Local Development](LOCAL_DEVELOPMENT.md)** - Setup your local development environment
- **[First Contribution](FIRST_CONTRIBUTION.md)** - Guide for first-time contributors

## Supported Stack

- **WordPress**: 6.8+ (tested up to 6.9)
- **PHP**: 8.2, 8.3, 8.4
- **Node.js**: 20 LTS or higher
- **Composer**: 2.x
- **Browsers**: Modern evergreen browsers (Chrome, Firefox, Safari, Edge)

## Quick Start

### 1. Clone and Setup

```bash
git clone https://github.com/Starisian-Technologies/sparxstar-gluon.git your-plugin-name
cd your-plugin-name
composer install
npm install
```

### 2. Customize Plugin

Update these files with your plugin information:

- `sparxstar-plugin-entry.php` - Plugin header, namespace, constants
- `package.json` - Name, description, repository
- `composer.json` - Name, description, namespace
- `phpcs.xml.dist` - Text domain, prefix
- Replace "sparxstar" throughout the codebase with your plugin slug

### 3. Build Assets

```bash
npm run build
```

### 4. Develop

```bash
# Watch for changes (if you set up watch scripts)
npm run watch

# Or manually build after changes
npm run build
```

## Architecture Overview

```
┌─────────────────────────────────────────────────────────┐
│                    Plugin Entry Point                    │
│              sparxstar-plugin-entry.php                  │
└─────────────────────┬───────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────┐
│                    PSR-4 Autoloader                      │
│              src/includes/Autoloader.php                 │
└─────────────────────┬───────────────────────────────────┘
                      │
        ┌─────────────┼─────────────┐
        ▼             ▼              ▼
    ┌──────┐    ┌─────────┐    ┌──────────┐
    │ Core │    │ Helpers │    │Integration│
    └──────┘    └─────────┘    └──────────┘
```

### Directory Structure

```
src/
├── core/           # Core plugin functionality
├── helpers/        # Helper classes and utilities
├── includes/       # Autoloader and includes
├── integrations/   # Third-party integrations
├── templates/      # Template files
├── js/            # JavaScript source files
└── css/           # CSS source files

assets/
├── js/            # Minified JavaScript (generated)
└── css/           # Minified CSS (generated)

tests/
├── phpunit/       # PHP unit tests
└── e2e/           # End-to-end tests

.github/workflows/ # CI/CD workflows
docs/              # Documentation
```

## Development Workflow

### Daily Development

1. **Write code** in `src/` directories
2. **Run linters** to check code quality
3. **Run tests** to verify functionality
4. **Build assets** before committing
5. **Commit** with conventional commits

### Before Committing

```bash
# Lint everything
npm run lint
composer run lint:php

# Run tests
npm test
composer run test:php

# Build assets
npm run build

# Commit (husky will run pre-commit hooks)
git commit -m "feat: add new feature"
```

## Standards & Best Practices

### PHP Standards

- **PSR-4**: Autoloading
- **PSR-12**: Coding style (where not conflicting with WordPress)
- **WordPress VIP**: Primary standards (takes precedence)
- **Type declarations**: Use PHP 8.2+ features

### JavaScript Standards

- **ES2021**: Modern JavaScript features
- **ESLint**: Linting and code quality
- **Prettier**: Code formatting
- **JSDoc**: Documentation comments

### CSS Standards

- **Modern CSS**: Use CSS custom properties
- **BEM**: Block Element Modifier methodology (recommended)
- **Stylelint**: Linting and code quality
- **Mobile-first**: Responsive design approach

## Testing Strategy

### PHP Testing (PHPUnit)

```bash
composer run test:php
```

Test files: `tests/phpunit/*Test.php`

### JavaScript Testing (Jest)

```bash
npm test
```

Test files: `src/js/__tests__/*.test.js`

### E2E Testing (Playwright)

```bash
npm run test:e2e
```

Test files: `tests/e2e/*.spec.js`

## Build Process

### JavaScript Build

1. Source files: `src/js/**/*.js`
2. Process: Minify with Terser
3. Output: `assets/js/**/*.min.js`
4. Source maps: Generated

### CSS Build

1. Source files: `src/css/**/*.css`
2. Process: Minify with clean-css
3. Output: `assets/css/**/*.min.css`

## Deployment

### Automated (Recommended)

Push a version tag:

```bash
git tag -a v1.2.3 -m "Release 1.2.3"
git push origin v1.2.3
```

GitHub Actions will:

- Build assets
- Update versions
- Create release ZIP
- Generate checksums
- Create GitHub release

### Manual

See [BUILD.md](BUILD.md) for manual deployment steps.

## Troubleshooting

### Common Issues

**Problem**: Build fails with "command not found"  
**Solution**: Run `npm install` to install dependencies

**Problem**: PHPStan errors after update  
**Solution**: Regenerate baseline: `./vendor/bin/phpstan analyse --generate-baseline`

**Problem**: Tests fail in CI but pass locally  
**Solution**: Check PHP version compatibility (CI tests PHP 8.2, 8.3, 8.4)

### Getting Help

- Check [BUILD.md](BUILD.md) for detailed build instructions
- Check [TOOLING.md](TOOLING.md) for tool configuration
- Open an issue on GitHub
- Contact: support@starisian.com

## Additional Resources

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [WordPress VIP Standards](https://docs.wpvip.com/technical-references/)
- [PSR Standards](https://www.php-fig.org/psr/)
- [Modern PHP](https://modernphp.com/)

## When to Fork vs. Extend

### Fork This Repository When:

- Creating a new standalone plugin
- Need complete customization
- Starting a new project from scratch

### Use as Template When:

- Creating multiple similar plugins
- Want to keep the structure but customize everything
- Building client projects

### Extend When:

- Building features that might be contributed back
- Want to receive upstream updates
- Creating a plugin ecosystem

## Contributing

See [CONTRIBUTING.md](../CONTRIBUTING.md) for contribution guidelines.

First-time contributors should read [FIRST_CONTRIBUTION.md](FIRST_CONTRIBUTION.md).

## License

This template is licensed under the MIT License. See [LICENSE.md](../LICENSE.md).

Projects built with this template can use any license.
