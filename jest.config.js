module.exports = {
    testEnvironment: 'node',
    transform: {},
    testPathIgnorePatterns: ['/node_modules/', '/vendor/', '/tests/e2e/'],
    testMatch: [
        '**/tests/__tests__/**/*.test.js',
        '**/tests/__tests__/**/*.spec.js',
        '**/__tests__/**/*.test.js',
    ],
};
