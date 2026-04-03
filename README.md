<img width="1280" height="640" alt="Untitled-7 (2)" src="https://github.com/user-attachments/assets/dfcadedb-c950-4767-889a-cde70eef6b7a" />

# SPARXSTAR™ Gluon - WordPress® Plugin Starter

A comprehensive template repository for building modern WordPress plugins that
follow Starisian Technologies coding conventions and SPARXSTAR deployment standards.

## Features

✨ **Modern PHP Development**

- PHP 8.2+ with PSR-4 autoloading
- WordPress VIP coding standards
- PHPStan static analysis (Level 5)
- PHPCS with WordPress standards
- Rector for automated refactoring
- PHPUnit for unit testing

🎨 **Frontend Tooling**

- ESLint for JavaScript linting
- Stylelint for CSS linting
- Terser for JS minification
- clean-css for CSS minification
- Automated build pipeline (src/ → assets/)

🧪 **Comprehensive Testing**

- PHPUnit for PHP unit tests
- Jest for JavaScript unit tests
- Playwright for E2E testing
- Puppeteer for browser automation
- Accessibility testing with axe-core

🚀 **CI/CD & Automation**

- GitHub Actions workflows
- Automated releases with version bumping
- Security scanning (CodeQL, dependency audits)
- Accessibility validation
- Code quality checks
- Automated asset building

📦 **Release Management**

- Automated versioning from Git tags
- Asset minification and optimization
- i18n POT file generation
- Checksum generation (MD5, SHA256)
- Respects .distignore for clean distributions

## Quick Start

1. **Clone and Install**

```bash
git clone https://github.com/Starisian-Technologies/sparxstar-gluon.git your-plugin-name
cd your-plugin-name
composer install
npm install
```

2. **Build Assets**

```bash
npm run build
```

3. **Run Tests**

```bash
composer run test:php
npm test
```

4. **Activate in WordPress**

- Copy plugin to WordPress plugins directory
- Activate through WordPress admin

## Renaming This Scaffold

Gluon is a **named scaffold** — when building a real plugin, replace all scaffold
identifiers with your plugin's name. Run through these steps after cloning:

| Find (case-sensitive) | Replace with |
| --------------------- | ------------ |
| `sparxstar-gluon` | `your-plugin-slug` |
| `SparxstarGluon` | `YourPluginName` |
| `SPARXSTAR_GLUON` | `YOUR_PLUGIN` |
| `sparxstar_gluon` | `your_plugin` |
| `Gluon` | `YourPlugin` |
| `gluon` | `yourplugin` |
| `SPARXSTAR GLUON` | `YOUR PLUGIN NAME` |

**Files to rename:**

- `sparxstar-gluon.php` → `your-plugin-slug.php`
- `src/SparxstarGluonOrchestrator.php` → `src/YourPluginNameOrchestrator.php`
- All files under `src/` that contain `SparxstarGluon` in their name

Update the plugin header fields (`Plugin Name`, `Text Domain`, `Plugin URI`, etc.)
and the `composer.json` `name` field to match your plugin's identity.

## Documentation

- [Build & Development Guide](docs/BUILD.md) - Detailed build instructions
- [Tooling Configuration](docs/TOOLING.md) - Configure all dev tools
- [Local Development](docs/LOCAL_DEVELOPMENT.md) - Setup local environment
- [First Contribution](docs/FIRST_CONTRIBUTION.md) - Contributing guide

## Development Commands

### PHP

```bash
composer run lint:php          # Check PHP code style
composer run fix:php           # Auto-fix PHP issues
composer run analyze:php       # Run PHPStan analysis
composer run test:php          # Run PHPUnit tests
composer run refactor:php      # Preview Rector changes
composer run refactor:php:fix  # Apply Rector refactoring
```

### JavaScript & CSS

```bash
npm run lint              # Lint JS & CSS
npm run lint:js          # Lint JavaScript only
npm run lint:css         # Lint CSS only
npm run format           # Format with Prettier
npm test                 # Run Jest tests
npm run test:e2e         # Run Playwright E2E tests
```

### Building

```bash
npm run build            # Build all assets
npm run build:js         # Minify JS (src/js → assets/js)
npm run build:css        # Minify CSS (src/css → assets/css)
npm run makepot          # Generate translation file
```

## Project Structure

```
├── src/                      # Source code (scanned by linters)
│   ├── js/                  # JavaScript source files
│   ├── css/                 # CSS source files
│   ├── core/                # Core PHP classes
│   ├── helpers/             # Helper classes
│   ├── includes/            # Autoloader & includes
│   ├── integrations/        # Third-party integrations
│   └── templates/           # Template files
├── assets/                   # Built/minified assets (generated)
│   ├── js/                  # Minified JavaScript
│   └── css/                 # Minified CSS
├── tests/                    # Test files
│   ├── phpunit/             # PHP unit tests
│   └── e2e/                 # End-to-end tests
├── vendor/                   # Composer dependencies (ignored)
├── node_modules/            # npm dependencies (ignored)
├── .github/workflows/       # CI/CD workflows
├── docs/                    # Documentation
└── languages/               # Translation files
```

## Code Quality Standards

This template follows:

- **PSR-4** autoloading
- **PSR-12** coding style (where not conflicting with WordPress)
- **WordPress® VIP** standards (takes precedence over PSR where conflicts exist)
- **WordPress® Coding Standards** for WordPress-specific code
- **Modern PHP practices** (type declarations, readonly properties, etc.)

### Linting Scope

**Important**: Linting and analysis tools only scan:

- Root-level PHP files (e.g., `sparxstar-plugin-entry.php`)
- `src/` directory

Excluded from linting:

- `vendor/` - Third-party PHP dependencies
- `node_modules/` - Third-party JS dependencies
- `assets/` - Generated/minified files
- `tests/` - Test files (separate standards)
- `data/`, `examples/`, `schemas/` - Non-code directories

## Workflows

### Continuous Integration

Runs on every push/PR:

- PHP linting (PHPCS) and analysis (PHPStan)
- JavaScript/CSS linting
- Unit tests (PHP & JS)
- Asset building verification
- Rector refactoring checks

### Release Process

Triggered by pushing a version tag (`v*`):

1. Updates version in all files
2. Installs production dependencies
3. Builds and minifies assets
4. Generates translation files
5. Creates distribution ZIP
6. Generates checksums
7. Creates GitHub release

**To release:**

```bash
git tag -a v1.2.3 -m "Release 1.2.3"
git push origin v1.2.3
```

### Security Scanning

Weekly and on-demand:

- Dependency audits (Composer & npm)
- CodeQL analysis
- Secret scanning
- Security best practices checks

### Accessibility Testing

Automated accessibility validation:

- axe-core integration
- WCAG 2.1 compliance
- HTML validation

## Configuration Files

| File                   | Purpose                        |
| ---------------------- | ------------------------------ |
| `phpcs.xml.dist`       | PHP_CodeSniffer configuration  |
| `phpstan.neon.dist`    | PHPStan static analysis        |
| `phpunit.xml.dist`     | PHPUnit testing                |
| `rector.php`           | Rector refactoring rules       |
| `eslint.config.js`     | ESLint JavaScript linting      |
| `.stylelintrc.json`    | Stylelint CSS linting          |
| `jest.config.js`       | Jest testing configuration     |
| `playwright.config.js` | Playwright E2E testing         |
| `.distignore`          | Files to exclude from releases |
| `composer.json`        | PHP dependencies & scripts     |
| `package.json`         | Node dependencies & scripts    |

## Requirements

- PHP 8.2 or higher
- WordPress 6.8 or higher
- Node.js 20 or higher
- Composer 2.x

## License

This software is provided “as is”, without warranty of any kind,
express or implied, including but not limited to the warranties
of merchantability, fitness for a particular purpose, and non-infringement.

MIT License - see [LICENSE.md](LICENSE.md).

Copyright (c) 2026 Starisian Technologies (Max Barrett).

## Credits

Created and maintained by [Starisian Technologies](https://www.starisian.com)™ .

**Author**: Max Barrett  
**Email**: [support@starisian.com](mailto:support@starisian.com)

SPARXSTAR™ and Starisian Technologies™ are trademarks of Starisian Technologies. WordPress® is a registered trademark of WordPress Inc. Starisian Technologies is in now way affiliated with WordPress.

## Support

- 📖 [Documentation](docs/)
- 🐛 [Issue Tracker](https://github.com/Starisian-Technologies/sparxstar-gluon/issues)
- 💬 [Discussions](https://github.com/Starisian-Technologies/sparxstar-gluon/discussions)
- 📧 Email: support@starisian.com

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for contribution guidelines and [docs/FIRST_CONTRIBUTION.md](docs/FIRST_CONTRIBUTION.md) for first-time contributors.
