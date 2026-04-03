# Build & Development Guide

This document explains how to build, develop, and maintain this WordPress plugin.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Development Workflow](#development-workflow)
- [Building Assets](#building-assets)
- [Testing](#testing)
- [Code Quality](#code-quality)
- [Release Process](#release-process)

## Prerequisites

- PHP 8.2 or higher
- Node.js 20 or higher
- Composer 2.x
- npm or yarn

## Installation

1. Clone the repository:

```bash
git clone https://github.com/Starisian-Technologies/sparxstar-gluon.git
cd sparxstar-gluon
```

2. Install PHP dependencies:

```bash
composer install
```

3. Install Node dependencies:

```bash
npm install
```

4. Install Playwright browsers (for E2E testing):

```bash
npx playwright install
```

## Development Workflow

### Directory Structure

```
src/
├── js/          # Source JavaScript files
├── css/         # Source CSS files
├── core/        # Core PHP classes
├── helpers/     # Helper PHP classes
├── includes/    # Autoloader and includes
├── integrations/# Third-party integrations
└── templates/   # Template files

assets/
├── js/          # Minified JavaScript (generated)
└── css/         # Minified CSS (generated)
```

### Development Commands

```bash
# Lint all code
npm run lint
composer run lint:php

# Fix auto-fixable issues
npm run format
composer run fix:php

# Run tests
npm test
composer run test:php

# Build assets for production
npm run build

# Build JS only
npm run build:js

# Build CSS only
npm run build:css

# Generate translation files
npm run makepot

# Run Rector refactoring (dry-run)
composer run refactor:php

# Apply Rector refactoring
composer run refactor:php:fix
```

## Building Assets

### JavaScript

JavaScript files are minified using [Terser](https://terser.org/):

```bash
npm run build:js
```

This will:

1. Read all `.js` files from `src/js/`
2. Minify them using Terser
3. Output to `assets/js/` as `.min.js` files
4. Generate source maps

### CSS

CSS files are minified using [clean-css](https://github.com/jakubpawlowicz/clean-css):

```bash
npm run build:css
```

This will:

1. Read all `.css` files from `src/css/`
2. Minify them using clean-css
3. Output to `assets/css/` as `.min.css` files

### Full Build

Run both JS and CSS builds:

```bash
npm run build
```

## Testing

### PHP Unit Tests

```bash
# Run all PHPUnit tests
composer run test:php

# Run specific test file
./vendor/bin/phpunit tests/phpunit/ExampleTest.php
```

### JavaScript Unit Tests

```bash
# Run all Jest tests
npm test

# Run in watch mode
npm test -- --watch

# Generate coverage report
npm test -- --coverage
```

### E2E Tests (Playwright)

```bash
# Run all E2E tests
npm run test:e2e

# Run specific browser
npx playwright test --project=chromium

# Run in headed mode (see browser)
npx playwright test --headed

# Debug mode
npx playwright test --debug
```

### Puppeteer Tests

Puppeteer is available for custom browser automation. See `tests/e2e/` for examples.

## Code Quality

### PHP

#### PHPCS (Code Sniffer)

Checks code against WordPress coding standards:

```bash
composer run lint:php
```

Fix auto-fixable issues:

```bash
composer run fix:php
```

Configuration: [phpcs.xml.dist](../phpcs.xml.dist)

#### PHPStan (Static Analysis)

Analyzes code for type errors and bugs:

```bash
composer run analyze:php
```

Configuration: [phpstan.neon.dist](../phpstan.neon.dist)

#### Rector (Refactoring)

Modernizes PHP code and applies best practices:

```bash
# Dry run (preview changes)
composer run refactor:php

# Apply changes
composer run refactor:php:fix
```

Configuration: [rector.php](../rector.php)

### JavaScript

#### ESLint

Lints JavaScript code:

```bash
npm run lint:js
```

Configuration: [eslint.config.js](../eslint.config.js)

### CSS

#### Stylelint

Lints CSS code:

```bash
npm run lint:css
```

Configuration: [.stylelintrc.json](../.stylelintrc.json)

## Internationalization (i18n)

Generate POT file for translations:

```bash
npm run makepot
```

This creates/updates `languages/plugin-textdomain.pot` with all translatable strings.

## Release Process

### Automated Release (Recommended)

1. Update [CHANGELOG.md](../CHANGELOG.md) with changes for the new version
2. Commit all changes
3. Create and push a version tag:

```bash
git tag -a v1.2.3 -m "Release version 1.2.3"
git push origin v1.2.3
```

The GitHub Actions workflow will automatically:

- Update version numbers in all files
- Install dependencies
- Build and minify assets
- Generate translation files
- Create distribution zip
- Generate checksums (MD5, SHA256)
- Create GitHub release with artifacts

### Manual Release

1. Update version in:
    - `sparxstar-plugin-entry.php` (plugin header, @version, GLUON_PLUGIN_VERSION)
    - `package.json`
    - `composer.json` (if version field exists)

2. Build assets:

```bash
npm run build
```

3. Generate POT file:

```bash
npm run makepot
```

4. Create distribution:

```bash
# Install production dependencies only
composer install --no-dev --optimize-autoloader

# Create zip excluding dev files
# Use .distignore to exclude files
```

## CI/CD Workflows

### Continuous Integration

Runs on every push and pull request:

- **Lint PHP**: PHPCS, PHPStan, Rector checks
- **Lint Frontend**: ESLint, Stylelint
- **Test PHP**: PHPUnit on PHP 8.2, 8.3, 8.4
- **Test JavaScript**: Jest unit tests
- **Build Assets**: Verify JS/CSS build process

### Code Quality

- **HTML Validation**: Validates HTML templates
- **CSS Validation**: Checks CSS quality and browser compatibility
- **JS Validation**: Analyzes JavaScript complexity

### Security

Weekly security scans:

- Dependency audits (Composer & npm)
- CodeQL analysis
- Secret scanning
- Security best practices checks

### Accessibility

- Automated accessibility testing with axe-core
- HTML validation
- WCAG 2.1 compliance checks

## Troubleshooting

### Build Issues

If builds fail, try:

```bash
# Clear node_modules and reinstall
rm -rf node_modules package-lock.json
npm install

# Clear composer cache
composer clear-cache
composer install
```

### Test Issues

```bash
# Reinstall Playwright browsers
npx playwright install --force

# Clear Jest cache
npm test -- --clearCache
```

### Linting Issues

```bash
# Auto-fix what's possible
npm run format
composer run fix:php

# Check what can't be auto-fixed
npm run lint
composer run lint:php
```

## Additional Resources

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [WordPress VIP Standards](https://docs.wpvip.com/technical-references/code-quality-and-best-practices/)
- [PSR Standards](https://www.php-fig.org/psr/)

## Support

For issues and questions:

- Open an issue on GitHub
- Review existing documentation in `docs/`
- Contact: support@starisian.com
