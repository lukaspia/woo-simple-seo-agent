# Woo Simple SEO Agent

![GitHub Release](https://img.shields.io/github/v/release/lukaspia/woo-simple-seo-agent)
![GitHub License](https://img.shields.io/github/license/lukaspia/woo-simple-seo-agent) 
[![WooCommerce](https://img.shields.io/badge/WooCommerce-Compatible-96588A.svg)](https://woocommerce.com/)

> **Note**: This plugin is currently in development.

## Overview

Woo Simple SEO Agent is a powerful WordPress plugin designed to enhance your WooCommerce store's SEO capabilities. It provides an intuitive interface within the WordPress admin panel that helps you generate and manage SEO-optimized content for your products.

## Features

- **AI-Powered SEO Suggestions**: Get intelligent recommendations for product titles, descriptions, and meta tags
- **One-Click Implementation**: Easily apply suggested SEO improvements with a single click
- **Customizable Requests**: Tailor your SEO requests with specific requirements or additional context
- **Conversation History**: Keep track of all your SEO requests and their results
- **Developer Friendly**: Built with modern web technologies including TypeScript and SCSS
- **WooCommerce Integration**: Seamlessly works with your existing WooCommerce products

## Requirements

- WordPress 6.0 or higher
- WooCommerce 7.0 or higher
- PHP 8.1 or higher
- Node.js 16.x or higher (for development)
- npm 8.x or higher (for development)

## Installation

1. Download the latest release from the plugin repository
2. Extract the plugin to the `wp-content/plugins` folder
3. Copy `config.dist.php` to `config.php`
4. Enter the gemini api key into `config.php`, replacing `YOUR_GEMINI_API_KEY_HERE`. Available in [Google AI Studio](https://aistudio.google.com/app/apikey)
5. Follow the Development Setup steps (below, excluding step 1)
6. Go to WordPress admin panel → Plugins → and turn on Woo Simple SEO Agent
7. The plugin will be available under Products → Woo Simple SEO Agent

## Development Setup

1. Clone the repository to your WordPress plugins directory
2. Navigate to the plugin directory: `cd wp-content/plugins/woo-simple-seo-agent`
3. Install js dependencies: `npm install`
4. Build assets: `npm run build`
5. For development with automatic rebuilding: `npm run dev`
6. Install PHP dependencies: bash`composer install`

## Usage

1. Go to any product in your WooCommerce store
2. Find the "Woo Simple SEO Agent" meta box
3. Select the type of SEO assistance you need
4. Add any specific requirements in the text area
5. Click "Send" to generate SEO suggestions
6. Review and apply the suggestions as needed
