/**
 * Config file
 * ------------------------------------------------------------------------------------
 * Digunakan untuk mendefinisikan konfigurasi global tema Sneat.
 * Wajib dimuat SEBELUM helpers.js, menu.js, dan main.js.
 */

'use strict';

// Base URL assets
let assetsPath = document.documentElement.getAttribute('data-assets-path');

// Default fallback kalau atribut tidak ada
if (!assetsPath) {
  assetsPath = './';
}

// Global theme config
window.config = {
  colors: {
    primary: '#696cff',
    secondary: '#8592a3',
    success: '#71dd37',
    info: '#03c3ec',
    warning: '#ffab00',
    danger: '#ff3e1d',
    dark: '#233446',
    black: '#000000',
    white: '#ffffff',
    cardBg: '#fff'
  },
  colors_label: {
    primary: '#e7e7ff',
    secondary: '#e2e3e5',
    success: '#d2f8d2',
    info: '#d7f2fa',
    warning: '#ffe8c1',
    danger: '#ffd5cc',
    dark: '#ccced2'
  },
  colors_dark: {
    cardBg: '#2b2c40'
  },
  variables: {
    font: {
      base: '"Public Sans", sans-serif'
    }
  },
  assetsPath: assetsPath
};
