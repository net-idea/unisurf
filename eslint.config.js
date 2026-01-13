import tseslint from '@typescript-eslint/eslint-plugin';
import tsParser from '@typescript-eslint/parser';

export default [
  {
    files: ['**/*.ts'],
    languageOptions: {
      parser: tsParser,
      parserOptions: {
        ecmaVersion: 2020,
        sourceType: 'module',
      },
    },
    plugins: {
      '@typescript-eslint': tseslint,
    },
    rules: {
      'padding-line-between-statements': [
        'error',
        {
          blankLine: 'always',
          prev: '*',
          next: [
            'if',
            'for',
            'while',
            'switch',
            'try',
            'return',
            'break',
            'continue',
            'throw',
            'case',
            'default',
          ],
        },
        {
          blankLine: 'always',
          prev: [
            'if',
            'for',
            'while',
            'switch',
            'try',
            'return',
            'break',
            'continue',
            'throw',
            'case',
            'default',
          ],
          next: '*',
        },
      ],
    },
  },
];
